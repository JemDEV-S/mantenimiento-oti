@php
    $page = [
        'title' => 'Dashboard',
        'moduleLabel' => 'Panel Principal',
        'pageTitle' => 'Dashboard',
        'headerTitle' => 'Vista General',
        'headerDescription' => 'Resumen del estado actual de activos, mantenimiento y operaciones.',
    ];
@endphp

<x-layouts.app :page="$page">

    <x-ui.page-header
        :title="$page['headerTitle']"
        :description="$page['headerDescription']"
    >
        <x-slot:actions>
            @can('asset.create')
            <x-ui.button size="sm" href="{{ route('assets.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo activo
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <div>
            <x-ui.stat-card label="Total Activos" :value="number_format($stats['total_assets'])" color="sigat">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="En Reparación" :value="$stats['assets_in_repair']" :trend-up="false" color="amber">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="Casos Abiertos" :value="$stats['open_cases']" color="rose">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75a2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="Campañas Activas" :value="$stats['active_campaigns']" color="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="mt-2 grid grid-cols-1 gap-4 xl:grid-cols-12">

        {{-- Recent Maintenance Cases --}}
        <div class="xl:col-span-8">
            <x-ui.data-table>
                <x-slot:toolbar>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Casos Recientes</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Últimos casos de mantenimiento abiertos</p>
                    </div>
                    @can('maintenance-case.view')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.index') }}">Ver todos</x-ui.button>
                    @endcan
                </x-slot:toolbar>

                <x-slot:head>
                    <x-ui.th>Código</x-ui.th>
                    <x-ui.th>Activo</x-ui.th>
                    <x-ui.th>Tipo</x-ui.th>
                    <x-ui.th>Estado</x-ui.th>
                    <x-ui.th>Técnico</x-ui.th>
                </x-slot:head>

                @forelse ($recentCases as $case)
                @php
                    $typeTone = match($case->maintenance_type->value) {
                        'preventivo' => 'primary',
                        'correctivo' => 'info',
                        'predictivo' => 'neutral',
                        'emergencia' => 'danger',
                        default      => 'neutral',
                    };
                    $statusTone = match($case->status->value) {
                        'pendiente'   => 'neutral',
                        'en_progreso' => 'warning',
                        'en_espera'   => 'warning',
                        'completado'  => 'success',
                        'cancelado'   => 'danger',
                        default       => 'neutral',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <a href="{{ route('maintenance-cases.show', $case) }}" class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md hover:bg-sigat-100 transition-colors">
                            {{ $case->code }}
                        </a>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900">{{ $case->asset->name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $case->asset->internal_code ?? '' }}</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$typeTone" :dot="true">{{ $case->maintenance_type->label() }}</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$statusTone" :dot="true">{{ $case->status->label() }}</x-ui.badge></x-ui.td>
                    <x-ui.td>
                        @if ($case->assignedTechnician)
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$case->assignedTechnician->full_name" size="xs" />
                            <span class="text-sm text-slate-700">{{ $case->assignedTechnician->full_name }}</span>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">Sin asignar</span>
                        @endif
                    </x-ui.td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400 text-sm">No hay casos abiertos.</td>
                </tr>
                @endforelse
            </x-ui.data-table>
        </div>

        {{-- Stats sidebar --}}
        <div class="space-y-4 xl:col-span-4">

            {{-- Quick stats --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Técnicos activos</h3>
                    <x-ui.badge tone="success" :dot="true">{{ $stats['total_technicians'] }} disponibles</x-ui.badge>
                </x-slot:header>
                <p class="text-3xl font-bold text-slate-900">{{ $stats['total_technicians'] }}</p>
                <p class="text-sm text-slate-500 mt-1">Técnicos con acceso activo al sistema</p>
                @can('employee.view')
                <div class="mt-3">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('employees.index', ['is_technician' => 1]) }}">Ver técnicos →</x-ui.button>
                </div>
                @endcan
            </x-ui.card>

            {{-- Quick links --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Accesos rápidos</h3>
                </x-slot:header>
                <div class="space-y-1">
                    @can('asset.view')
                    <a href="{{ route('assets.index') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-sigat-50 text-sigat-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Inventario de activos</span>
                        <svg class="w-4 h-4 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endcan
                    @can('maintenance-case.create')
                    <a href="{{ route('maintenance-cases.create') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Nuevo caso</span>
                        <svg class="w-4 h-4 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endcan
                    @can('asset-movement.create')
                    <a href="{{ route('asset-movements.create') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Registrar movimiento</span>
                        <svg class="w-4 h-4 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endcan
                    @can('campaign.create')
                    <a href="{{ route('campaigns.create') }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Nueva campaña</span>
                        <svg class="w-4 h-4 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endcan
                </div>
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
