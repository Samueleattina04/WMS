<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\PickingList;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function ddtEntrata(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'createdBy']);
        $company = app('currentCompany');
        $pdf = Pdf::loadView('documents.ddt-entrata', compact('purchaseOrder', 'company'));
        return $pdf->download("DDT-entrata-{$purchaseOrder->id}.pdf");
    }

    public function pickingListPdf(PickingList $pickingList)
    {
        $pickingList->load(['salesOrder.customer', 'items.product', 'items.slot.shelf.zone', 'assignedTo']);
        $company = app('currentCompany');
        $pdf = Pdf::loadView('documents.picking-list', compact('pickingList', 'company'));
        return $pdf->download("picking-list-{$pickingList->id}.pdf");
    }
}
