<x-app-layout>
    <x-slot:title>{{ $warehouse->name }}</x-slot:title>

    <x-page-header :title="$warehouse->name" :subtitle="$warehouse->address ?? 'Mappa del magazzino'">
        <x-slot:actions>
            <x-btn :href="route('warehouses.zones.create', $warehouse)" variant="secondary">+ Zona</x-btn>
            <x-btn :href="route('warehouses.edit', $warehouse)" variant="primary">Modifica</x-btn>
        </x-slot:actions>
    </x-page-header>

    @if($warehouse->zones->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <p class="text-gray-500 text-sm mb-4">Nessuna zona configurata in questo magazzino.</p>
            <x-btn :href="route('warehouses.zones.create', $warehouse)" variant="primary">Aggiungi Zona</x-btn>
        </div>
    @else
        <div class="space-y-6">
            @foreach($warehouse->zones as $zone)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                {{-- Zone Header --}}
                <div class="px-6 py-4 bg-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-mono font-bold bg-gray-700 text-gray-200">
                            {{ $zone->code }}
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-white">{{ $zone->name }}</h3>
                            @if($zone->type)
                                <span class="text-xs text-gray-400">{{ ucfirst($zone->type) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-btn :href="route('warehouses.shelves.create', [$warehouse, $zone])" variant="secondary" size="sm">+ Scaffale</x-btn>
                        <x-btn :href="route('warehouses.zones.edit', [$warehouse, $zone])" variant="secondary" size="sm">Modifica</x-btn>
                    </div>
                </div>

                <div class="p-6">
                    @if($zone->shelves->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">Nessuno scaffale in questa zona.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($zone->shelves as $shelf)
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                            {{ $shelf->code }}
                                        </span>
                                        @if($shelf->name)
                                            <span class="text-sm text-gray-700 font-medium">{{ $shelf->name }}</span>
                                        @endif
                                    </div>
                                    <x-btn :href="route('warehouses.slots.create', [$warehouse, $zone, $shelf])" variant="secondary" size="sm">+ Slot</x-btn>
                                </div>

                                {{-- Slot Grid --}}
                                @if($shelf->slots->isEmpty())
                                    <p class="text-xs text-gray-400 italic pl-2">Nessuno slot configurato.</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($shelf->slots as $slot)
                                        @php
                                            $qty = $slot->stockLocations->sum('quantity');
                                            $maxQty = $slot->max_quantity;
                                            if ($qty <= 0) {
                                                $slotColor = 'bg-green-100 border-green-300 text-green-700 hover:bg-green-200';
                                                $slotLabel = 'Vuoto';
                                            } elseif ($maxQty && $qty >= $maxQty) {
                                                $slotColor = 'bg-red-100 border-red-300 text-red-700 hover:bg-red-200';
                                                $slotLabel = 'Pieno';
                                            } else {
                                                $slotColor = 'bg-yellow-100 border-yellow-300 text-yellow-700 hover:bg-yellow-200';
                                                $slotLabel = 'Parziale';
                                            }
                                        @endphp
                                        <a href="{{ route('slots.show', $slot) }}"
                                           class="flex flex-col items-center justify-center w-16 h-16 rounded-lg border-2 cursor-pointer transition-colors {{ $slotColor }}"
                                           title="{{ $slot->code }} — {{ $slotLabel }} ({{ $qty }})">
                                            <span class="text-xs font-mono font-bold leading-tight">{{ $slot->code }}</span>
                                            <span class="text-xs leading-tight">{{ $qty }}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Legend --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm p-4 flex items-center gap-6">
        <span class="text-xs text-gray-500 font-medium">Legenda:</span>
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-green-100 border-2 border-green-300"></div>
            <span class="text-xs text-gray-600">Vuoto</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-yellow-100 border-2 border-yellow-300"></div>
            <span class="text-xs text-gray-600">Parziale</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-red-100 border-2 border-red-300"></div>
            <span class="text-xs text-gray-600">Pieno</span>
        </div>
    </div>
</x-app-layout>
