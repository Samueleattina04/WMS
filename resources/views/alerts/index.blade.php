<x-app-layout>
    <x-slot:title>Alert</x-slot>
    <x-page-header title="Alert Magazzino">
        <x-slot:actions>
            @if(request('resolved') != '1')
                <form method="POST" action="{{ route('alerts.resolve-all') }}">
                    @csrf
                    <x-btn type="submit" variant="secondary" onclick="return confirm('Risolvi tutti gli alert attivi?')">
                        Risolvi Tutti
                    </x-btn>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex gap-3">
            <select name="type" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti i tipi</option>
                <option value="low_stock" @selected(request('type')=='low_stock')>Scorta Bassa</option>
                <option value="out_of_stock" @selected(request('type')=='out_of_stock')>Esaurito</option>
                <option value="expiry" @selected(request('type')=='expiry')>In Scadenza</option>
            </select>
            <select name="resolved" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Attivi</option>
                <option value="1" @selected(request('resolved')=='1')>Risolti</option>
            </select>
            <x-btn type="submit" variant="secondary">Filtra</x-btn>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Q. Attuale</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Soglia / Scadenza</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notificato</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Azione</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($alerts as $alert)
                        <tr class="hover:bg-gray-50 {{ $alert->is_resolved ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <a href="{{ route('products.show', $alert->product) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $alert->product->name }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $alert->product->sku }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$alert->alert_type_color" :text="$alert->alert_type_label"/>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold {{ $alert->current_quantity == 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $alert->current_quantity ?? '–' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                @if($alert->alert_type === 'expiry')
                                    {{ $alert->expiry_date?->format('d/m/Y') }}
                                @else
                                    Min: {{ $alert->threshold_quantity }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $alert->notified_at?->format('d/m H:i') ?? '–' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$alert->is_resolved)
                                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}">
                                        @csrf
                                        <x-btn type="submit" variant="success" size="sm">Risolvi</x-btn>
                                    </form>
                                @else
                                    <x-badge color="green" text="Risolto"/>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-green-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-gray-500">Nessun alert attivo! Tutto ok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($alerts->hasPages())
            <div class="px-6 py-4 border-t">{{ $alerts->links() }}</div>
        @endif
    </div>
</x-app-layout>
