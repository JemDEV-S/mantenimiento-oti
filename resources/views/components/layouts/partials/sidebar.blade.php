{{-- Rail + Expandable Panel --}}
<aside class="fixed inset-y-0 left-0 z-40 flex" x-data>

    {{-- Icon Rail --}}
    <nav class="flex flex-col items-center w-[72px] bg-rail py-6 gap-1 border-r border-white/5">

        {{-- Brand --}}
        <a href="/" class="mb-6 flex items-center justify-center w-10 h-10 rounded-xl bg-sigat-600 text-white font-extrabold text-sm tracking-tight shadow-lg shadow-sigat-600/30">
            S
        </a>

        {{-- Module Icons --}}
        @php
            $modules = config('navigation.sidebar_modules', []);
            $submenus = config('navigation.sidebar_submenus', []);
        @endphp

        @foreach ($modules as $mod)
            <button
                @click="{{ $mod['route'] ? "\$store.sidebar.close(); window.location.href='" . route($mod['route']) . "'" : "\$store.sidebar.toggle('{$mod['key']}')" }}"
                :class="$store.sidebar.activeModule === '{{ $mod['key'] }}' ? 'bg-sigat-600/25 text-white border-sigat-400/40 shadow-sm shadow-sigat-900/20' : 'hover:text-white hover:bg-rail-hover border-transparent'"
                class="glow-indicator group relative flex items-center justify-center w-11 h-11 rounded-xl border text-white/85 transition-all duration-200"
                x-bind:class="{ 'active': $store.sidebar.activeModule === '{{ $mod['key'] }}' }"
                title="{{ $mod['label'] }}"
            >
                @include('components.icons.' . $mod['icon'])

                {{-- Tooltip --}}
                <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-slate-800 text-white text-xs font-medium whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 shadow-xl">
                    {{ $mod['label'] }}
                </span>
            </button>
        @endforeach

        {{-- Técnico (solo rol tecnico y admin) --}}
        @hasanyrole('tecnico|admin')
        <button
            @click="$store.sidebar.toggle('tecnico')"
            :class="$store.sidebar.activeModule === 'tecnico' ? 'bg-sigat-600/25 text-white border-sigat-400/40 shadow-sm shadow-sigat-900/20' : 'hover:text-white hover:bg-rail-hover border-transparent'"
            class="glow-indicator group relative flex items-center justify-center w-11 h-11 rounded-xl border text-white/85 transition-all duration-200"
            title="Panel Técnico"
        >
            @include('components.icons.clipboard')
            <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-slate-800 text-white text-xs font-medium whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 shadow-xl">
                Panel Técnico
            </span>
        </button>
        @endhasanyrole

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Settings --}}
        <button
            @click="$store.sidebar.toggle('settings')"
            :class="$store.sidebar.activeModule === 'settings' ? 'bg-sigat-600/25 text-white shadow-sm shadow-sigat-900/20' : 'hover:text-white hover:bg-rail-hover'"
            class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-white/85 transition-all duration-200"
            title="Configuracion"
        >
            @include('components.icons.cog')
            <span class="absolute left-full ml-3 px-2.5 py-1 rounded-lg bg-slate-800 text-white text-xs font-medium whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50">
                Configuracion
            </span>
        </button>
    </nav>

    {{-- Expandable Panel --}}
    <div
        x-show="$store.sidebar.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-4"
        class="w-[280px] bg-panel border-r border-panel-border py-6 px-4 overflow-y-auto"
        x-cloak
    >
        {{-- Panel Header --}}
        <div class="flex items-center justify-between mb-6 px-1">
            <h2 class="text-white font-semibold text-sm tracking-wide uppercase"
                x-text="$store.sidebar.activeModule"></h2>
            <button @click="$store.sidebar.close()" class="text-slate-500 hover:text-slate-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Submenu Items --}}
        @foreach ($submenus as $moduleKey => $items)
            <div x-show="$store.sidebar.activeModule === '{{ $moduleKey }}'" class="space-y-1" x-cloak>
                @foreach ($items as $item)
                    <a href="{{ route($item['route']) }}"
                       class="group flex flex-col gap-0.5 px-3 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-all duration-200">
                        <span class="font-medium text-sm">{{ $item['label'] }}</span>
                        <span class="text-xs text-slate-500 group-hover:text-slate-400">{{ $item['description'] }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach

        {{-- Técnico submenu --}}
        @hasanyrole('tecnico|admin')
        @php $technicianLinks = config('navigation.sidebar_technician', []); @endphp
        <div x-show="$store.sidebar.activeModule === 'tecnico'" class="space-y-1" x-cloak>
            @foreach ($technicianLinks as $item)
                <a href="{{ route($item['route']) }}"
                   class="group flex flex-col gap-0.5 px-3 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-all duration-200">
                    <span class="font-medium text-sm">{{ $item['label'] }}</span>
                    <span class="text-xs text-slate-500 group-hover:text-slate-400">{{ $item['description'] }}</span>
                </a>
            @endforeach
        </div>
        @endhasanyrole

        {{-- Settings submenu --}}
        <div x-show="$store.sidebar.activeModule === 'settings'" class="space-y-1" x-cloak>
            <a href="{{ route('settings.index') }}" class="group flex flex-col gap-0.5 px-3 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-all duration-200">
                <span class="font-medium text-sm">Configuracion</span>
                <span class="text-xs text-slate-500 group-hover:text-slate-400">Parametros del sistema</span>
            </a>
            <a href="{{ route('agents.index') }}" class="group flex flex-col gap-0.5 px-3 py-3 rounded-xl text-slate-300 hover:bg-white/5 hover:text-white transition-all duration-200">
                <span class="font-medium text-sm">Agentes</span>
                <span class="text-xs text-slate-500 group-hover:text-slate-400">Dispositivos de monitoreo</span>
            </a>
        </div>
    </div>
</aside>

{{-- Overlay to close panel on mobile --}}
<div
    x-show="$store.sidebar.open"
    @click="$store.sidebar.close()"
    class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm lg:hidden"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
></div>
