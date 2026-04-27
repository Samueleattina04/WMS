<?php

namespace App\Http\Controllers;

use App\Models\Shelf;
use App\Models\Slot;
use App\Models\StockLocation;
use App\Models\Warehouse;
use App\Models\Zone;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['zones.shelves.slots'])->where('is_active', true)->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $this->authorize('create', Warehouse::class);
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Warehouse::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        $warehouse = Warehouse::create($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Magazzino creato con successo.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['zones.shelves.slots.stockLocations.product']);
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $warehouse->update($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Magazzino aggiornato.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('delete', $warehouse);
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Magazzino eliminato.');
    }

    // Zones
    public function createZone(Warehouse $warehouse)
    {
        $this->authorize('create', Zone::class);
        return view('warehouses.zones.create', compact('warehouse'));
    }

    public function storeZone(Request $request, Warehouse $warehouse)
    {
        $this->authorize('create', Zone::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:receiving,storage,shipping,quality',
            'description' => 'nullable|string',
        ]);
        $validated['warehouse_id'] = $warehouse->id;
        Zone::create($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Zona creata con successo.');
    }

    public function editZone(Warehouse $warehouse, Zone $zone)
    {
        $this->authorize('update', $zone);
        return view('warehouses.zones.edit', compact('warehouse', 'zone'));
    }

    public function updateZone(Request $request, Warehouse $warehouse, Zone $zone)
    {
        $this->authorize('update', $zone);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:receiving,storage,shipping,quality',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $zone->update($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Zona aggiornata.');
    }

    // Shelves
    public function createShelf(Warehouse $warehouse, Zone $zone)
    {
        $this->authorize('create', Shelf::class);
        return view('warehouses.shelves.create', compact('warehouse', 'zone'));
    }

    public function storeShelf(Request $request, Warehouse $warehouse, Zone $zone)
    {
        $this->authorize('create', Shelf::class);
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_weight' => 'nullable|numeric|min:0',
            'max_volume' => 'nullable|numeric|min:0',
        ]);
        $validated['zone_id'] = $zone->id;
        Shelf::create($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Scaffale creato con successo.');
    }

    // Slots
    public function createSlot(Warehouse $warehouse, Zone $zone, Shelf $shelf)
    {
        $this->authorize('create', Slot::class);
        return view('warehouses.slots.create', compact('warehouse', 'zone', 'shelf'));
    }

    public function storeSlot(Request $request, Warehouse $warehouse, Zone $zone, Shelf $shelf)
    {
        $this->authorize('create', Slot::class);
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'level' => 'nullable|string|max:10',
            'column' => 'nullable|string|max:10',
            'max_quantity' => 'nullable|integer|min:1',
        ]);
        $validated['shelf_id'] = $shelf->id;
        Slot::create($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Slot creato con successo.');
    }

    public function slotDetail(Slot $slot)
    {
        $slot->load(['shelf.zone.warehouse']);
        $stockLocations = StockLocation::where('slot_id', $slot->id)->with('product')->get();
        return view('warehouses.slots.detail', compact('slot', 'stockLocations'));
    }
}
