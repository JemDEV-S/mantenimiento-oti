@php
    $page = $page ?? [];
    $actions = $actions ?? config('navigation.command_palette_actions', []);
@endphp

{{-- Command Palette (Ctrl+K) --}}
<div
    x-show="$store.commandPalette.open"
    x-trap.noscroll="$store.commandPalette.open"
    @keydown.escape.window="$store.commandPalette.close()"
    class="fixed inset-0 z-50 flex items-start justify-center pt-[15vh]"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        @click="$store.commandPalette.close()"
        x-show="$store.commandPalette.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
    ></div>

    {{-- Panel --}}
    <div
        x-show="$store.commandPalette.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-200/80 overflow-hidden"
        x-data="{ query: '' }"
    >
        {{-- Search Input --}}
        <div class="flex items-center gap-3 px-5 border-b border-slate-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input
                x-ref="searchInput"
                x-model="query"
                x-init="$watch('$store.commandPalette.open', val => { if(val) setTimeout(() => $refs.searchInput.focus(), 100) })"
                type="text"
                placeholder="Buscar activos, casos, empleados..."
                class="w-full py-4 text-sm bg-transparent border-0 outline-none focus:ring-0 placeholder-slate-400"
            >
            <kbd class="shrink-0 text-[10px] font-mono bg-slate-100 border border-slate-200 rounded-md px-1.5 py-0.5 text-slate-400">ESC</kbd>
        </div>

        {{-- Quick Actions --}}
        <div class="p-3 max-h-[320px] overflow-y-auto space-y-3">
            @if (! empty($page['pageTitle']) || ! empty($page['headerDescription']))
                <div class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-100">
                    @if (! empty($page['moduleLabel']))
                        <p class="text-[11px] font-semibold text-sigat-600 uppercase tracking-wider">{{ $page['moduleLabel'] }}</p>
                    @endif
                    @if (! empty($page['pageTitle']))
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $page['pageTitle'] }}</p>
                    @endif
                    @if (! empty($page['headerDescription']))
                        <p class="mt-1 text-xs text-slate-500">{{ $page['headerDescription'] }}</p>
                    @endif
                </div>
            @endif

            <p class="px-2 py-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Acciones rapidas</p>

            @foreach ($actions as $action)
                <a
                    href="{{ route($action['route']) }}"
                    x-show="!query || '{{ strtolower($action['label']) }}'.includes(query.toLowerCase())"
                    @click="$store.commandPalette.close()"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left hover:bg-sigat-50 text-slate-700 hover:text-sigat-700 transition-colors group"
                >
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-sigat-100 text-slate-500 group-hover:text-sigat-600 transition-colors shrink-0">
                        @include('components.icons.' . $action['icon'])
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $action['label'] }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $action['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="flex items-center gap-4 px-5 py-2.5 border-t border-slate-100 bg-slate-50/50 text-[11px] text-slate-400">
            <span class="flex items-center gap-1"><kbd class="font-mono bg-white border rounded px-1">Up/Down</kbd> navegar</span>
            <span class="flex items-center gap-1"><kbd class="font-mono bg-white border rounded px-1">Enter</kbd> abrir</span>
            <span class="flex items-center gap-1"><kbd class="font-mono bg-white border rounded px-1">esc</kbd> cerrar</span>
        </div>
    </div>
</div>
