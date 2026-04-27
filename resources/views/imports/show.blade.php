<x-app-layout>
    <x-slot:title>Importazione #{{ $import->id }}</x-slot>
    <x-page-header title="Dettaglio Importazione">
        <x-slot:actions>
            <x-btn href="{{ route('imports.index') }}" variant="secondary">Torna alle Importazioni</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Riepilogo</h3>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500">File</dt><dd class="font-medium text-gray-900">{{ $import->filename }}</dd></div>
                    <div><dt class="text-gray-500">Tipo</dt><dd class="text-gray-900">{{ $import->type_label }}</dd></div>
                    <div><dt class="text-gray-500">Stato</dt>
                        <dd>
                            @php $statusColors = ['completed'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-700','processing'=>'bg-yellow-100 text-yellow-700','pending'=>'bg-gray-100 text-gray-600']; @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$import->status] ?? 'bg-gray-100' }}">{{ $import->status_label }}</span>
                        </dd>
                    </div>
                    <div><dt class="text-gray-500">Totale righe</dt><dd class="text-gray-900">{{ $import->rows_total ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500">Importate</dt><dd class="text-green-700 font-medium">{{ $import->rows_imported ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500">Errori</dt><dd class="text-red-600 font-medium">{{ $import->rows_failed ?? 0 }}</dd></div>
                    <div><dt class="text-gray-500">Operatore</dt><dd class="text-gray-900">{{ $import->createdBy?->name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Data</dt><dd class="text-gray-900">{{ $import->created_at->format('d/m/Y H:i') }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2">
            @if($import->error_log && count($import->error_log) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-5 mb-4">
                <h3 class="font-semibold text-red-700 mb-3">Errori riscontrati ({{ count($import->error_log) }})</h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($import->error_log as $error)
                    <div class="flex items-start gap-2 text-sm bg-red-50 rounded p-2">
                        <span class="text-red-400 font-mono text-xs mt-0.5">Riga {{ $error['row'] ?? '?' }}</span>
                        <span class="text-red-700">{{ $error['message'] ?? $error }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($import->status === 'completed')
            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-semibold text-green-900">Importazione completata con successo!</p>
                        <p class="text-sm text-green-700">{{ $import->rows_imported ?? 0 }} record importati su {{ $import->rows_total ?? 0 }} totali.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
