<x-app-layout>
    <x-slot:title>Modifica Cliente</x-slot>
    <x-page-header title="Modifica: {{ $customer->name }}">
        <x-slot:actions>
            <x-btn href="{{ route('customers.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>
    <form method="POST" action="{{ route('customers.update', $customer) }}" class="max-w-lg">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <x-form-input label="Nome Azienda / Cliente" name="name" :required="true" :value="old('name', $customer->name)"/>
            <x-form-input label="Referente" name="contact_person" :value="old('contact_person', $customer->contact_person)"/>
            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Email" name="email" type="email" :value="old('email', $customer->email)"/>
                <x-form-input label="Telefono" name="phone" :value="old('phone', $customer->phone)"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                <textarea name="address" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $customer->address) }}</textarea>
            </div>
            <x-form-input label="Partita IVA / C.F." name="vat_number" :value="old('vat_number', $customer->vat_number)"/>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_active', $customer->is_active))>
                <span class="text-sm font-medium text-gray-700">Cliente Attivo</span>
            </label>
        </div>
        <div class="mt-4 flex gap-3">
            <x-btn href="{{ route('customers.index') }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Salva Modifiche</x-btn>
        </div>
    </form>
</x-app-layout>
