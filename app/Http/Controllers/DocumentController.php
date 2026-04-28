<?php

namespace App\Http\Controllers;

use App\Models\PickingList;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function ddtEntrata(PurchaseOrder $purchaseOrder)
    {
        $order = $purchaseOrder->load(['supplier', 'items.product', 'createdBy']);
        $company = app('currentCompany');
        $pdf = Pdf::loadView('documents.ddt-entrata', compact('order', 'company'));
        return $pdf->download("DDT-entrata-{$order->order_number}.pdf");
    }

    public function pickingListPdf(PickingList $pickingList)
    {
        $pickingList->load(['salesOrder.customer', 'items.product', 'items.slot.shelf.zone', 'assignedTo']);
        $company = app('currentCompany');
        $order = $pickingList->salesOrder;

        $pickingItems = $pickingList->items->map(fn ($item) => [
            'product_name' => $item->product?->name ?? '—',
            'sku'          => $item->product?->sku,
            'slot_code'    => $item->slot?->code,
            'lot_number'   => $item->lot_number,
            'expiry_date'  => $item->expiry_date,
            'quantity'     => $item->quantity_requested,
            'fifo'         => true,
        ])->toArray();

        $pdf = Pdf::loadView('documents.picking-list', compact('order', 'company', 'pickingItems'));
        return $pdf->download("picking-list-{$pickingList->id}.pdf");
    }
}
