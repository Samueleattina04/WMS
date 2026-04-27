<x-app-layout>
    <x-slot:title>Ricezione Merce</x-slot>
    <x-page-header title="Ricezione Merce" :subtitle="'Ordine: '.($purchaseOrder->order_number ?? '#'.$purchaseOrder->id).' - '.$purchaseOrder->supplier->name">
        <x-slot:actions>
            <x-btn href="{{ route('purchase-orders.show', $purchaseOrder) }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('purchase-orders.process-receiving', $purchaseOrder) }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead><tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ordinato</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Già Ricevuto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Da Ricevere</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Slot Destinazione</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Lotto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Scadenza</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->items as $index => $item)
                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                            <tr class="{{ $item->remaining_quantity == 0 ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->product->sku }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-sm">{{ $item->quantity_ordered }}</td>
                                <td class="px-4 py-3 text-center text-sm text-green-600">{{ $item->quantity_received }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" name="items[{{ $index }}][quantity_received]"
                                        value="{{ $item->remaining_quantity }}"
                                        min="0" max="{{ $item->remaining_quantity }}"
                                        class="w-20 text-center rounded-md border-gray-300 shadow-sm text-sm"
                                        {{ $item->remaining_quantity == 0 ? 'disabled' : '' }}>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][slot_id]" class="rounded-md border-gray-300 shadow-sm text-sm w-full">
                                        <option value="">-- Nessuno --</option>
                                        @foreach($slots as $slot)
                                            <option value="{{ $slot->id }}">
                                                {{ $slot->shelf->zone->warehouse->name }} › {{ $slot->shelf->zone->name }} › {{ $slot->shelf->code }} › {{ $slot->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->product->has_lot_tracking)
                                        <input type="text" name="items[{{ $index }}][lot_number]" placeholder="LOT-001"
                                            class="w-24 rounded-md border-gray-300 shadow-sm text-sm">
                                    @else
                                        <span class="text-gray-300 text-xs">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->product->has_expiry)
                                        <input type="date" name="items[{{ $index }}][expiry_date]"
                                            class="rounded-md border-gray-300 shadow-sm text-sm">
                                    @else
                                        <span class="text-gray-300 text-xs">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <x-btn href="{{ route('purchase-orders.show', $purchaseOrder) }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Conferma Ricezione</x-btn>
        </div>
    </form>
</x-app-layout>
