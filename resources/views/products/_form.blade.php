<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Informazioni Base</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-form-input label="Nome Prodotto" name="name" :required="true" :value="old('name', $product->name ?? '')"/>
                </div>
                <x-form-input label="SKU" name="sku" :value="old('sku', $product->sku ?? '')" help="Codice interno univoco"/>
                <x-form-input label="Barcode" name="barcode" :value="old('barcode', $product->barcode ?? '')" help="EAN, UPC, ecc."/>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="category_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Nessuna --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fornitore Principale</label>
                    <select name="supplier_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Nessuno --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $sup->id)>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unità di Misura</label>
                    <select name="unit_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Nessuna --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $unit->id)>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                    <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Prezzi e Stock</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prezzo di Costo (€)</label>
                    <input type="number" name="cost_price" step="0.01" min="0"
                        value="{{ old('cost_price', $product->cost_price ?? '0') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prezzo di Vendita (€)</label>
                    <input type="number" name="selling_price" step="0.01" min="0"
                        value="{{ old('selling_price', $product->selling_price ?? '0') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Soglia Scorta Minima</label>
                    <input type="number" name="min_stock_alert" min="0"
                        value="{{ old('min_stock_alert', $product->min_stock_alert ?? '0') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Alert quando lo stock scende sotto questa soglia</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantità Riordino</label>
                    <input type="number" name="reorder_quantity" min="0"
                        value="{{ old('reorder_quantity', $product->reorder_quantity ?? '0') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Note</h3>
            <textarea name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $product->notes ?? '') }}</textarea>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Opzioni</h3>
            <div class="space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="has_lot_tracking" value="1" class="rounded border-gray-300 text-indigo-600"
                        @checked(old('has_lot_tracking', $product->has_lot_tracking ?? false))>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Gestione Lotti</span>
                        <p class="text-xs text-gray-500">Traccia numero di lotto</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="has_expiry" value="1" class="rounded border-gray-300 text-indigo-600"
                        @checked(old('has_expiry', $product->has_expiry ?? false))>
                    <div>
                        <span class="text-sm font-medium text-gray-700">Gestione Scadenze</span>
                        <p class="text-xs text-gray-500">Traccia data di scadenza</p>
                    </div>
                </label>
                @if(isset($product))
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600"
                            @checked(old('is_active', $product->is_active ?? true))>
                        <span class="text-sm font-medium text-gray-700">Prodotto Attivo</span>
                    </label>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Immagine</h3>
            @if(isset($product) && $product->image)
                <img src="{{ Storage::url($product->image) }}" class="w-full rounded-lg object-cover mb-3 max-h-48">
            @endif
            <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium
                file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Dimensioni e Peso</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                    <input type="number" name="weight" step="0.001" min="0"
                        value="{{ old('weight', $product->weight ?? '') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>
    </div>
</div>
