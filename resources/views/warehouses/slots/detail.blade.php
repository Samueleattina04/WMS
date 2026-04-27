<x-app-layout>
    <x-slot:title>Slot {{ $slot->code }}</x-slot:title>

    <x-page-header :title="'Slot: ' . $slot->code" :subtitle="$warehouse->name . ' › ' . $zone->name . ' › ' . $shelf->code">
        <x-slot:actions>
            <x-btn :href="route('movements.create', ['slot_to_id' => $slot->id])" variant="secondary">Registra Entrata</x-btn>
            <x-btn :href="route('warehouses.show', $warehouse)" variant="primary">← Mappa</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Slot Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Dettagli Slot</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Codice</dt>
                        <dd class="text-sm font-mono font-bold text-gray-900">{{ $slot->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Percorso</dt>
                        <dd class="text-sm text-gray-700">{{ $warehouse->name }} › {{ $zone->name }} › {{ $shelf->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Livello / Colonna</dt>
                        <dd class="text-sm text-gray-900">{{ $slot->level ?? '—' }} / {{ $slot->column ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Quantità massima</dt>
                        <dd class="text-sm text-gray-900">{{ $slot->max_quantity ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Quantità attuale</dt>
                        @php $totalQty = $slot->stockLocations->sum('quantity'); @endphp
                        <dd class="mt-1">
                            @if($totalQty <= 0)
                                <x-badge color="green" text="Vuoto (0)"/>
                            @elseif($slot->max_quantity && $totalQty >= $slot->max_quantity)
                                <x-badge color="red" text="Pieno ({{ $totalQty }})"/>
                            @else
                                <x-badge color="yellow" text="Parziale ({{ $totalQty }})"/>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Stock Locations --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">Prodotti nello slot</h2>
                </div>
                @if($slot->stockLocations->isEmpty())
                    <div class="p-12 text-center text-sm text-gray-400">Slot vuoto — nessun prodotto presente.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantità</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scadenza</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($slot->stockLocations as $loc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        <a href="{{ route('products.show', $loc->product) }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ $loc->product->name ?? '—' }}
                                        </a>
                                        <div class="text-xs text-gray-400 font-mono">{{ $loc->product->sku ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $loc->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $loc->lot_number ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($loc->expiry_date)
                                            @php $exp = \Carbon\Carbon::parse($loc->expiry_date); @endphp
                                            <span class="{{ $exp->isPast() ? 'text-red-600 font-medium' : ($exp->diffInDays() < 30 ? 'text-yellow-600' : 'text-gray-600') }}">
                                                {{ $exp->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
