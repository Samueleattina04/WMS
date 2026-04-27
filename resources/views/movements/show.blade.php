<x-app-layout>
    <x-slot:title>Movimento #{{ $movement->id }}</x-slot:title>

    <x-page-header title="Dettaglio Movimento" :subtitle="'#' . $movement->id">
        <x-slot:actions>
            <x-btn :href="route('movements.index')" variant="secondary">← Lista Movimenti</x-btn>
        </x-slot:actions>
    </x-page-header>

    @php
        $typeColors = ['incoming'=>'green','outgoing'=>'red','transfer'=>'blue','adjustment'=>'yellow','return'=>'purple','damaged'=>'orange'];
        $typeLabels = ['incoming'=>'Entrata','outgoing'=>'Uscita','transfer'=>'Trasferimento','adjustment'=>'Rettifica','return'=>'Reso','damaged'=>'Danneggiato'];
    @endphp

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <div>
                    <dt class="text-xs text-gray-500">Prodotto</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        <a href="{{ route('products.show', $movement->product) }}" class="text-indigo-600 hover:text-indigo-800">
                            {{ $movement->product->name ?? '—' }}
                        </a>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Tipo</dt>
                    <dd class="mt-1">
                        <x-badge :color="$typeColors[$movement->type] ?? 'gray'" :text="$typeLabels[$movement->type] ?? $movement->type"/>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Quantità</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $movement->quantity }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Operatore</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $movement->operator->name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Slot di provenienza</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900">{{ $movement->slotFrom->full_path ?? $movement->slotFrom->code ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Slot di destinazione</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900">{{ $movement->slotTo->full_path ?? $movement->slotTo->code ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Numero documento</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $movement->document_number ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Tipo documento</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($movement->document_type ?? '—') }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Numero lotto</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $movement->lot_number ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Data scadenza</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $movement->expiry_date ? \Carbon\Carbon::parse($movement->expiry_date)->format('d/m/Y') : '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs text-gray-500">Data/Ora</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $movement->created_at->format('d/m/Y H:i') }}</dd>
                </div>

                @if($movement->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500">Note</dt>
                    <dd class="mt-1 text-sm text-gray-700 bg-gray-50 rounded-md p-3">{{ $movement->notes }}</dd>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
