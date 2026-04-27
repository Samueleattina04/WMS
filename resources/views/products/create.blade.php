<x-app-layout>
    <x-slot:title>Nuovo Prodotto</x-slot>

    <x-page-header title="Nuovo Prodotto" subtitle="Aggiungi un prodotto al catalogo">
        <x-slot:actions>
            <x-btn href="{{ route('products.index') }}" variant="secondary">Annulla</x-btn>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('products._form')
        <div class="mt-6 flex justify-end gap-3">
            <x-btn href="{{ route('products.index') }}" variant="secondary">Annulla</x-btn>
            <x-btn type="submit" variant="primary">Salva Prodotto</x-btn>
        </div>
    </form>
</x-app-layout>
