<x-app-layout>
    <x-slot:title>Prodotti</x-slot>

    <x-page-header title="Prodotti" subtitle="Gestione catalogo prodotti">
        <x-slot:actions>
            @can('create', App\Models\Product::class)
                <x-btn href="{{ route('products.create') }}" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuovo Prodotto
                </x-btn>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cerca per nome, SKU, barcode..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <select name="category_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutte le categorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="supplier_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti i fornitori</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <select name="filter" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Attivi</option>
                <option value="low_stock" @selected(request('filter') == 'low_stock')>Scorta Bassa</option>
                <option value="inactive" @selected(request('filter') == 'inactive')>Inattivi</option>
            </select>
            <x-btn type="submit" variant="secondary">Cerca</x-btn>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodotto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU / Barcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        @php $isLow = $product->min_stock_alert > 0 && $product->stock_total <= $product->min_stock_alert; @endphp
                        <tr class="hover:bg-gray-50 {{ $isLow ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="w-10 h-10 rounded object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded mr-3 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('products.show', $product) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $product->name }}
                                        </a>
                                        @if($product->has_expiry)
                                            <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Scadenza</span>
                                        @endif
                                        @if($product->has_lot_tracking)
                                            <span class="ml-1 text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">Lotti</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($product->sku) <div>{{ $product->sku }}</div> @endif
                                @if($product->barcode) <div class="font-mono text-xs">{{ $product->barcode }}</div> @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold {{ $isLow ? 'text-red-600' : ($product->stock_total === 0 ? 'text-gray-400' : 'text-gray-900') }}">
                                    {{ number_format($product->stock_total) }}
                                </span>
                                @if($isLow)
                                    <span class="ml-1 text-xs text-yellow-600">(min: {{ $product->min_stock_alert }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-700">
                                € {{ number_format($product->cost_price, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->is_active)
                                    <x-badge color="green" text="Attivo"/>
                                @else
                                    <x-badge color="gray" text="Inattivo"/>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Dettaglio</a>
                                    @can('update', $product)
                                        <a href="{{ route('products.edit', $product) }}" class="text-gray-600 hover:text-gray-800 text-sm">Modifica</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Nessun prodotto trovato
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
