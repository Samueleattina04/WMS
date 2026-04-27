<x-app-layout>
    <x-slot:title>Valore Magazzino</x-slot>
    <x-page-header title="Valore Magazzino">
        <x-slot:actions>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Esporta Excel
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 bg-indigo-600 rounded-xl p-6 text-white">
        <p class="text-sm text-indigo-200">Valore Totale Magazzino (a costo)</p>
        <p class="text-4xl font-bold mt-1">€ {{ number_format($totalValue, 2, ',', '.') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Magazzino / Zona</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Q.tà</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Costo Unit.</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valore Totale</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stockData as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $row->name }}</p>
                                <p class="text-xs text-gray-400">{{ $row->sku }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $row->warehouse_name }} › {{ $row->zone_name }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($row->total_quantity) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">€ {{ number_format($row->cost_price, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">€ {{ number_format($row->total_value, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Nessun dato disponibile</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stockData->hasPages())
            <div class="px-6 py-4 border-t">{{ $stockData->links() }}</div>
        @endif
    </div>
</x-app-layout>
