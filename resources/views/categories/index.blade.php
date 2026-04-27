<x-app-layout>
    <x-slot:title>Categorie</x-slot>
    <x-page-header title="Categorie Prodotti"/>

    <div x-data="{ showForm: false, editId: null, editName: '', editDesc: '', editParent: '' }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Panel --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4" x-text="editId ? 'Modifica Categoria' : 'Nuova Categoria'"></h3>
                    <form method="POST" :action="editId ? '/categories/' + editId : '{{ route('categories.store') }}'" class="space-y-3">
                        @csrf
                        <span x-show="editId" x-html="'<input name=\'_method\' type=\'hidden\' value=\'PUT\'>'"></span>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                            <input type="text" name="name" :value="editId ? editName : '{{ old('name') }}'" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria Padre</label>
                            <select name="parent_id" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="">— Nessuna (categoria principale) —</option>
                                @foreach($categories->whereNull('parent_id') as $parent)
                                <option value="{{ $parent->id }}" :selected="editParent == '{{ $parent->id }}'">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                            <textarea name="description" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" x-text="editDesc"></textarea>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-indigo-700">Salva</button>
                            <button type="button" x-show="editId" @click="editId=null;editName='';editDesc='';editParent=''" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Annulla</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Panel --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Nome</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Padre</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Descrizione</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500">Prodotti</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    @if($cat->parent_id)
                                        <span class="text-gray-400 mr-1">└</span>
                                    @endif
                                    {{ $cat->name }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $cat->parent?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $cat->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $cat->products_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button
                                        @click="editId='{{ $cat->id }}'; editName='{{ addslashes($cat->name) }}'; editDesc='{{ addslashes($cat->description ?? '') }}'; editParent='{{ $cat->parent_id }}'"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Modifica</button>
                                    <form method="POST" action="{{ route('categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Eliminare?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Elimina</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Nessuna categoria. Crea la prima!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
