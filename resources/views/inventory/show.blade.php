<x-app-layout>
    <x-slot:title>Inventario: {{ $inventorySession->name }}</x-slot>
    <x-page-header :title="$inventorySession->name">
        <x-slot:actions>
            @if($inventorySession->status === 'draft')
                <form method="POST" action="{{ route('inventory.start', $inventorySession) }}">
                    @csrf
                    <x-btn type="submit" variant="primary">Avvia Inventario</x-btn>
                </form>
            @elseif($inventorySession->status === 'in_progress')
                <form method="POST" action="{{ route('inventory.complete', $inventorySession) }}" onsubmit="return confirm('Confermi il completamento? Le discrepanze verranno applicate allo stock.')">
                    @csrf
                    <x-btn type="submit" variant="success">Completa e Rettifica Stock</x-btn>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex items-center gap-4">
        <x-badge :color="$inventorySession->status_color" :text="$inventorySession->status_label"/>
        @if($inventorySession->zone) <span class="text-sm text-gray-500">Zona: {{ $inventorySession->zone->name }}</span> @endif
        @if($inventorySession->shelf) <span class="text-sm text-gray-500">Scaffale: {{ $inventorySession->shelf->code }}</span> @endif
    </div>

    @php
        $totalDiscrepancy = $inventorySession->items->whereNotNull('counted_quantity')->sum(fn($i) => abs($i->discrepancy ?? 0));
    @endphp
    @if($totalDiscrepancy > 0)
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
            ⚠️ Discrepanze totali: <strong>{{ $totalDiscrepancy }}</strong> unità
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotto</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Q. Sistema</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Q. Contata</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discrepanza</th>
                    @if($inventorySession->status === 'in_progress')
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azione</th>
                    @endif
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($inventorySession->items as $item)
                        @php $disc = $item->discrepancy ?? 0; @endphp
                        <tr class="{{ $disc != 0 && $item->counted_quantity !== null ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $item->slot?->full_code ?? '–' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->lot_number ?? '–' }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ $item->system_quantity }}</td>
                            <td class="px-4 py-3 text-right text-sm {{ $item->counted_quantity !== null ? 'font-semibold text-gray-900' : 'text-gray-400' }}">
                                {{ $item->counted_quantity ?? '–' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold {{ $disc > 0 ? 'text-green-600' : ($disc < 0 ? 'text-red-600' : 'text-gray-400') }}">
                                {{ $item->counted_quantity !== null ? ($disc > 0 ? '+' : '') . $disc : '–' }}
                            </td>
                            @if($inventorySession->status === 'in_progress')
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('inventory.update-item', [$inventorySession, $item]) }}" class="flex items-center justify-end gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="counted_quantity" value="{{ $item->counted_quantity ?? $item->system_quantity }}" min="0"
                                            class="w-20 text-center rounded-md border-gray-300 shadow-sm text-sm">
                                        <x-btn type="submit" variant="secondary" size="sm">Salva</x-btn>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
