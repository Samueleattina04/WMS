<x-app-layout>
    <x-slot:title>Importazioni</x-slot>
    <x-page-header title="Importazioni Excel">
        <x-slot:actions>
            <x-btn href="{{ route('imports.create') }}" variant="primary">+ Nuova Importazione</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-50 rounded-lg"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V7"/></svg></div>
                <span class="font-medium text-gray-900">Prodotti</span>
            </div>
            <p class="text-sm text-gray-500">Importa anagrafica prodotti da file Excel. Aggiorna o crea nuovi prodotti tramite SKU.</p>
            <a href="{{ route('imports.create', ['type' => 'products']) }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Importa prodotti →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 rounded-lg"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                <span class="font-medium text-gray-900">Giacenze Iniziali</span>
            </div>
            <p class="text-sm text-gray-500">Carica le scorte iniziali di magazzino. Utile per la prima configurazione del sistema.</p>
            <a href="{{ route('imports.create', ['type' => 'stock']) }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Importa giacenze →</a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 rounded-lg"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <span class="font-medium text-gray-900">Template</span>
            </div>
            <p class="text-sm text-gray-500">Scarica i template Excel precompilati con le intestazioni corrette per ogni tipo di importazione.</p>
            <a href="{{ route('imports.template', 'products') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-medium">Scarica template prodotti →</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Storico Importazioni</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">File</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tipo</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Righe</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Errori</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Stato</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Operatore</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Data</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Dettagli</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($imports as $import)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $import->filename }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $import->type_label }}</td>
                    <td class="px-4 py-3 text-center text-gray-700">{{ $import->rows_total ?? 0 }}</td>
                    <td class="px-4 py-3 text-center">
                        @if(($import->rows_failed ?? 0) > 0)
                            <span class="text-red-600 font-medium">{{ $import->rows_failed }}</span>
                        @else
                            <span class="text-green-600">0</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php $statusColors = ['completed'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-700','processing'=>'bg-yellow-100 text-yellow-700','pending'=>'bg-gray-100 text-gray-600']; @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$import->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $import->status_label }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $import->createdBy?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $import->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('imports.show', $import) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Visualizza</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Nessuna importazione effettuata</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($imports->hasPages())
        <div class="p-4 border-t border-gray-100">{{ $imports->links() }}</div>
        @endif
    </div>
</x-app-layout>
