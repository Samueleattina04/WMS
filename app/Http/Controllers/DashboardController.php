<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index()
    {
        $company = app('currentCompany');
        $kpis = $this->dashboardService->getKpis($company);
        $recentMovements = $this->dashboardService->getRecentMovements($company);
        $movementsTrend = $this->dashboardService->getMovementsTrend($company);
        $activeAlerts = StockAlert::where('is_resolved', false)
            ->with('product')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('kpis', 'recentMovements', 'movementsTrend', 'activeAlerts'));
    }
}
