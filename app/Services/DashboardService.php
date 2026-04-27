<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Movement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\StockAlert;
use App\Models\StockLocation;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getKpis(Company $company): array
    {
        $companyId = $company->id;

        $warehouseValue = StockLocation::where('company_id', $companyId)
            ->join('products', 'stock_locations.product_id', '=', 'products.id')
            ->sum(DB::raw('stock_locations.quantity * products.cost_price'));

        $dailyMovements = Movement::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->count();

        $activeAlerts = StockAlert::where('company_id', $companyId)
            ->where('is_resolved', false)
            ->count();

        $lowStockProducts = Product::where('company_id', $companyId)
            ->where('min_stock_alert', '>', 0)
            ->where('is_active', true)
            ->whereHas('stockLocations', function ($q) {
                $q->select(DB::raw('SUM(quantity)'));
            })
            ->get()
            ->filter(fn ($p) => $p->total_stock <= $p->min_stock_alert)
            ->count();

        $openPurchaseOrders = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'partial'])
            ->count();

        $openSalesOrders = SalesOrder::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'picking', 'packed'])
            ->count();

        return compact(
            'warehouseValue',
            'dailyMovements',
            'activeAlerts',
            'lowStockProducts',
            'openPurchaseOrders',
            'openSalesOrders'
        );
    }

    public function getRecentMovements(Company $company, int $limit = 10): \Illuminate\Support\Collection
    {
        return Movement::where('company_id', $company->id)
            ->with(['product', 'slotFrom', 'slotTo', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getMovementsTrend(Company $company, int $days = 7): array
    {
        $data = Movement::where('company_id', $company->id)
            ->whereDate('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, type, COUNT(*) as count')
            ->groupBy('date', 'type')
            ->get();

        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $data->where('date', $date);
            $result[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'incoming' => $dayData->where('type', 'incoming')->sum('count'),
                'outgoing' => $dayData->where('type', 'outgoing')->sum('count'),
            ];
        }

        return $result;
    }
}
