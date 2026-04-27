<x-app-layout>
    <x-slot:title>Nuova Sessione Inventario</x-slot>
    <x-page-header title="Nuova Sessione Inventario">
        <x-slot:actions>
            <x-btn href="{{ route('inventory.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('inventory.store') }}" class="max-w-lg">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome sessione" name="name" :required="true" :value="old('name')" help="Es. Inventario Q4 2024 - Zona B"/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zona (opzionale)</label>
                <select name="zone_id" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">-- Tutte le zone --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" @selected(old('zone_id')==$zone->id)>{{ $zone->warehouse->name ?? '' }} › {{ $zone->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Lascia vuoto per inventario totale</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Scaffale (opzionale)</label>
                <select name="shelf_id" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">-- Tutti gli scaffali --</option>
                    @foreach($shelves as $shelf)
                        <option value="{{ $shelf->id }}" @selected(old('shelf_id')==$shelf->id)>{{ $shelf->zone->name }} › {{ $shelf->code }} - {{ $shelf->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg text-sm text-blue-700">
                <strong>Nota:</strong> La sessione verrà pre-popolata con tutti i prodotti presenti nelle posizioni selezionate con le quantità attuali di sistema.
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <x-btn href="{{ route('inventory.index') }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Crea Sessione</x-btn>
        </div>
    </form>
</x-app-layout>
