<x-app-layout>
    <x-slot:title>Picking #{{ $pickingList->id }}</x-slot>
    <x-page-header title="Picking List #{{ $pickingList->id }}" :subtitle="'OV: '.($pickingList->salesOrder->order_number ?? '#'.$pickingList->sales_order_id).' — '.($pickingList->salesOrder->customer?->name ?? 'Generico')">
        <x-slot:actions>
            <x-btn href="{{ route('documents.picking-list', $pickingList) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Stampa
            </x-btn>
            @if($pickingList->status === 'pending')
                <form method="POST" action="{{ route('picking.start', $pickingList) }}">
                    @csrf
                    <x-btn type="submit" variant="primary">Avvia Picking</x-btn>
                </form>
            @elseif($pickingList->status === 'in_progress')
                <form method="POST" action="{{ route('picking.complete', $pickingList) }}">
                    @csrf
                    <x-btn type="submit" variant="success" onclick="return confirm('Confermi completamento picking?')">Completa</x-btn>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-3 border-b flex items-center gap-4">
            <x-badge :color="$pickingList->status_color" :text="$pickingList->status_label"/>
            @if($pickingList->assignedTo)
                <span class="text-sm text-gray-500">Assegnato a: <strong>{{ $pickingList->assignedTo->name }}</strong></span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posizione</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotto</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Da Prelevare</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prelevato</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                    @if($pickingList->status === 'in_progress')
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azione</th>
                    @endif
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pickingList->items as $item)
                        <tr class="{{ $item->is_completed ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-3">
                                @if($item->slot)
                                    <div class="text-sm font-mono font-medium text-indigo-600">{{ $item->slot->full_code }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ $item->slot->shelf->zone->warehouse->name }} &rsaquo;
                                        {{ $item->slot->shelf->zone->name }} &rsaquo;
                                        {{ $item->slot->shelf->name }}
                                    </div>
                                @else
                                    <span class="text-xs text-red-500">Slot non assegnato</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500 font-mono">{{ $item->lot_number ?? '–' }}</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">{{ $item->quantity_required }}</td>
                            <td class="px-6 py-3 text-right text-sm {{ $item->is_completed ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                {{ $item->quantity_picked }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($item->is_completed)
                                    <x-badge color="green" text="OK"/>
                                @else
                                    <x-badge color="yellow" text="Da fare"/>
                                @endif
                            </td>
                            @if($pickingList->status === 'in_progress' && !$item->is_completed)
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('picking.confirm-item', [$pickingList, $item]) }}" class="flex items-center justify-end gap-2">
                                        @csrf
                                        <input type="number" name="quantity_picked" value="{{ $item->quantity_required }}" min="0" max="{{ $item->quantity_required }}" class="w-16 text-center rounded-md border-gray-300 shadow-sm text-sm">
                                        <x-btn type="submit" variant="success" size="sm">✓</x-btn>
                                    </form>
                                </td>
                            @elseif($pickingList->status === 'in_progress')
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
