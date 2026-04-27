<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Slot;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'createdBy'])->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $orders = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseOrder::class);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'nullable|string|max:100',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ], [
            'supplier_id.required' => 'Seleziona un fornitore.',
            'items.required' => 'Inserisci almeno un articolo.',
            'items.*.product_id.required' => 'Seleziona il prodotto.',
            'items.*.quantity_ordered.required' => 'Inserisci la quantità ordinata.',
        ]);

        DB::transaction(function () use ($validated) {
            $order = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'order_number' => $validated['order_number'] ?? null,
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'created_by_user_id' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'] ?? 0,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Ordine di acquisto creato con successo.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'createdBy']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);
        $purchaseOrder->load(['items.product']);
        $slots = Slot::with('shelf.zone.warehouse')->where('is_active', true)->get();
        return view('purchase-orders.receive', compact('purchaseOrder', 'slots'));
    }

    public function processReceiving(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.slot_id' => 'nullable|exists:slots,id',
            'items.*.lot_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $allReceived = true;
            $anyReceived = false;

            foreach ($validated['items'] as $itemData) {
                $item = PurchaseOrderItem::findOrFail($itemData['item_id']);
                $qty = (int) $itemData['quantity_received'];

                if ($qty <= 0) {
                    continue;
                }

                $anyReceived = true;
                $item->increment('quantity_received', $qty);

                if ($item->fresh()->quantity_received < $item->quantity_ordered) {
                    $allReceived = false;
                }

                if ($itemData['slot_id']) {
                    $this->stockService->addStock(
                        $item->product,
                        Slot::findOrFail($itemData['slot_id']),
                        $qty,
                        $itemData['lot_number'] ?? null,
                        isset($itemData['expiry_date']) ? new \DateTime($itemData['expiry_date']) : null,
                        [
                            'document_number' => $purchaseOrder->order_number,
                            'document_type' => 'ddt',
                            'notes' => "Ricezione OA #{$purchaseOrder->id}",
                            'created_by_user_id' => auth()->id(),
                        ]
                    );
                }
            }

            if ($anyReceived) {
                $purchaseOrder->update([
                    'status' => $allReceived ? 'received' : 'partial',
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Ricezione registrata con successo.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('delete', $purchaseOrder);
        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Solo gli ordini in attesa possono essere eliminati.');
        }
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Ordine eliminato.');
    }
}
