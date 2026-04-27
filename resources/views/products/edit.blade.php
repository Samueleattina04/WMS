<x-app-layout>
    <x-slot:title>Modifica Prodotto</x-slot:title>

    <x-page-header title="Modifica Prodotto" :subtitle="$product->name">
        <x-slot:actions>
            <x-btn :href="route('products.show', $product)" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                @include('products._form', ['product' => $product])
                <div class="mt-6 flex items-center gap-3">
                    <x-btn type="submit" variant="primary">Salva Modifiche</x-btn>
                    <x-btn :href="route('products.show', $product)" variant="secondary">Annulla</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
