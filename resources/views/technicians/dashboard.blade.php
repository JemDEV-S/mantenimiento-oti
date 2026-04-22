@php
    $page = [
        'title'             => 'Mi Panel',
        'moduleLabel'       => 'Panel Técnico',
        'pageTitle'         => 'Mi Panel Técnico',
        'headerTitle'       => 'Mi Panel',
        'headerDescription' => 'Resumen de tu carga de trabajo y accesos rápidos.',
    ];
@endphp

<x-layouts.app :page="$page">

    <x-ui.page-header
        :title="$page['headerTitle']"
        :description="$page['headerDescription']"
    >
        <x-slot:actions>
            <x-ui.button size="sm" href="{{ route('tecnico.attend-asset') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/></svg>
                Atender equipo
            </x-ui.button>
            <x-ui.button variant="secondary" size="sm" href="{{ route('tecnico.work-queue') }}">
                Ver cola de trabajo
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if (! $employee)
    <x-ui.card class="mb-4">
        <div class="flex items-start gap-3 p-1">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
            <div>
                <p class="text-sm font-medium text-slate-800">Tu cuenta no tiene un perfil de empleado asociado.</p>
                <p class="text-xs text-slate-500 mt-0.5">Las métricas personales no están disponibles. Contacta al administrador.</p>
            </div>
        </div>
    </x-ui.card>
    @endif

    {{-- Stats personales --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-ui.stat-card label="Pendientes" :value="$myStats['pending']" color="neutral">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="En Progreso" :value="$myStats['in_progress']" color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/></svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="En Espera" :value="$myStats['waiting']" color="sigat">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
            </x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card label="Completados Hoy" :value="$myStats['done_today']" color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-ui.stat-card>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-12">

        {{-- Mis casos activos --}}
        <div class="xl:col-span-8">
            <x-ui.data-table>
                <x-slot:toolbar>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Mis casos activos</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Casos asignados pendientes de atención</p>
                    </div>
                    <x-ui.button variant="ghost" size="xs" href="{{ route('tecnico.work-queue') }}">Ver cola completa</x-ui.button>
                </x-slot:toolbar>

                <x-slot:head>
                    <x-ui.th>Código</x-ui.th>
                    <x-ui.th>Equipo</x-ui.th>
                    <x-ui.th>Ubicación</x-ui.th>
                    <x-ui.th>Estado</x-ui.th>
                    <x-ui.th>Prioridad</x-ui.th>
                </x-slot:head>

                @forelse ($myCases as $case)
                @php
                    $statusTone = match($case->status->value) {
                        'pendiente'   => 'neutral',
                        'en_progreso' => 'warning',
                        'en_espera'   => 'warning',
                        default       => 'neutral',
                    };
                    $priorityTone = match($case->priority->value) {
                        'baja'    => 'neutral',
                        'media'   => 'warning',
                        'alta'    => 'warning',
                        'critica' => 'danger',
                        default   => 'neutral',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <a href="{{ route('tecnico.cases.show', $case) }}"
                           class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md hover:bg-sigat-100 transition-colors">
                            {{ $case->code }}
                        </a>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">{{ $case->asset->name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $case->asset->internal_code ?? '' }}</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td>
                        <span class="text-sm text-slate-600">{{ $case->asset?->organizationalUnit?->name ?? '—' }}</span>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$statusTone" :dot="true">{{ $case->status->label() }}</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$priorityTone">{{ $case->priority->label() }}</x-ui.badge></x-ui.td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400 text-sm">No tienes casos activos asignados.</td>
                </tr>
                @endforelse
            </x-ui.data-table>
        </div>

        {{-- Panel derecho --}}
        <div class="space-y-4 xl:col-span-4">

            {{-- Accesos rápidos --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Accesos rápidos</h3>
                </x-slot:header>
                <div class="space-y-1">
                    <a href="{{ route('tecnico.attend-asset') }}"
                       class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-sigat-50 text-sigat-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Registrar atención</p>
                            <p class="text-xs text-slate-400">Flujo rápido de mantenimiento</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('assets.index') }}"
                       class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Buscar activo</p>
                            <p class="text-xs text-slate-400">Inventario de equipos</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('tecnico.work-queue') }}"
                       class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Cola de trabajo</p>
                            <p class="text-xs text-slate-400">Ver pendientes y en proceso</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </x-ui.card>

            {{-- Campañas en curso --}}
            @if ($activeCampaigns->isNotEmpty())
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Campañas en curso</h3>
                    <x-ui.badge tone="success" :dot="true">{{ $activeCampaigns->count() }} activa{{ $activeCampaigns->count() > 1 ? 's' : '' }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-2">
                    @foreach ($activeCampaigns as $campaign)
                    <a href="{{ route('campaigns.show', $campaign) }}"
                       class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900 truncate">{{ $campaign->name }}</p>
                            <p class="text-xs text-slate-400">{{ $campaign->code }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            {{-- Agentes desconectados --}}
            @if ($offlineAgents->isNotEmpty())
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Agentes sin señal</h3>
                    <x-ui.badge tone="danger" :dot="true">{{ $offlineAgents->count() }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-2">
                    @foreach ($offlineAgents as $agent)
                    <a href="{{ route('agents.show', $agent) }}"
                       class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900 truncate">{{ $agent->hostname ?? $agent->uuid }}</p>
                            <p class="text-xs text-slate-400">{{ $agent->asset?->name ?? 'Sin activo vinculado' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

        </div>
    </div>

</x-layouts.app>
