<x-app-layout>
    <x-slot:title>Ordini di Vendita</x-slot>
    <x-page-header title="Ordini di Vendita">
        <x-slot:actions>
            @can('create', App\Models\SalesOrder::class)
                <x-btn href="{{ route('sales-orders.create') }}" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuovo Ordine
                </x-btn>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti gli stati</option>
                <option value="pending" @selected(request('status')=='pending')>In Attesa</option>
                <option value="picking" @selected(request('status')=='picking')>In Picking</option>
                <option value="packed" @selected(request('status')=='packed')>Imballato</option>
                <option value="shipped" @selected(request('status')=='shipped')>Spedito</option>
                <option value="cancelled" @selected(request('status')=='cancelled')>Annullato</option>
            </select>
            <select name="customer_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti i clienti</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" @selected(request('customer_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <x-btn type="submit" variant="secondary">Filtra</x-btn>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Ordine</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Picking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creato</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $order->order_number ?? '#'.$order->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $order->customer?->name ?? 'Generico' }}</td>
                            <td class="px-6 py-4"><x-badge :color="$order->status_color" :text="$order->status_label"/></td>
                            <td class="px-6 py-4">
                                @if($order->pickingList)
                                    <x-badge :color="$order->pickingList->status_color" :text="$order->pickingList->status_label"/>
                                @else
                                    <span class="text-xs text-gray-400">–</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales-orders.show', $order) }}" class="text-indigo-600 hover:underline text-sm">Dettaglio</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Nessun ordine trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t">{{ $orders->links() }}</div>
        @endif
    </div>
</x-app-layout>
