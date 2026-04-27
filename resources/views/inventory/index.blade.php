<x-app-layout>
    <x-slot:title>Inventario</x-slot>
    <x-page-header title="Sessioni Inventario">
        <x-slot:actions>
            <x-btn href="{{ route('inventory.create') }}" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuova Sessione
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ambito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Articoli</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creato da</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $session->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($session->shelf) Scaffale: {{ $session->shelf->code }}
                                @elseif($session->zone) Zona: {{ $session->zone->name }}
                                @else Totale
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :color="$session->status_color" :text="$session->status_label"/></td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $session->items_count ?? $session->items->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $session->createdBy->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $session->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('inventory.show', $session) }}" class="text-indigo-600 hover:underline text-sm">Apri</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">Nessuna sessione trovata</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sessions->hasPages())
            <div class="px-6 py-4 border-t">{{ $sessions->links() }}</div>
        @endif
    </div>
</x-app-layout>
