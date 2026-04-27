<x-app-layout>
    <x-slot:title>{{ $product->name }}</x-slot:title>

    <x-page-header :title="$product->name" :subtitle="'SKU: ' . $product->sku">
        <x-slot:actions>
            <x-btn :href="route('products.edit', $product)" variant="secondary">Modifica</x-btn>
            <x-btn :href="route('movements.create', ['product_id' => $product->id])" variant="primary">Registra Movimento</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Product Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Dettagli Prodotto</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Nome</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">SKU</dt>
                        <dd class="text-sm font-mono text-gray-900">{{ $product->sku }}</dd>
                    </div>
                    @if($product->barcode)
                    <div>
                        <dt class="text-xs text-gray-500">Barcode</dt>
                        <dd class="text-sm font-mono text-gray-900">{{ $product->barcode }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500">Categoria</dt>
                        <dd class="text-sm text-gray-900">{{ $product->category->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Fornitore</dt>
                        <dd class="text-sm text-gray-900">{{ $product->supplier->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Unità di misura</dt>
                        <dd class="text-sm text-gray-900">{{ $product->unit->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Scorta minima</dt>
                        <dd class="text-sm text-gray-900">{{ $product->min_stock ?? 0 }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Costo unitario</dt>
                        <dd class="text-sm text-gray-900">€ {{ number_format($product->unit_cost ?? 0, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Stato scorte</dt>
                        <dd class="mt-1">
                            @php
                                $totalQty = $product->stockLocations->sum('quantity');
                                $minStock = $product->min_stock ?? 0;
                            @endphp
                            @if($totalQty <= 0)
                                <x-badge color="red" text="Esaurito" />
                            @elseif($minStock > 0 && $totalQty <= $minStock)
                                <x-badge color="yellow" text="Scorta bassa ({{ $totalQty }})" />
                            @else
                                <x-badge color="green" text="Disponibile ({{ $totalQty }})" />
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Stock Locations + Movements --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Stock Locations --}}
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Ubicazioni in magazzino</h2>
                </div>
                <div class="overflow-x-auto">
                    @if($product->stockLocations->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500">Nessuna ubicazione registrata.</div>
                    @else
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slot</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantità</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scadenza</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($product->stockLocations as $loc)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $loc->slot->full_path ?? $loc->slot->code ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $loc->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $loc->lot_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($loc->expiry_date)
                                        @php $exp = \Carbon\Carbon::parse($loc->expiry_date); @endphp
                                        <span class="{{ $exp->isPast() ? 'text-red-600 font-medium' : ($exp->diffInDays() < 30 ? 'text-yellow-600' : '') }}">
                                            {{ $exp->format('d/m/Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            {{-- Recent Movements --}}
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Movimenti recenti</h2>
                    <x-btn :href="route('movements.index', ['product_id' => $product->id])" variant="secondary" size="sm">Vedi tutti</x-btn>
                </div>
                <div class="overflow-x-auto">
                    @if($recentMovements->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500">Nessun movimento registrato.</div>
                    @else
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantità</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Da</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">A</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operatore</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentMovements as $mov)
                            @php
                                $typeColors = ['incoming'=>'green','outgoing'=>'red','transfer'=>'blue','adjustment'=>'yellow','return'=>'purple','damaged'=>'orange'];
                                $typeLabels = ['incoming'=>'Entrata','outgoing'=>'Uscita','transfer'=>'Trasferimento','adjustment'=>'Rettifica','return'=>'Reso','damaged'=>'Danneggiato'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <x-badge :color="$typeColors[$mov->type] ?? 'gray'" :text="$typeLabels[$mov->type] ?? $mov->type" />
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $mov->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->slotFrom->code ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->slotTo->code ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->operator->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
