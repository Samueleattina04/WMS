<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovementsExport;
use App\Exports\StockValueExport;

class ReportController extends Controller
{
    public function movements(Request $request)
    {
        $query = Movement::with(['product', 'slotFrom', 'slotTo', 'createdBy'])
            ->orderByDesc('created_at');

        if ($request->product_id) $query->where('product_id', $request->product_id);
        if ($request->type) $query->where('type', $request->type);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->user_id) $query->where('created_by_user_id', $request->user_id);

        $movements = $query->paginate(50)->withQueryString();
        $products = Product::orderBy('name')->get();

        if ($request->export === 'excel') {
            return Excel::download(new MovementsExport($query->get()), 'movimenti_' . now()->format('Y-m-d') . '.xlsx');
        }

        return view('reports.movements', compact('movements', 'products'));
    }

    public function stockValue(Request $request)
    {
        $company = app('currentCompany');

        $stockData = StockLocation::join('products', 'stock_locations.product_id', '=', 'products.id')
            ->join('slots', 'stock_locations.slot_id', '=', 'slots.id')
            ->join('shelves', 'slots.shelf_id', '=', 'shelves.id')
            ->join('zones', 'shelves.zone_id', '=', 'zones.id')
            ->join('warehouses', 'zones.warehouse_id', '=', 'warehouses.id')
            ->select([
                'products.id', 'products.name', 'products.sku',
                'products.cost_price', 'products.selling_price',
                'warehouses.name as warehouse_name',
                'zones.name as zone_name',
                DB::raw('SUM(stock_locations.quantity) as total_quantity'),
                DB::raw('SUM(stock_locations.quantity * products.cost_price) as total_value'),
            ])
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.cost_price',
                'products.selling_price', 'warehouses.name', 'zones.name')
            ->orderBy('products.name')
            ->paginate(50)->withQueryString();

        $totalValue = StockLocation::join('products', 'stock_locations.product_id', '=', 'products.id')
            ->sum(DB::raw('stock_locations.quantity * products.cost_price'));

        if ($request->export === 'excel') {
            return Excel::download(new StockValueExport($company), 'valore_magazzino_' . now()->format('Y-m-d') . '.xlsx');
        }

        return view('reports.stock-value', compact('stockData', 'totalValue'));
    }

    public function purchaseOrders(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items', 'createdBy'])->orderByDesc('created_at');
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);

        $orders = $query->paginate(30)->withQueryString();
        return view('reports.purchase-orders', compact('orders'));
    }

    public function salesOrders(Request $request)
    {
        $query = SalesOrder::with(['customer', 'items', 'createdBy'])->orderByDesc('created_at');
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);

        $orders = $query->paginate(30)->withQueryString();
        return view('reports.sales-orders', compact('orders'));
    }
}
