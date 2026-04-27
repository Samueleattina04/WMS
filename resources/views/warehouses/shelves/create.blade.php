<x-app-layout>
    <x-slot:title>Nuovo Scaffale</x-slot:title>

    <x-page-header title="Nuovo Scaffale" :subtitle="$zone->name . ' — ' . $warehouse->name">
        <x-slot:actions>
            <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouses.shelves.store', [$warehouse, $zone]) }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Codice scaffale" name="code" :required="true" :value="old('code')" placeholder="Es. S01"/>
                        <x-form-input label="Nome" name="name" :value="old('name')" placeholder="Es. Scaffale 1"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Peso massimo (kg)" name="max_weight" type="number" :value="old('max_weight')" placeholder="Es. 500" min="0" step="0.01"/>
                        <x-form-input label="Volume massimo (m³)" name="max_volume" type="number" :value="old('max_volume')" placeholder="Es. 2.5" min="0" step="0.001"/>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Crea Scaffale</x-btn>
                    <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
