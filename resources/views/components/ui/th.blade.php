@props(['sortable' => false, 'sorted' => null])

<th {{ $attributes->merge(['class' => 'px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap']) }}>
    @if ($sortable)
        <button class="inline-flex items-center gap-1 group">
            {{ $slot }}
            <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
