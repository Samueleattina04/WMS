<x-app-layout>
    <x-slot:title>Nuova Zona</x-slot:title>

    <x-page-header title="Nuova Zona" :subtitle="'Magazzino: ' . $warehouse->name">
        <x-slot:actions>
            <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouses.zones.store', $warehouse) }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Nome zona" name="name" :required="true" :value="old('name')" placeholder="Es. Area A"/>
                        <x-form-input label="Codice" name="code" :required="true" :value="old('code')" placeholder="Es. A"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo zona <span class="text-red-500">*</span></label>
                        <select name="type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Seleziona tipo --</option>
                            <option value="receiving" {{ old('type') === 'receiving' ? 'selected' : '' }}>Ricevimento</option>
                            <option value="storage" {{ old('type') === 'storage' ? 'selected' : '' }}>Stoccaggio</option>
                            <option value="shipping" {{ old('type') === 'shipping' ? 'selected' : '' }}>Spedizione</option>
                            <option value="quality" {{ old('type') === 'quality' ? 'selected' : '' }}>Controllo Qualità</option>
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Crea Zona</x-btn>
                    <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
