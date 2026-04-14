@props([
    'href' => null,
    'danger' => false,
])

@php
    $classes = 'flex items-center gap-2.5 w-full px-4 py-2.5 text-sm transition-colors text-left ';
    $classes .= $danger
        ? 'text-rose-600 hover:bg-rose-50'
        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
