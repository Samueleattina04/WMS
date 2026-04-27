<x-app-layout>
    <x-slot:title>Nuovo Magazzino</x-slot:title>

    <x-page-header title="Nuovo Magazzino" subtitle="Aggiungi un magazzino al sistema">
        <x-slot:actions>
            <x-btn :href="route('warehouses.index')" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouses.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <x-form-input label="Nome magazzino" name="name" :required="true" :value="old('name')" placeholder="Es. Magazzino Centrale"/>
                    <x-form-input label="Indirizzo" name="address" :value="old('address')" placeholder="Via Roma 1, 20100 Milano"/>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                        <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Descrizione opzionale...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Crea Magazzino</x-btn>
                    <x-btn :href="route('warehouses.index')" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
