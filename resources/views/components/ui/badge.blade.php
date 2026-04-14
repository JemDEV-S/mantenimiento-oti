@props([
    'tone' => 'neutral',
    'size' => 'md',
    'dot' => false,
])

@php
    $tones = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
        'danger'  => 'bg-rose-50 text-rose-700 border-rose-200/60',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200/60',
        'info'    => 'bg-sky-50 text-sky-700 border-sky-200/60',
        'neutral' => 'bg-slate-100 text-slate-600 border-slate-200/60',
        'primary' => 'bg-sigat-50 text-sigat-700 border-sigat-200/60',
    ];

    $dots = [
        'success' => 'bg-emerald-500',
        'danger'  => 'bg-rose-500',
        'warning' => 'bg-amber-500',
        'info'    => 'bg-sky-500',
        'neutral' => 'bg-slate-400',
        'primary' => 'bg-sigat-500',
    ];

    $sizes = [
        'sm' => 'text-[11px] px-2 py-0.5',
        'md' => 'text-xs px-2.5 py-1',
    ];

    $classes = 'inline-flex items-center gap-1.5 font-semibold rounded-full border ' . ($tones[$tone] ?? $tones['neutral']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dots[$tone] ?? $dots['neutral'] }}"></span>
    @endif
    {{ $slot }}
</span>
