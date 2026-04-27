<x-app-layout>
    <x-slot:title>Fornitori</x-slot>
    <x-page-header title="Fornitori">
        <x-slot:actions>
            <x-btn href="{{ route('suppliers.create') }}" variant="primary">+ Nuovo Fornitore</x-btn>
        </x-slot:actions>
    </x-page-header>
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca fornitore..." class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <x-btn type="submit" variant="secondary">Cerca</x-btn>
        </div>
    </form>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead><tr class="bg-gray-50">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contatto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email / Tel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($suppliers as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $s->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $s->contact_person ?? '–' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $s->email ?? '' }}<br>{{ $s->phone ?? '' }}</td>
                        <td class="px-6 py-4"><x-badge :color="$s->is_active?'green':'gray'" :text="$s->is_active?'Attivo':'Inattivo'"/></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('suppliers.show', $s) }}" class="text-indigo-600 hover:underline text-sm mr-2">Dettaglio</a>
                            <a href="{{ route('suppliers.edit', $s) }}" class="text-gray-600 hover:underline text-sm">Modifica</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Nessun fornitore trovato</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($suppliers->hasPages())<div class="px-6 py-4 border-t">{{ $suppliers->links() }}</div>@endif
    </div>
</x-app-layout>
