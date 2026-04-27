<x-app-layout>
    <x-slot:title>Nuova Importazione</x-slot>
    <x-page-header title="Nuova Importazione Excel">
        <x-slot:actions>
            <x-btn href="{{ route('imports.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Importazione <span class="text-red-500">*</span></label>
                    <select name="type" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Seleziona tipo —</option>
                        <option value="products" @selected(request('type')=='products' || old('type')=='products')>Prodotti</option>
                        <option value="stock" @selected(request('type')=='stock' || old('type')=='stock')>Giacenze Iniziali</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Excel <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-400">Formati accettati: .xlsx, .xls, .csv (max 5MB)</p>
                    @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Colonne richieste per Prodotti:</h4>
                    <div class="grid grid-cols-2 gap-1 text-xs text-blue-700">
                        <span>• <code>nome</code> (obbligatorio)</span>
                        <span>• <code>sku</code> (obbligatorio)</span>
                        <span>• <code>barcode</code></span>
                        <span>• <code>categoria</code></span>
                        <span>• <code>fornitore</code></span>
                        <span>• <code>unita_misura</code></span>
                        <span>• <code>prezzo_costo</code></span>
                        <span>• <code>prezzo_vendita</code></span>
                        <span>• <code>soglia_minima</code></span>
                        <span>• <code>descrizione</code></span>
                    </div>
                    <a href="{{ route('imports.template', 'products') }}" class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-800 font-medium underline">Scarica template</a>
                </div>

                <div class="flex gap-3 pt-1">
                    <x-btn href="{{ route('imports.index') }}" variant="secondary">Annulla</x-btn>
                    <x-btn type="submit" variant="primary">Avvia Importazione</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
