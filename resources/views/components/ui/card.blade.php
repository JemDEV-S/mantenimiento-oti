@props([
    'padding' => true,
    'hover' => false,
])

@php
    $classes = 'bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-100/50';
    if ($padding) $classes .= ' p-6';
    if ($hover) $classes .= ' hover:shadow-md hover:border-slate-300/80 transition-all duration-300';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="flex items-center justify-between gap-4 mb-5">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-5 pt-4 border-t border-slate-100">
            {{ $footer }}
        </div>
    @endisset
</div>
