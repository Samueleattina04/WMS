<x-app-layout>
    <x-slot:title>Dashboard</x-slot>

    <div class="space-y-6">
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <x-kpi-card
                title="Valore Magazzino"
                :value="'€ ' . number_format($kpis['warehouseValue'], 0, ',', '.')"
                color="indigo"
                :link="route('reports.stock-value')"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            />
            <x-kpi-card
                title="Movimenti Oggi"
                :value="$kpis['dailyMovements']"
                color="blue"
                :link="route('movements.index')"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>'
            />
            <x-kpi-card
                title="Alert Attivi"
                :value="$kpis['activeAlerts']"
                :color="$kpis['activeAlerts'] > 0 ? 'red' : 'green'"
                :link="route('alerts.index')"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>'
            />
            <x-kpi-card
                title="Scorte Basse"
                :value="$kpis['lowStockProducts']"
                :color="$kpis['lowStockProducts'] > 0 ? 'yellow' : 'green'"
                :link="route('products.index', ['filter' => 'low_stock'])"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'
            />
            <x-kpi-card
                title="OA Aperti"
                :value="$kpis['openPurchaseOrders']"
                color="purple"
                :link="route('purchase-orders.index')"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'
            />
            <x-kpi-card
                title="OV Aperti"
                :value="$kpis['openSalesOrders']"
                color="green"
                :link="route('sales-orders.index')"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>'
            />
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            {{-- Movements Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Movimenti ultimi 7 giorni</h3>
                <canvas id="movementsChart" height="200"></canvas>
            </div>

            {{-- Active Alerts --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Alert attivi</h3>
                    <a href="{{ route('alerts.index') }}" class="text-sm text-indigo-600 hover:underline">Vedi tutti</a>
                </div>
                @forelse($activeAlerts as $alert)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $alert->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $alert->alert_type_label }}</p>
                        </div>
                        <x-badge :color="$alert->alert_type_color" :text="$alert->alert_type_label"/>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Nessun alert attivo</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Movements --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Ultimi movimenti</h3>
                <a href="{{ route('movements.index') }}" class="text-sm text-indigo-600 hover:underline">Vedi tutti</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantità</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operatore</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Ora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentMovements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $movement->product->name }}</td>
                                <td class="px-6 py-3">
                                    <x-badge :color="$movement->type_color" :text="$movement->type_label"/>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ number_format($movement->quantity) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $movement->createdBy->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $movement->created_at->format('d/m H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Nessun movimento registrato</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('movementsChart');
        const data = @json($movementsTrend);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.date),
                datasets: [
                    {
                        label: 'Entrate',
                        data: data.map(d => d.incoming),
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Uscite',
                        data: data.map(d => d.outgoing),
                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>
</x-app-layout>
