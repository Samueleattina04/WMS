<!DOCTYPE html>
<html lang="it" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'WMS' }} - {{ isset($currentCompany) ? $currentCompany->name : config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }">

{{-- Mobile sidebar overlay --}}
<div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" style="display:none;">
    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 opacity-75"></div>
    <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-gray-900 z-50">
        @include('layouts.sidebar')
    </div>
</div>

<div class="min-h-full flex">
    {{-- Desktop Sidebar --}}
    <div class="hidden lg:flex lg:flex-shrink-0">
        <div class="flex flex-col w-64 bg-gray-900">
            @include('layouts.sidebar')
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Top bar mobile --}}
        <div class="lg:hidden bg-gray-900 px-4 py-3 flex items-center justify-between">
            <button @click="sidebarOpen = true" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-white font-semibold text-sm">{{ isset($currentCompany) ? $currentCompany->name : 'WMS' }}</span>
            <div class="w-6"></div>
        </div>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            @include('components.flash-messages')
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
