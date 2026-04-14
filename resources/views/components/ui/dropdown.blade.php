@props([
    'align' => 'right',
    'width' => '48',
])

@php
    $alignments = [
        'left' => 'left-0',
        'right' => 'right-0',
    ];
    $widths = [
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
    ];
@endphp

<div x-data="{ open: false }" class="relative inline-block">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-40 mt-2 {{ $alignments[$align] ?? 'right-0' }} {{ $widths[$width] ?? 'w-48' }} bg-white rounded-xl shadow-xl shadow-slate-200/60 border border-slate-200/80 py-1.5"
        x-cloak
    >
        {{ $slot }}
    </div>
</div>
