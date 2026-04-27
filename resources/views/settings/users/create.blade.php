<x-app-layout>
    <x-slot:title>Nuovo Utente</x-slot>
    <x-page-header title="Nuovo Utente">
        <x-slot:actions>
            <x-btn href="{{ route('settings.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>
    <form method="POST" action="{{ route('settings.users.store') }}" class="max-w-md">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome" name="name" :required="true" :value="old('name')"/>
            <x-form-input label="Email" name="email" type="email" :required="true" :value="old('email')"/>
            <x-form-input label="Password" name="password" type="password" :required="true"/>
            <x-form-input label="Conferma Password" name="password_confirmation" type="password" :required="true"/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruolo <span class="text-red-500">*</span></label>
                <select name="role" class="block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                    <option value="warehouse" @selected(old('role')=='warehouse')>Magazziniere</option>
                    <option value="manager" @selected(old('role')=='manager')>Responsabile</option>
                    <option value="readonly" @selected(old('role')=='readonly')>Sola Lettura</option>
                    <option value="admin" @selected(old('role')=='admin')>Amministratore</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-3">
            <x-btn href="{{ route('settings.index') }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Crea Utente</x-btn>
        </div>
    </form>
</x-app-layout>
