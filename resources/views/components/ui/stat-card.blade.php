@props([
    'label',
    'value',
    'trend' => null,
    'trendUp' => true,
    'icon' => null,
    'color' => 'sigat',
])

@php
    $colors = [
        'sigat'   => ['bg' => 'bg-sigat-50', 'text' => 'text-sigat-600', 'icon' => 'bg-sigat-100 text-sigat-600'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'bg-emerald-100 text-emerald-600'],
        'amber'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'bg-amber-100 text-amber-600'],
        'rose'    => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'icon' => 'bg-rose-100 text-rose-600'],
        'sky'     => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'icon' => 'bg-sky-100 text-sky-600'],
    ];
    $c = $colors[$color] ?? $colors['sigat'];
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex items-start gap-4 hover:shadow-md hover:border-slate-300/80 transition-all duration-300 group">
    @if ($icon)
        <div class="flex items-center justify-center w-11 h-11 rounded-xl {{ $c['icon'] }} shrink-0 group-hover:scale-105 transition-transform duration-300">
            {{ $icon }}
        </div>
    @endif

    <div class="flex-1 min-w-0">
        <p class="text-sm text-slate-500 font-medium truncate">{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5 tracking-tight">{{ $value }}</p>
        @if ($trend)
            <p class="flex items-center gap-1 text-xs font-semibold mt-1.5 {{ $trendUp ? 'text-emerald-600' : 'text-rose-600' }}">
                @if ($trendUp)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
                @endif
                {{ $trend }}
            </p>
        @endif
    </div>
</div>
