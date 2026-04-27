<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier', 'unit'])
            ->orderBy('name');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filter === 'low_stock') {
            $query->where('min_stock_alert', '>', 0)->where('is_active', true);
        } elseif ($request->filter === 'inactive') {
            $query->where('is_active', false);
        } else {
            $query->where('is_active', true);
        }

        $products = $query->paginate(25)->withQueryString();

        // Attach total stock to each product
        $productIds = $products->pluck('id');
        $stocks = StockLocation::whereIn('product_id', $productIds)
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        $products->each(function ($product) use ($stocks) {
            $product->stock_total = $stocks->get($product->id, 0);
        });

        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('name')->get();
        return view('products.create', compact('categories', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_id' => 'nullable|exists:units_of_measure,id',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'has_expiry' => 'boolean',
            'has_lot_tracking' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ], $this->italianMessages());

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['has_expiry'] = $request->boolean('has_expiry');
        $validated['has_lot_tracking'] = $request->boolean('has_lot_tracking');

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)->with('success', 'Prodotto creato con successo.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'unit']);
        $stockLocations = StockLocation::where('product_id', $product->id)
            ->with('slot.shelf.zone.warehouse')
            ->orderBy('expiry_date')
            ->get();
        $totalStock = $stockLocations->sum('quantity');
        $recentMovements = $product->movements()
            ->with(['slotFrom', 'slotTo', 'createdBy'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('products.show', compact('product', 'stockLocations', 'totalStock', 'recentMovements'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_id' => 'nullable|exists:units_of_measure,id',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'has_expiry' => 'boolean',
            'has_lot_tracking' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ], $this->italianMessages());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['has_expiry'] = $request->boolean('has_expiry');
        $validated['has_lot_tracking'] = $request->boolean('has_lot_tracking');
        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('products.show', $product)->with('success', 'Prodotto aggiornato con successo.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Prodotto eliminato.');
    }

    private function italianMessages(): array
    {
        return [
            'name.required' => 'Il nome è obbligatorio.',
            'cost_price.numeric' => 'Il prezzo di costo deve essere un numero.',
            'selling_price.numeric' => 'Il prezzo di vendita deve essere un numero.',
            'image.image' => 'Il file deve essere un\'immagine.',
            'image.max' => 'L\'immagine non può superare i 2MB.',
        ];
    }
}
