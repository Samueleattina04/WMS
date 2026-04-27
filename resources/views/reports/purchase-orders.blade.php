<x-app-layout>
    <x-slot:title>Report Ordini di Acquisto</x-slot>
    <x-page-header title="Report Ordini di Acquisto"/>
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex gap-3 flex-wrap">
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti</option>
                <option value="pending" @selected(request('status')=='pending')>In Attesa</option>
                <option value="received" @selected(request('status')=='received')>Ricevuto</option>
                <option value="cancelled" @selected(request('status')=='cancelled')>Annullato</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
            <x-btn type="submit" variant="secondary">Filtra</x-btn>
        </div>
    </form>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Ordine</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornitore</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valore</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><a href="{{ route('purchase-orders.show', $order) }}" class="text-indigo-600 hover:underline">{{ $order->order_number ?? '#'.$order->id }}</a></td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $order->supplier->name }}</td>
                            <td class="px-6 py-4"><x-badge :color="$order->status_color" :text="$order->status_label"/></td>
                            <td class="px-6 py-4 text-right text-sm font-semibold">€ {{ number_format($order->total_value, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Nessun ordine trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="px-6 py-4 border-t">{{ $orders->links() }}</div>@endif
    </div>
</x-app-layout>
