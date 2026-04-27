<x-app-layout>
    <x-slot:title>Picking List</x-slot>
    <x-page-header title="Picking List" subtitle="Gestione prelievi da magazzino"/>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex gap-3">
            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti gli stati</option>
                <option value="pending" @selected(request('status')=='pending')>In Attesa</option>
                <option value="in_progress" @selected(request('status')=='in_progress')>In Corso</option>
                <option value="completed" @selected(request('status')=='completed')>Completato</option>
            </select>
            <x-btn type="submit" variant="secondary">Filtra</x-btn>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordine Vendita</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assegnato a</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completato</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pickingLists as $pl)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $pl->salesOrder->order_number ?? '#'.$pl->sales_order_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $pl->salesOrder->customer?->name ?? 'Generico' }}</td>
                            <td class="px-6 py-4"><x-badge :color="$pl->status_color" :text="$pl->status_label"/></td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $pl->assignedTo?->name ?? '–' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $pl->completed_at?->format('d/m/Y H:i') ?? '–' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('picking.show', $pl) }}" class="text-indigo-600 hover:underline text-sm">Apri</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Nessuna picking list trovata</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pickingLists->hasPages())
            <div class="px-6 py-4 border-t">{{ $pickingLists->links() }}</div>
        @endif
    </div>
</x-app-layout>
