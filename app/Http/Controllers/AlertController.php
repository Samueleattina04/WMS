<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAlert::with('product')->orderByDesc('created_at');

        if ($request->type) {
            $query->where('alert_type', $request->type);
        }

        if ($request->resolved === '1') {
            $query->where('is_resolved', true);
        } else {
            $query->where('is_resolved', false);
        }

        $alerts = $query->paginate(30)->withQueryString();
        return view('alerts.index', compact('alerts'));
    }

    public function resolve(StockAlert $alert)
    {
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'Alert risolto.');
    }

    public function resolveAll()
    {
        StockAlert::where('is_resolved', false)->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'Tutti gli alert sono stati risolti.');
    }
}
