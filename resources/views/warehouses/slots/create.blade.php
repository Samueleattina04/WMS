<x-app-layout>
    <x-slot:title>Nuovo Slot</x-slot:title>

    <x-page-header title="Nuovo Slot" :subtitle="$shelf->code . ' — ' . $zone->name . ' — ' . $warehouse->name">
        <x-slot:actions>
            <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('warehouses.slots.store', [$warehouse, $zone, $shelf]) }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <x-form-input label="Codice slot" name="code" :required="true" :value="old('code')" placeholder="Es. A01-S01-L1-C1"/>
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Livello" name="level" type="number" :value="old('level')" placeholder="Es. 1" min="1"/>
                        <x-form-input label="Colonna" name="column" type="number" :value="old('column')" placeholder="Es. 1" min="1"/>
                    </div>
                    <x-form-input label="Quantità massima" name="max_quantity" type="number" :value="old('max_quantity')" placeholder="Es. 100" min="1"/>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Crea Slot</x-btn>
                    <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
