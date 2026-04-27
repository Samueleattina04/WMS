<x-app-layout>
    <x-slot:title>Magazzini</x-slot:title>

    <x-page-header title="Magazzini" subtitle="Gestisci i tuoi magazzini">
        <x-slot:actions>
            <x-btn :href="route('warehouses.create')" variant="primary">+ Nuovo Magazzino</x-btn>
        </x-slot:actions>
    </x-page-header>

    @if($warehouses->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <p class="text-gray-500 text-sm mb-4">Nessun magazzino configurato.</p>
            <x-btn :href="route('warehouses.create')" variant="primary">Crea il primo magazzino</x-btn>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($warehouses as $warehouse)
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $warehouse->name }}</h3>
                        @if($warehouse->address)
                            <p class="text-xs text-gray-500 mt-1">{{ $warehouse->address }}</p>
                        @endif
                    </div>
                    <div class="flex gap-1">
                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center bg-indigo-50 rounded-lg p-3">
                        <p class="text-xl font-bold text-indigo-700">{{ $warehouse->zones_count ?? $warehouse->zones->count() }}</p>
                        <p class="text-xs text-indigo-500 mt-1">Zone</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <p class="text-xl font-bold text-gray-700">{{ $warehouse->shelves_count ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Scaffali</p>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <p class="text-xl font-bold text-gray-700">{{ $warehouse->slots_count ?? '—' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Slot</p>
                    </div>
                </div>

                @if($warehouse->description)
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $warehouse->description }}</p>
                @endif

                <x-btn :href="route('warehouses.show', $warehouse)" variant="secondary" class="w-full justify-center">
                    Visualizza Mappa
                </x-btn>
            </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
