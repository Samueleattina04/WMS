<x-app-layout>
    <x-slot:title>Nuovo Fornitore</x-slot>
    <x-page-header title="Nuovo Fornitore">
        <x-slot:actions><x-btn href="{{ route('suppliers.index') }}" variant="secondary">Annulla</x-btn></x-slot:actions>
    </x-page-header>
    <form method="POST" action="{{ route('suppliers.store') }}" class="max-w-xl">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome" name="name" :required="true" :value="old('name')"/>
            <x-form-input label="Referente" name="contact_person" :value="old('contact_person')"/>
            <x-form-input label="Email" name="email" type="email" :value="old('email')"/>
            <x-form-input label="Telefono" name="phone" :value="old('phone')"/>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label><textarea name="address" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('address') }}</textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Note</label><textarea name="notes" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('notes') }}</textarea></div>
        </div>
        <div class="mt-4 flex gap-3"><x-btn href="{{ route('suppliers.index') }}" variant="secondary">Annulla</x-btn><x-btn type="submit" variant="primary">Salva</x-btn></div>
    </form>
</x-app-layout>
