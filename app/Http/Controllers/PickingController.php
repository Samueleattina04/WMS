<?php

namespace App\Http\Controllers;

use App\Models\PickingList;
use App\Models\PickingListItem;
use App\Models\Slot;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $query = PickingList::with(['salesOrder.customer', 'assignedTo'])->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pickingLists = $query->paginate(20)->withQueryString();
        return view('picking.index', compact('pickingLists'));
    }

    public function show(PickingList $pickingList)
    {
        $pickingList->load(['salesOrder.customer', 'items.product', 'items.slot.shelf.zone', 'assignedTo']);
        return view('picking.show', compact('pickingList'));
    }

    public function start(PickingList $pickingList)
    {
        $this->authorize('update', $pickingList);
        $pickingList->update([
            'status' => 'in_progress',
            'assigned_to_user_id' => auth()->id(),
        ]);
        $pickingList->salesOrder->update(['status' => 'picking']);
        return redirect()->route('picking.show', $pickingList)->with('success', 'Picking list avviata.');
    }

    public function confirmItem(Request $request, PickingList $pickingList, PickingListItem $item)
    {
        $this->authorize('update', $pickingList);

        $request->validate([
            'quantity_picked' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $item, $pickingList) {
            $qty = (int) $request->quantity_picked;
            $item->update([
                'quantity_picked' => $qty,
                'is_completed' => true,
            ]);

            if ($item->slot_id && $qty > 0) {
                $this->stockService->removeStock(
                    $item->product,
                    Slot::findOrFail($item->slot_id),
                    $qty,
                    'outgoing',
                    $item->lot_number,
                    [
                        'document_number' => $pickingList->salesOrder->order_number,
                        'document_type' => 'order',
                        'notes' => "Picking OV #{$pickingList->salesOrder_id}",
                        'created_by_user_id' => auth()->id(),
                    ]
                );
            }

            // Check if all items completed
            $allDone = $pickingList->items()->where('is_completed', false)->doesntExist();
            if ($allDone) {
                $pickingList->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                $pickingList->salesOrder->update(['status' => 'packed']);
            }
        });

        return back()->with('success', 'Articolo confermato.');
    }

    public function complete(PickingList $pickingList)
    {
        $this->authorize('update', $pickingList);
        $pickingList->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $pickingList->salesOrder->update(['status' => 'packed']);
        return redirect()->route('picking.index')->with('success', 'Picking list completata.');
    }
}
