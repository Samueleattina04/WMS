<x-app-layout>
    <x-slot:title>Modifica Fornitore</x-slot>
    <x-page-header title="Modifica: {{ $supplier->name }}">
        <x-slot:actions><x-btn href="{{ route('suppliers.show', $supplier) }}" variant="secondary">Annulla</x-btn></x-slot:actions>
    </x-page-header>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="max-w-xl">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome" name="name" :required="true" :value="old('name',$supplier->name)"/>
            <x-form-input label="Referente" name="contact_person" :value="old('contact_person',$supplier->contact_person)"/>
            <x-form-input label="Email" name="email" type="email" :value="old('email',$supplier->email)"/>
            <x-form-input label="Telefono" name="phone" :value="old('phone',$supplier->phone)"/>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label><textarea name="address" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('address',$supplier->address) }}</textarea></div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_active',$supplier->is_active))><span class="text-sm font-medium text-gray-700">Attivo</span></label>
        </div>
        <div class="mt-4 flex gap-3"><x-btn href="{{ route('suppliers.show', $supplier) }}" variant="secondary">Annulla</x-btn><x-btn type="submit" variant="primary">Salva</x-btn></div>
    </form>
</x-app-layout>
