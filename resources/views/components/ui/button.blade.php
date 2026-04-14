@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary'   => 'bg-sigat-600 text-white hover:bg-sigat-700 shadow-lg shadow-sigat-600/25 hover:shadow-sigat-700/30 focus:ring-sigat-500 active:scale-[0.98]',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 focus:ring-slate-400',
        'danger'    => 'bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 focus:ring-rose-400',
        'ghost'     => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-800 focus:ring-slate-400',
        'outline'   => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 hover:border-slate-400 focus:ring-sigat-500',
    ];

    $sizes = [
        'xs' => 'text-xs px-2.5 py-1.5 rounded-lg',
        'sm' => 'text-sm px-3.5 py-2',
        'md' => 'text-sm px-4 py-2.5',
        'lg' => 'text-base px-6 py-3',
    ];

    $iconOnly = [
        'xs' => 'p-1.5 rounded-lg',
        'sm' => 'p-2',
        'md' => 'p-2.5',
        'lg' => 'p-3',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($icon ? ($iconOnly[$size] ?? $iconOnly['md']) : ($sizes[$size] ?? $sizes['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
