<x-app-layout>
    <x-slot:title>Nuovo Movimento</x-slot:title>

    <x-page-header title="Nuovo Movimento" subtitle="Registra un movimento di magazzino">
        <x-slot:actions>
            <x-btn :href="route('movements.index')" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6"
             x-data="{
                type: '{{ old('type', '') }}',
                needsFrom: ['outgoing','transfer','damaged'],
                needsTo: ['incoming','transfer','return'],
                get showFrom() { return this.needsFrom.includes(this.type); },
                get showTo() { return this.needsTo.includes(this.type); },
                get showLot() { return ['incoming','return'].includes(this.type); },
             }">
            <form action="{{ route('movements.store') }}" method="POST">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prodotto <span class="text-red-500">*</span></label>
                        <select name="product_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $errors->has('product_id') ? 'border-red-300' : '' }}">
                            <option value="">-- Seleziona prodotto --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo movimento <span class="text-red-500">*</span></label>
                        <select name="type" x-model="type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $errors->has('type') ? 'border-red-300' : '' }}">
                            <option value="">-- Seleziona tipo --</option>
                            <option value="incoming" {{ old('type') === 'incoming' ? 'selected' : '' }}>Entrata</option>
                            <option value="outgoing" {{ old('type') === 'outgoing' ? 'selected' : '' }}>Uscita</option>
                            <option value="transfer" {{ old('type') === 'transfer' ? 'selected' : '' }}>Trasferimento</option>
                            <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Rettifica</option>
                            <option value="return" {{ old('type') === 'return' ? 'selected' : '' }}>Reso</option>
                            <option value="damaged" {{ old('type') === 'damaged' ? 'selected' : '' }}>Danneggiato</option>
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <x-form-input label="Quantità" name="quantity" type="number" :required="true" min="1" :value="old('quantity')" />

                    {{-- Slot Da --}}
                    <div x-show="showFrom" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slot di provenienza</label>
                        <select name="slot_from_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Seleziona slot --</option>
                            @foreach($slots as $slot)
                                <option value="{{ $slot->id }}" {{ old('slot_from_id') == $slot->id ? 'selected' : '' }}>{{ $slot->full_path ?? $slot->code }}</option>
                            @endforeach
                        </select>
                        @error('slot_from_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slot A --}}
                    <div x-show="showTo" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slot di destinazione</label>
                        <select name="slot_to_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Seleziona slot --</option>
                            @foreach($slots as $slot)
                                <option value="{{ $slot->id }}" {{ old('slot_to_id') == $slot->id ? 'selected' : '' }}>{{ $slot->full_path ?? $slot->code }}</option>
                            @endforeach
                        </select>
                        @error('slot_to_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lot & Expiry --}}
                    <div x-show="showLot" x-cloak class="grid grid-cols-2 gap-4">
                        <x-form-input label="Numero lotto" name="lot_number" :value="old('lot_number')" />
                        <x-form-input label="Data scadenza" name="expiry_date" type="date" :value="old('expiry_date')" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Numero documento" name="document_number" :value="old('document_number')" />
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo documento</label>
                            <select name="document_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Nessuno --</option>
                                <option value="ddt" {{ old('document_type') === 'ddt' ? 'selected' : '' }}>DDT</option>
                                <option value="invoice" {{ old('document_type') === 'invoice' ? 'selected' : '' }}>Fattura</option>
                                <option value="order" {{ old('document_type') === 'order' ? 'selected' : '' }}>Ordine</option>
                                <option value="other" {{ old('document_type') === 'other' ? 'selected' : '' }}>Altro</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                    </div>

                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Registra Movimento</x-btn>
                    <x-btn :href="route('movements.index')" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
