<x-app-layout>
    <x-slot:title>{{ $customer->name }}</x-slot>
    <x-page-header title="{{ $customer->name }}">
        <x-slot:actions>
            <x-btn href="{{ route('customers.edit', $customer) }}" variant="secondary">Modifica</x-btn>
            <x-btn href="{{ route('customers.index') }}" variant="secondary">Torna ai Clienti</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Anagrafica</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Nome</dt>
                        <dd class="font-medium text-gray-900">{{ $customer->name }}</dd>
                    </div>
                    @if($customer->contact_person)
                    <div>
                        <dt class="text-gray-500">Referente</dt>
                        <dd class="text-gray-900">{{ $customer->contact_person }}</dd>
                    </div>
                    @endif
                    @if($customer->email)
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $customer->email }}</dd>
                    </div>
                    @endif
                    @if($customer->phone)
                    <div>
                        <dt class="text-gray-500">Telefono</dt>
                        <dd class="text-gray-900">{{ $customer->phone }}</dd>
                    </div>
                    @endif
                    @if($customer->address)
                    <div>
                        <dt class="text-gray-500">Indirizzo</dt>
                        <dd class="text-gray-900">{{ $customer->address }}</dd>
                    </div>
                    @endif
                    @if($customer->vat_number)
                    <div>
                        <dt class="text-gray-500">P.IVA / C.F.</dt>
                        <dd class="text-gray-900">{{ $customer->vat_number }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Stato</dt>
                        <dd>
                            @if($customer->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Attivo</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Inattivo</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Ordini di Vendita</h3>
                    <x-btn href="{{ route('sales-orders.create', ['customer_id' => $customer->id]) }}" variant="primary">+ Nuovo Ordine</x-btn>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">N. Ordine</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Data</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Stato</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Totale</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customer->salesOrders ?? [] as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('sales-orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3"><span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ $order->status }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">€ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-sm">Nessun ordine</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
