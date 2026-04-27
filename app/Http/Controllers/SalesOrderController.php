<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PickingList;
use App\Models\PickingListItem;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\StockLocation;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = SalesOrder::with(['customer', 'createdBy', 'pickingList'])->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $orders = $query->paginate(20)->withQueryString();
        $customers = Customer::orderBy('name')->get();

        return view('sales-orders.index', compact('orders', 'customers'));
    }

    public function create()
    {
        $this->authorize('create', SalesOrder::class);
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('sales-orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SalesOrder::class);

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'order_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
        ], [
            'items.required' => 'Inserisci almeno un articolo.',
            'items.*.product_id.required' => 'Seleziona il prodotto.',
            'items.*.quantity_requested.required' => 'Inserisci la quantità.',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = SalesOrder::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'order_number' => $validated['order_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'created_by_user_id' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                ]);
            }

            return $order;
        });

        return redirect()->route('sales-orders.show', $order)->with('success', 'Ordine di vendita creato con successo.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'items.product', 'pickingList.items', 'createdBy']);
        return view('sales-orders.show', compact('salesOrder'));
    }

    public function generatePickingList(SalesOrder $salesOrder)
    {
        $this->authorize('update', $salesOrder);

        if ($salesOrder->pickingList) {
            return back()->with('error', 'Esiste già una picking list per questo ordine.');
        }

        DB::transaction(function () use ($salesOrder) {
            $pickingList = PickingList::create([
                'sales_order_id' => $salesOrder->id,
                'status' => 'pending',
            ]);

            foreach ($salesOrder->items as $item) {
                $suggestions = $this->stockService->getSuggestedSlotsFifo($item->product, $item->quantity_requested);
                $remainingQty = $item->quantity_requested;

                foreach ($suggestions as $suggestion) {
                    if ($remainingQty <= 0) break;
                    $takeQty = min($remainingQty, $suggestion['available']);
                    PickingListItem::create([
                        'picking_list_id' => $pickingList->id,
                        'product_id' => $item->product_id,
                        'slot_id' => $suggestion['slot']->id,
                        'quantity_required' => $takeQty,
                        'lot_number' => $suggestion['lot_number'],
                    ]);
                    $remainingQty -= $takeQty;
                }

                // If still remaining (insufficient stock), add an unassigned item
                if ($remainingQty > 0) {
                    PickingListItem::create([
                        'picking_list_id' => $pickingList->id,
                        'product_id' => $item->product_id,
                        'slot_id' => null,
                        'quantity_required' => $remainingQty,
                    ]);
                }
            }

            $salesOrder->update(['status' => 'picking']);
        });

        return redirect()->route('picking.show', $salesOrder->pickingList)->with('success', 'Picking list generata con successo.');
    }

    public function updateStatus(Request $request, SalesOrder $salesOrder)
    {
        $this->authorize('update', $salesOrder);
        $request->validate(['status' => 'required|in:pending,picking,packed,shipped,cancelled']);
        $salesOrder->update(['status' => $request->status]);
        return back()->with('success', 'Stato ordine aggiornato.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $this->authorize('delete', $salesOrder);
        if (in_array($salesOrder->status, ['picking', 'packed', 'shipped'])) {
            return back()->with('error', 'Non è possibile eliminare un ordine in lavorazione.');
        }
        $salesOrder->delete();
        return redirect()->route('sales-orders.index')->with('success', 'Ordine eliminato.');
    }
}
