<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'children'])->withCount('products')->orderBy('name')->paginate(25);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        Category::create($validated);
        return back()->with('success', 'Categoria creata.');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        $category->update($validated);
        return back()->with('success', 'Categoria aggiornata.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        if ($category->children()->exists()) {
            return back()->with('error', 'Non puoi eliminare una categoria con sottocategorie.');
        }
        $category->delete();
        return back()->with('success', 'Categoria eliminata.');
    }
}
