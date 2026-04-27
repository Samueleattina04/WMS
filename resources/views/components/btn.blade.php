@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])
@php
$variants = [
    'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white border-transparent',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white border-transparent',
    'success' => 'bg-green-600 hover:bg-green-700 text-white border-transparent',
    'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white border-transparent',
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];
$classes = 'inline-flex items-center gap-2 font-medium border rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed ' .
    ($variants[$variant] ?? $variants['primary']) . ' ' .
    ($sizes[$size] ?? $sizes['md']);
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
