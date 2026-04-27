<x-app-layout>
    <x-slot:title>Ordine #{{ $purchaseOrder->order_number ?? $purchaseOrder->id }}</x-slot>
    <x-page-header title="OA: {{ $purchaseOrder->order_number ?? '#'.$purchaseOrder->id }}" :subtitle="'Fornitore: '.$purchaseOrder->supplier->name">
        <x-slot:actions>
            @if(!in_array($purchaseOrder->status, ['received','cancelled']))
                <x-btn href="{{ route('purchase-orders.receive', $purchaseOrder) }}" variant="success">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ricevi Merce
                </x-btn>
            @endif
            <x-btn href="{{ route('documents.ddt-entrata', $purchaseOrder) }}" variant="secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Stampa DDT
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Articoli</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead><tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ord.</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ricevuto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rimanente</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prezzo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Totale</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ $item->product->name }}<br>
                                    <span class="text-xs text-gray-400">{{ $item->product->sku }}</span>
                                </td>
                                <td class="px-6 py-3 text-right text-sm">{{ $item->quantity_ordered }}</td>
                                <td class="px-6 py-3 text-right text-sm text-green-600 font-medium">{{ $item->quantity_received }}</td>
                                <td class="px-6 py-3 text-right text-sm {{ $item->remaining_quantity > 0 ? 'text-yellow-600' : 'text-gray-400' }}">{{ $item->remaining_quantity }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">€ {{ number_format($item->quantity_ordered * $item->unit_price, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-6 py-3 text-right text-sm font-bold text-gray-700">Totale Ordine:</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">€ {{ number_format($purchaseOrder->total_value, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informazioni</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Stato</dt><dd><x-badge :color="$purchaseOrder->status_color" :text="$purchaseOrder->status_label"/></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Fornitore</dt><dd class="font-medium text-right">{{ $purchaseOrder->supplier->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Data attesa</dt><dd>{{ $purchaseOrder->expected_date?->format('d/m/Y') ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Creato da</dt><dd>{{ $purchaseOrder->createdBy->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Data creazione</dt><dd>{{ $purchaseOrder->created_at->format('d/m/Y') }}</dd></div>
                </dl>
                @if($purchaseOrder->notes)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">{{ $purchaseOrder->notes }}</p>
                    </div>
                @endif
            </div>
            @if(!in_array($purchaseOrder->status, ['received','cancelled']))
                <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}">
                    @csrf @method('DELETE')
                    <x-btn type="submit" variant="danger" class="w-full justify-center" onclick="return confirm('Confermi eliminazione?')">Elimina Ordine</x-btn>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
