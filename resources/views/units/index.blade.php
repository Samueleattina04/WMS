<x-app-layout>
    <x-slot:title>Unità di Misura</x-slot>
    <x-page-header title="Unità di Misura"/>

    <div x-data="{ editId: null, editName: '', editAbbr: '', editType: 'quantity' }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4" x-text="editId ? 'Modifica Unità' : 'Nuova Unità di Misura'"></h3>
                    <form method="POST" :action="editId ? '/units/' + editId : '{{ route('units.store') }}'" class="space-y-3">
                        @csrf
                        <span x-show="editId" x-html="'<input name=\'_method\' type=\'hidden\' value=\'PUT\'>'"></span>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input type="text" name="name" :value="editId ? editName : '{{ old('name') }}'" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Es: Pezzo, Chilogrammo...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Abbreviazione <span class="text-red-500">*</span></label>
                            <input type="text" name="abbreviation" :value="editId ? editAbbr : '{{ old('abbreviation') }}'" required maxlength="10" class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Es: pz, kg, lt...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="type" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="quantity" :selected="editType === 'quantity'">Quantità</option>
                                <option value="weight" :selected="editType === 'weight'">Peso</option>
                                <option value="volume" :selected="editType === 'volume'">Volume</option>
                                <option value="length" :selected="editType === 'length'">Lunghezza</option>
                                <option value="area" :selected="editType === 'area'">Area</option>
                            </select>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-indigo-700">Salva</button>
                            <button type="button" x-show="editId" @click="editId=null" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Annulla</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Nome</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Abbreviazione</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Tipo</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500">Prodotti</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                            $typeLabels = ['quantity'=>'Quantità','weight'=>'Peso','volume'=>'Volume','length'=>'Lunghezza','area'=>'Area'];
                            @endphp
                            @forelse($units as $unit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $unit->name }}</td>
                                <td class="px-4 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $unit->abbreviation }}</span></td>
                                <td class="px-4 py-3 text-gray-600">{{ $typeLabels[$unit->type] ?? $unit->type }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $unit->products_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button
                                        @click="editId='{{ $unit->id }}'; editName='{{ addslashes($unit->name) }}'; editAbbr='{{ $unit->abbreviation }}'; editType='{{ $unit->type }}'"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Modifica</button>
                                    <form method="POST" action="{{ route('units.destroy', $unit) }}" class="inline" onsubmit="return confirm('Eliminare?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Elimina</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Nessuna unità. Crea la prima!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
