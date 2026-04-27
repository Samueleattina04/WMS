<x-app-layout>
    <x-slot:title>Impostazioni</x-slot>
    <x-page-header title="Impostazioni Azienda"/>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Company Data --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Dati Azienda</h3>
            <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <x-form-input label="Nome Azienda" name="name" :required="true" :value="old('name', $company->name)"/>
                    <x-form-input label="Email" name="email" type="email" :value="old('email', $company->email)"/>
                    <x-form-input label="Telefono" name="phone" :value="old('phone', $company->phone)"/>
                    <x-form-input label="P.IVA" name="vat_number" :value="old('vat_number', $company->vat_number)"/>
                    <x-form-input label="Codice Fiscale" name="fiscal_code" :value="old('fiscal_code', $company->fiscal_code)"/>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                        <textarea name="address" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('address', $company->address) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if($company->logo)
                            <img src="{{ Storage::url($company->logo) }}" class="h-12 mb-2">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700">
                    </div>
                    <x-btn type="submit" variant="primary">Salva Dati Azienda</x-btn>
                </div>
            </form>
        </div>

        {{-- Alert Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Impostazioni Alert</h3>
            <form method="POST" action="{{ route('settings.alerts.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giorni prima della scadenza per l'alert</label>
                        <input type="number" name="alert_days_before_expiry" min="1" max="365"
                            value="{{ old('alert_days_before_expiry', $company->getSetting('alert_days_before_expiry', 30)) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email per notifiche alert</label>
                        <input type="text" name="alert_emails"
                            value="{{ old('alert_emails', implode(', ', $company->alertEmails())) }}"
                            placeholder="email1@esempio.it, email2@esempio.it"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <p class="mt-1 text-xs text-gray-500">Separa più email con una virgola</p>
                    </div>
                    <x-btn type="submit" variant="primary">Salva Impostazioni Alert</x-btn>
                </div>
            </form>
        </div>
    </div>

    {{-- Users --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Utenti</h3>
            <x-btn href="{{ route('settings.users.create') }}" variant="primary" size="sm">+ Nuovo Utente</x-btn>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruolo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ultimo accesso</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $user->name }}
                                @if($user->id === auth()->id()) <span class="text-xs text-indigo-500">(tu)</span> @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php $roleColors = ['admin'=>'red','manager'=>'purple','warehouse'=>'blue','readonly'=>'gray']; @endphp
                                <x-badge :color="$roleColors[$user->role] ?? 'gray'" :text="$user->role_label"/>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$user->is_active ? 'green' : 'gray'" :text="$user->is_active ? 'Attivo' : 'Inattivo'"/>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Mai' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('settings.users.edit', $user) }}" class="text-indigo-600 hover:underline text-sm">Modifica</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('settings.users.destroy', $user) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Elimina utente {{ $user->name }}?')">Elimina</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
