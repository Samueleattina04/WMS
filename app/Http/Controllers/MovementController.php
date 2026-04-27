<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Slot;
use App\Services\StockService;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = Movement::with(['product', 'slotFrom.shelf.zone', 'slotTo.shelf.zone', 'createdBy'])
            ->orderByDesc('created_at');

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(30)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('movements.index', compact('movements', 'products'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Movement::class);
        $type = $request->get('type', 'incoming');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $slots = Slot::with('shelf.zone.warehouse')->where('is_active', true)->get();
        return view('movements.create', compact('type', 'products', 'slots'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Movement::class);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:incoming,outgoing,transfer,adjustment,return,damaged',
            'quantity' => 'required|integer|min:1',
            'slot_from_id' => 'required_if:type,outgoing,transfer,return,damaged|nullable|exists:slots,id',
            'slot_to_id' => 'required_if:type,incoming,transfer|nullable|exists:slots,id',
            'lot_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'document_number' => 'nullable|string|max:100',
            'document_type' => 'nullable|in:ddt,order,transfer,adjustment',
            'notes' => 'nullable|string',
        ], [
            'product_id.required' => 'Seleziona un prodotto.',
            'type.required' => 'Seleziona il tipo di movimento.',
            'quantity.required' => 'La quantità è obbligatoria.',
            'quantity.min' => 'La quantità deve essere maggiore di zero.',
            'slot_from_id.required_if' => 'Seleziona lo slot di provenienza.',
            'slot_to_id.required_if' => 'Seleziona lo slot di destinazione.',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $movementData = [
            'document_number' => $validated['document_number'] ?? null,
            'document_type' => $validated['document_type'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by_user_id' => auth()->id(),
        ];

        try {
            match ($validated['type']) {
                'incoming' => $this->stockService->addStock(
                    $product,
                    Slot::findOrFail($validated['slot_to_id']),
                    $validated['quantity'],
                    $validated['lot_number'] ?? null,
                    isset($validated['expiry_date']) ? new \DateTime($validated['expiry_date']) : null,
                    $movementData
                ),
                'outgoing', 'return', 'damaged' => $this->stockService->removeStock(
                    $product,
                    Slot::findOrFail($validated['slot_from_id']),
                    $validated['quantity'],
                    $validated['type'],
                    $validated['lot_number'] ?? null,
                    $movementData
                ),
                'transfer' => $this->stockService->transferStock(
                    $product,
                    Slot::findOrFail($validated['slot_from_id']),
                    Slot::findOrFail($validated['slot_to_id']),
                    $validated['quantity'],
                    $validated['lot_number'] ?? null,
                    $movementData
                ),
                'adjustment' => $this->stockService->adjustStock(
                    $product,
                    Slot::findOrFail($validated['slot_to_id'] ?? $validated['slot_from_id']),
                    $validated['quantity'],
                    $movementData
                ),
            };

            return redirect()->route('movements.index')->with('success', 'Movimento registrato con successo.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function show(Movement $movement)
    {
        $movement->load(['product', 'slotFrom.shelf.zone.warehouse', 'slotTo.shelf.zone.warehouse', 'createdBy']);
        return view('movements.show', compact('movement'));
    }
}
