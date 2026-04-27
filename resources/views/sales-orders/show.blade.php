<x-app-layout>
    <x-slot:title>Ordine {{ $salesOrder->order_number ?? '#'.$salesOrder->id }}</x-slot>
    <x-page-header title="OV: {{ $salesOrder->order_number ?? '#'.$salesOrder->id }}" :subtitle="$salesOrder->customer?->name ?? 'Cliente generico'">
        <x-slot:actions>
            @if(!$salesOrder->pickingList && $salesOrder->status === 'pending')
                <form method="POST" action="{{ route('sales-orders.generate-picking', $salesOrder) }}" class="inline">
                    @csrf
                    <x-btn type="submit" variant="primary">Genera Picking List</x-btn>
                </form>
            @elseif($salesOrder->pickingList)
                <x-btn href="{{ route('picking.show', $salesOrder->pickingList) }}" variant="secondary">Vedi Picking</x-btn>
            @endif
            @if($salesOrder->status === 'packed')
                <form method="POST" action="{{ route('sales-orders.update-status', $salesOrder) }}" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="shipped">
                    <x-btn type="submit" variant="success">Segna Spedito</x-btn>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b"><h3 class="text-base font-semibold">Articoli</h3></div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead><tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Richiesto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prelevato</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($salesOrder->items as $item)
                            <tr>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-6 py-3 text-right text-sm">{{ $item->quantity_requested }}</td>
                                <td class="px-6 py-3 text-right text-sm {{ $item->quantity_picked >= $item->quantity_requested ? 'text-green-600 font-medium' : 'text-yellow-600' }}">
                                    {{ $item->quantity_picked }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informazioni</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Stato</dt><dd><x-badge :color="$salesOrder->status_color" :text="$salesOrder->status_label"/></dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Cliente</dt><dd>{{ $salesOrder->customer?->name ?? '–' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Creato da</dt><dd>{{ $salesOrder->createdBy->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Data</dt><dd>{{ $salesOrder->created_at->format('d/m/Y') }}</dd></div>
                    @if($salesOrder->pickingList)
                        <div class="flex justify-between"><dt class="text-gray-500">Picking</dt><dd><x-badge :color="$salesOrder->pickingList->status_color" :text="$salesOrder->pickingList->status_label"/></dd></div>
                    @endif
                </dl>
                @if($salesOrder->notes)
                    <div class="mt-3 pt-3 border-t"><p class="text-xs text-gray-500">{{ $salesOrder->notes }}</p></div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
