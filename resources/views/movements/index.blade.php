<x-app-layout>
    <x-slot:title>Movimenti</x-slot:title>

    <x-page-header title="Movimenti" subtitle="Storico di tutti i movimenti di magazzino">
        <x-slot:actions>
            <x-btn :href="route('movements.create')" variant="primary">+ Nuovo Movimento</x-btn>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Prodotto</label>
                <select name="product_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tutti i prodotti</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tutti i tipi</option>
                    <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>Entrata</option>
                    <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>Uscita</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Trasferimento</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Rettifica</option>
                    <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Reso</option>
                    <option value="damaged" {{ request('type') === 'damaged' ? 'selected' : '' }}>Danneggiato</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Data da</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Data a</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-end gap-2">
                <x-btn type="submit" variant="primary" class="flex-1">Filtra</x-btn>
                <x-btn href="{{ route('movements.index') }}" variant="secondary">Reset</x-btn>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            @if($movements->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-gray-500 text-sm">Nessun movimento trovato.</p>
                </div>
            @else
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodotto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantità</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Da</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">A</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operatore</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Ora</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($movements as $mov)
                    @php
                        $typeColors = ['incoming'=>'green','outgoing'=>'red','transfer'=>'blue','adjustment'=>'yellow','return'=>'purple','damaged'=>'orange'];
                        $typeLabels = ['incoming'=>'Entrata','outgoing'=>'Uscita','transfer'=>'Trasferimento','adjustment'=>'Rettifica','return'=>'Reso','damaged'=>'Danneggiato'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $mov->product->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <x-badge :color="$typeColors[$mov->type] ?? 'gray'" :text="$typeLabels[$mov->type] ?? $mov->type" />
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $mov->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->slotFrom->code ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->slotTo->code ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->document_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $mov->operator->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('movements.show', $mov) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Dettaglio</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @if($movements->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $movements->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
