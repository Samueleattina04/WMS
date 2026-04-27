<x-app-layout>
    <x-slot:title>{{ $supplier->name }}</x-slot>
    <x-page-header :title="$supplier->name">
        <x-slot:actions><x-btn href="{{ route('suppliers.edit', $supplier) }}" variant="secondary">Modifica</x-btn></x-slot:actions>
    </x-page-header>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Contatti</h3>
            <dl class="space-y-2 text-sm">
                @if($supplier->contact_person)<div class="flex gap-2"><dt class="text-gray-500 w-24">Referente</dt><dd>{{ $supplier->contact_person }}</dd></div>@endif
                @if($supplier->email)<div class="flex gap-2"><dt class="text-gray-500 w-24">Email</dt><dd>{{ $supplier->email }}</dd></div>@endif
                @if($supplier->phone)<div class="flex gap-2"><dt class="text-gray-500 w-24">Telefono</dt><dd>{{ $supplier->phone }}</dd></div>@endif
                @if($supplier->address)<div class="flex gap-2"><dt class="text-gray-500 w-24">Indirizzo</dt><dd>{{ $supplier->address }}</dd></div>@endif
            </dl>
        </div>
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Ultimi Ordini</h3>
            @forelse($supplier->purchaseOrders as $order)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <a href="{{ route('purchase-orders.show', $order) }}" class="text-sm text-indigo-600 hover:underline">{{ $order->order_number ?? '#'.$order->id }}</a>
                    <x-badge :color="$order->status_color" :text="$order->status_label"/>
                    <span class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nessun ordine</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
