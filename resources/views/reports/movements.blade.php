<x-app-layout>
    <x-slot:title>Report Movimenti</x-slot>
    <x-page-header title="Report Movimenti">
        <x-slot:actions>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Esporta Excel
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
            <select name="product_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti i prodotti</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(request('product_id')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">Tutti i tipi</option>
                @foreach(['incoming'=>'Entrata','outgoing'=>'Uscita','transfer'=>'Trasferimento','adjustment'=>'Rettifica','return'=>'Reso','damaged'=>'Danno'] as $val=>$label)
                    <option value="{{ $val }}" @selected(request('type')==$val)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300 shadow-sm text-sm" placeholder="Dal">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300 shadow-sm text-sm" placeholder="Al">
            <x-btn type="submit" variant="secondary">Cerca</x-btn>
            <a href="{{ route('reports.movements') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Q.tà</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Da Slot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">A Slot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operatore</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($movements as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $m->product->name }}</td>
                            <td class="px-4 py-3"><x-badge :color="$m->type_color" :text="$m->type_label"/></td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($m->quantity) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $m->slotFrom?->full_code ?? '–' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $m->slotTo?->full_code ?? '–' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $m->document_number ?? '–' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $m->createdBy?->name ?? '–' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">Nessun movimento trovato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="px-6 py-4 border-t">{{ $movements->links() }}</div>
        @endif
    </div>
</x-app-layout>
