<x-app-layout>
    <x-slot:title>Nuovo Ordine di Acquisto</x-slot>
    <x-page-header title="Nuovo Ordine di Acquisto">
        <x-slot:actions>
            <x-btn href="{{ route('purchase-orders.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('purchase-orders.store') }}" x-data="orderForm()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Articoli</h3>
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-3 mb-3 items-end">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Prodotto</label>
                                <select :name="`items[${index}][product_id]`" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                    <option value="">-- Seleziona --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-28">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Quantità</label>
                                <input type="number" :name="`items[${index}][quantity_ordered]`" min="1" x-model="item.qty" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                            </div>
                            <div class="w-28">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Prezzo (€)</label>
                                <input type="number" :name="`items[${index}][unit_price]`" min="0" step="0.01" x-model="item.price" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 mb-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <x-btn type="button" variant="secondary" size="sm" @click="addItem">+ Aggiungi articolo</x-btn>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Dettagli Ordine</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fornitore <span class="text-red-500">*</span></label>
                            <select name="supplier_id" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                <option value="">-- Seleziona --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-form-input label="N° Ordine" name="order_number" :value="old('order_number')"/>
                        <x-form-input label="Data Attesa Consegna" name="expected_date" type="date" :value="old('expected_date')"/>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                            <textarea name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <x-btn type="submit" variant="primary" class="w-full justify-center">Crea Ordine</x-btn>
            </div>
        </div>
    </form>

    <script>
        function orderForm() {
            return {
                items: [{ qty: 1, price: 0 }],
                addItem() { this.items.push({ qty: 1, price: 0 }); },
                removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); }
            }
        }
    </script>
</x-app-layout>
