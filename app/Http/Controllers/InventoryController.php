<?php

namespace App\Http\Controllers;

use App\Models\InventorySession;
use App\Models\InventorySessionItem;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\StockLocation;
use App\Models\Zone;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index()
    {
        $sessions = InventorySession::with(['createdBy', 'zone', 'shelf'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('inventory.index', compact('sessions'));
    }

    public function create()
    {
        $this->authorize('create', InventorySession::class);
        $zones = Zone::with('shelves')->orderBy('name')->get();
        $shelves = Shelf::with('zone')->orderBy('name')->get();
        return view('inventory.create', compact('zones', 'shelves'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', InventorySession::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_id' => 'nullable|exists:zones,id',
            'shelf_id' => 'nullable|exists:shelves,id',
        ]);

        $session = DB::transaction(function () use ($validated) {
            $session = InventorySession::create([
                'name' => $validated['name'],
                'zone_id' => $validated['zone_id'] ?? null,
                'shelf_id' => $validated['shelf_id'] ?? null,
                'status' => 'draft',
                'created_by_user_id' => auth()->id(),
            ]);

            // Pre-populate items based on scope
            $stockQuery = StockLocation::with(['product', 'slot']);

            if ($validated['shelf_id']) {
                $stockQuery->whereHas('slot', fn ($q) => $q->where('shelf_id', $validated['shelf_id']));
            } elseif ($validated['zone_id']) {
                $stockQuery->whereHas('slot.shelf', fn ($q) => $q->where('zone_id', $validated['zone_id']));
            }

            $stockLocations = $stockQuery->get();

            foreach ($stockLocations as $sl) {
                InventorySessionItem::create([
                    'inventory_session_id' => $session->id,
                    'product_id' => $sl->product_id,
                    'slot_id' => $sl->slot_id,
                    'system_quantity' => $sl->quantity,
                    'lot_number' => $sl->lot_number,
                ]);
            }

            return $session;
        });

        return redirect()->route('inventory.show', $session)->with('success', 'Sessione inventario creata.');
    }

    public function show(InventorySession $inventorySession)
    {
        $inventorySession->load(['items.product', 'items.slot.shelf.zone', 'createdBy', 'zone', 'shelf']);
        return view('inventory.show', compact('inventorySession'));
    }

    public function start(InventorySession $inventorySession)
    {
        $this->authorize('update', $inventorySession);
        $inventorySession->update(['status' => 'in_progress', 'started_at' => now()]);
        return back()->with('success', 'Sessione avviata.');
    }

    public function updateItem(Request $request, InventorySession $inventorySession, InventorySessionItem $item)
    {
        $this->authorize('update', $inventorySession);

        $request->validate([
            'counted_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $countedQty = (int) $request->counted_quantity;
        $discrepancy = $countedQty - $item->system_quantity;

        $item->update([
            'counted_quantity' => $countedQty,
            'discrepancy' => $discrepancy,
            'notes' => $request->notes,
            'counted_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Quantità aggiornata.');
    }

    public function complete(InventorySession $inventorySession)
    {
        $this->authorize('update', $inventorySession);

        DB::transaction(function () use ($inventorySession) {
            foreach ($inventorySession->items as $item) {
                if ($item->counted_quantity !== null && $item->discrepancy !== 0 && $item->slot_id) {
                    $this->stockService->adjustStock(
                        $item->product,
                        $item->slot,
                        $item->counted_quantity,
                        [
                            'notes' => "Rettifica inventario #{$inventorySession->id}",
                            'created_by_user_id' => auth()->id(),
                        ]
                    );
                }
            }

            $inventorySession->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Inventario completato e stock rettificato.');
    }
}
