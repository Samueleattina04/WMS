<x-app-layout>
    <x-slot:title>Clienti</x-slot>
    <x-page-header title="Clienti">
        <x-slot:actions>
            <x-btn href="{{ route('customers.create') }}" variant="primary">+ Nuovo Cliente</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca cliente..." class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm text-sm">
                <select name="active" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">Tutti</option>
                    <option value="1" @selected(request('active')=='1')>Attivi</option>
                    <option value="0" @selected(request('active')=='0')>Inattivi</option>
                </select>
                <x-btn type="submit" variant="secondary">Filtra</x-btn>
                @if(request()->hasAny(['search','active']))
                    <x-btn href="{{ route('customers.index') }}" variant="secondary">Reset</x-btn>
                @endif
            </form>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Nome</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Referente</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Telefono</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500">Stato</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('customers.show', $customer) }}" class="hover:text-indigo-600">{{ $customer->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->contact_person ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->email ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($customer->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Attivo</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Inattivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Modifica</a>
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Eliminare questo cliente?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Elimina</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Nessun cliente trovato</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($customers->hasPages())
        <div class="p-4 border-t border-gray-100">{{ $customers->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
