{{-- Logo --}}
<div class="flex items-center h-16 px-4 bg-gray-800 flex-shrink-0">
    @if(isset($currentCompany) && $currentCompany->logo)
        <img src="{{ Storage::url($currentCompany->logo) }}" class="h-8 w-auto mr-3" alt="Logo">
    @else
        <div class="w-8 h-8 bg-indigo-500 rounded mr-3 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
    @endif
    <span class="text-white font-bold text-base truncate">
        {{ isset($currentCompany) ? $currentCompany->name : 'WMS' }}
    </span>
</div>

{{-- Alert badge --}}
@php $alertCount = \App\Models\StockAlert::where('is_resolved', false)->count(); @endphp

{{-- Navigation --}}
<nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
    @php
        $navItems = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['route' => 'products.index', 'label' => 'Prodotti', 'icon' => 'box'],
            ['route' => 'movements.index', 'label' => 'Movimenti', 'icon' => 'arrows'],
            ['route' => 'warehouses.index', 'label' => 'Magazzino', 'icon' => 'warehouse'],
            ['route' => 'purchase-orders.index', 'label' => 'Ordini Acquisto', 'icon' => 'cart-down'],
            ['route' => 'sales-orders.index', 'label' => 'Ordini Vendita', 'icon' => 'cart-up'],
            ['route' => 'picking.index', 'label' => 'Picking', 'icon' => 'clipboard'],
            ['route' => 'inventory.index', 'label' => 'Inventario', 'icon' => 'search'],
            ['route' => 'alerts.index', 'label' => 'Alert', 'icon' => 'bell', 'badge' => $alertCount],
        ];
    @endphp

    @foreach($navItems as $item)
        @php $isActive = request()->routeIs(rtrim($item['route'], '.index') . '*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150
                  {{ $isActive ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            @include('layouts.icons.' . $item['icon'])
            <span class="ml-3">{{ $item['label'] }}</span>
            @if(isset($item['badge']) && $item['badge'] > 0)
                <span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $item['badge'] }}
                </span>
            @endif
        </a>
    @endforeach

    <div class="pt-4 mt-4 border-t border-gray-700">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Anagrafica</p>
        @foreach([
            ['route' => 'suppliers.index', 'label' => 'Fornitori', 'icon' => 'truck'],
            ['route' => 'customers.index', 'label' => 'Clienti', 'icon' => 'users'],
            ['route' => 'categories.index', 'label' => 'Categorie', 'icon' => 'tag'],
            ['route' => 'units.index', 'label' => 'Unità Misura', 'icon' => 'scale'],
        ] as $item)
            @php $isActive = request()->routeIs(rtrim($item['route'], '.index') . '*'); @endphp
            <a href="{{ route($item['route']) }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150
                      {{ $isActive ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                @include('layouts.icons.' . $item['icon'])
                <span class="ml-3">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="pt-4 mt-4 border-t border-gray-700">
        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Report</p>
        <a href="{{ route('reports.movements') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
            @include('layouts.icons.chart')
            <span class="ml-3">Movimenti</span>
        </a>
        <a href="{{ route('reports.stock-value') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
            @include('layouts.icons.currency')
            <span class="ml-3">Valore Stock</span>
        </a>
        <a href="{{ route('imports.index') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
            @include('layouts.icons.upload')
            <span class="ml-3">Import Excel</span>
        </a>
    </div>

    @if(auth()->user()->isAdmin())
        <div class="pt-4 mt-4 border-t border-gray-700">
            <a href="{{ route('settings.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('settings.*') ? 'bg-gray-700 text-white' : '' }}">
                @include('layouts.icons.cog')
                <span class="ml-3">Impostazioni</span>
            </a>
        </div>
    @endif
</nav>

{{-- User info --}}
<div class="flex-shrink-0 p-4 border-t border-gray-700">
    <div class="flex items-center">
        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center">
            <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
        <div class="ml-3 min-w-0 flex-1">
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400">{{ auth()->user()->role_label }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ml-2">
            @csrf
            <button type="submit" title="Esci" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</div>
