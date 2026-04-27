@props(['title', 'value', 'icon', 'color' => 'indigo', 'link' => null, 'subtitle' => null])
@php
$colors = [
    'indigo' => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50'],
    'green' => ['bg' => 'bg-green-500', 'light' => 'bg-green-50'],
    'yellow' => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50'],
    'red' => ['bg' => 'bg-red-500', 'light' => 'bg-red-50'],
    'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50'],
    'purple' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50'],
];
$c = $colors[$color] ?? $colors['indigo'];
@endphp
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 {{ $link ? 'hover:shadow-md transition-shadow' : '' }}">
    @if($link)
        <a href="{{ $link }}" class="block">
    @endif
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 {{ $c['bg'] }} rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        </div>
        <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs text-gray-400 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @if($link)
        </a>
    @endif
</div>
