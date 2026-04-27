<?php

namespace App\Http\Controllers;

use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;

class UnitOfMeasureController extends Controller
{
    public function index()
    {
        $units = UnitOfMeasure::withCount('products')->orderBy('name')->paginate(25);
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', UnitOfMeasure::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:20',
            'type' => 'required|in:quantity,weight,volume,length',
        ]);
        UnitOfMeasure::create($validated);
        return back()->with('success', 'Unità di misura creata.');
    }

    public function update(Request $request, UnitOfMeasure $unitOfMeasure)
    {
        $this->authorize('update', $unitOfMeasure);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:20',
            'type' => 'required|in:quantity,weight,volume,length',
        ]);
        $unitOfMeasure->update($validated);
        return back()->with('success', 'Unità aggiornata.');
    }

    public function destroy(UnitOfMeasure $unitOfMeasure)
    {
        $this->authorize('delete', $unitOfMeasure);
        $unitOfMeasure->delete();
        return back()->with('success', 'Unità eliminata.');
    }
}
