@props([
    'name' => '?',
    'size' => 'md',
    'src' => null,
])

@php
    $sizes = [
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-9 h-9 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $colors = ['bg-sigat-100 text-sigat-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-sky-100 text-sky-700', 'bg-rose-100 text-rose-700', 'bg-violet-100 text-violet-700'];
    $colorIndex = ord(strtoupper(substr($name, 0, 1))) % count($colors);
    $color = $colors[$colorIndex];

    $initials = collect(explode(' ', $name))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" {{ $attributes->merge(['class' => "rounded-full object-cover {$sizeClass}"]) }}>
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full font-semibold shrink-0 {$sizeClass} {$color}"]) }}>
        {{ $initials }}
    </span>
@endif
