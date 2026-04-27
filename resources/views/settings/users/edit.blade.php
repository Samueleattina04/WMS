<x-app-layout>
    <x-slot:title>Modifica Utente</x-slot>
    <x-page-header title="Modifica Utente: {{ $user->name }}">
        <x-slot:actions>
            <x-btn href="{{ route('settings.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>
    <form method="POST" action="{{ route('settings.users.update', $user) }}" class="max-w-md">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome" name="name" :required="true" :value="old('name', $user->name)"/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruolo</label>
                <select name="role" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="warehouse" @selected(old('role',$user->role)=='warehouse')>Magazziniere</option>
                    <option value="manager" @selected(old('role',$user->role)=='manager')>Responsabile</option>
                    <option value="readonly" @selected(old('role',$user->role)=='readonly')>Sola Lettura</option>
                    <option value="admin" @selected(old('role',$user->role)=='admin')>Amministratore</option>
                </select>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_active', $user->is_active))>
                <span class="text-sm font-medium text-gray-700">Utente Attivo</span>
            </label>
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-3">Lascia vuoto per mantenere la password attuale</p>
                <x-form-input label="Nuova Password" name="password" type="password"/>
                <div class="mt-3">
                    <x-form-input label="Conferma Password" name="password_confirmation" type="password"/>
                </div>
            </div>
        </div>
        <div class="mt-4 flex gap-3">
            <x-btn href="{{ route('settings.index') }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Salva Modifiche</x-btn>
        </div>
    </form>
</x-app-layout>
