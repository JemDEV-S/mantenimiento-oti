<x-layouts.app title="Dashboard" moduleLabel="Panel Principal" pageTitle="Dashboard">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Vista General"
        description="Resumen del estado actual de activos, mantenimiento y operaciones."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Exportar
            </x-ui.button>
            <x-ui.button size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo activo
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <div>
            <x-ui.stat-card label="Total Activos" value="1,247" trend="+12 este mes" :trend-up="true" color="sigat">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="En Mantenimiento" value="23" trend="5 urgentes" :trend-up="false" color="amber">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="Campanas Activas" value="3" trend="87% progreso" :trend-up="true" color="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
        <div>
            <x-ui.stat-card label="Movimientos Hoy" value="8" color="sky">
                <x-slot:icon>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                </x-slot:icon>
            </x-ui.stat-card>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="mt-2 grid grid-cols-1 gap-4 xl:grid-cols-12">

        {{-- Recent Maintenance Cases (8 cols) --}}
        <div class="xl:col-span-8">
            <x-ui.data-table>
                <x-slot:toolbar>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Casos Recientes</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ultimos casos de mantenimiento registrados</p>
                    </div>
                    <x-ui.button variant="ghost" size="xs" href="#">Ver todos</x-ui.button>
                </x-slot:toolbar>

                <x-slot:head>
                    <x-ui.th>Codigo</x-ui.th>
                    <x-ui.th>Activo</x-ui.th>
                    <x-ui.th>Tipo</x-ui.th>
                    <x-ui.th>Estado</x-ui.th>
                    <x-ui.th>Tecnico</x-ui.th>
                </x-slot:head>

                {{-- Sample rows --}}
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <span class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md">MC-2024-0147</span>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900">Laptop HP ProBook 450</p>
                            <p class="text-xs text-slate-400">ACT-00234</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge tone="info" :dot="true">Correctivo</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge tone="warning" :dot="true">En progreso</x-ui.badge></x-ui.td>
                    <x-ui.td>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar name="Carlos Ruiz" size="xs" />
                            <span class="text-sm">C. Ruiz</span>
                        </div>
                    </x-ui.td>
                </tr>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <span class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md">MC-2024-0146</span>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900">Impresora Epson L3250</p>
                            <p class="text-xs text-slate-400">ACT-00189</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge tone="primary" :dot="true">Preventivo</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge tone="success" :dot="true">Completado</x-ui.badge></x-ui.td>
                    <x-ui.td>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar name="Maria Lopez" size="xs" />
                            <span class="text-sm">M. Lopez</span>
                        </div>
                    </x-ui.td>
                </tr>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <span class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md">MC-2024-0145</span>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900">Monitor LG 24MK430H</p>
                            <p class="text-xs text-slate-400">ACT-00412</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge tone="danger" :dot="true">Emergencia</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge tone="neutral" :dot="true">Registrado</x-ui.badge></x-ui.td>
                    <x-ui.td>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar name="Pedro Diaz" size="xs" />
                            <span class="text-sm">P. Diaz</span>
                        </div>
                    </x-ui.td>
                </tr>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <span class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md">MC-2024-0144</span>
                    </x-ui.td>
                    <x-ui.td>
                        <div>
                            <p class="font-medium text-slate-900">PC Desktop Dell OptiPlex</p>
                            <p class="text-xs text-slate-400">ACT-00098</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge tone="neutral" :dot="true">Diagnostico</x-ui.badge></x-ui.td>
                    <x-ui.td><x-ui.badge tone="warning" :dot="true">En progreso</x-ui.badge></x-ui.td>
                    <x-ui.td>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar name="Carlos Ruiz" size="xs" />
                            <span class="text-sm">C. Ruiz</span>
                        </div>
                    </x-ui.td>
                </tr>
            </x-ui.data-table>
        </div>

        {{-- Sidebar Cards (4 cols) --}}
        <div class="space-y-4 xl:col-span-4">

            {{-- Active Campaigns --}}
            <x-ui.card :hover="true">
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Campanas Activas</h3>
                    <x-ui.badge tone="success" :dot="true">3 activas</x-ui.badge>
                </x-slot:header>

                <div class="space-y-3">
                    {{-- Campaign item --}}
                    <div class="group flex items-center gap-3 p-3 -mx-1 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">Preventivo Q1-2024</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: 87%"></div>
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium">87%</span>
                            </div>
                        </div>
                    </div>

                    <div class="group flex items-center gap-3 p-3 -mx-1 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">Revision Impresoras</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-500 rounded-full" style="width: 45%"></div>
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium">45%</span>
                            </div>
                        </div>
                    </div>

                    <div class="group flex items-center gap-3 p-3 -mx-1 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">Actualizacion Software</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-sky-500 rounded-full" style="width: 22%"></div>
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium">22%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Quick Stats Distribution --}}
            <x-ui.card :hover="true">
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado de Activos</h3>
                </x-slot:header>

                <div class="space-y-3">
                    @php
                        $statuses = [
                            ['label' => 'Disponible', 'count' => 892, 'pct' => 72, 'color' => 'bg-emerald-500'],
                            ['label' => 'Asignado', 'count' => 278, 'pct' => 22, 'color' => 'bg-sigat-500'],
                            ['label' => 'En mantenimiento', 'count' => 23, 'pct' => 2, 'color' => 'bg-amber-500'],
                            ['label' => 'De baja', 'count' => 54, 'pct' => 4, 'color' => 'bg-slate-400'],
                        ];
                    @endphp

                    {{-- Horizontal stacked bar --}}
                    <div class="flex h-3 rounded-full overflow-hidden bg-slate-100 gap-0.5">
                        @foreach ($statuses as $s)
                            <div class="{{ $s['color'] }} rounded-full transition-all duration-500" style="width: {{ $s['pct'] }}%"></div>
                        @endforeach
                    </div>

                    @foreach ($statuses as $s)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $s['color'] }}"></span>
                                <span class="text-slate-600">{{ $s['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-800">{{ $s['count'] }}</span>
                                <span class="text-xs text-slate-400">{{ $s['pct'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        {{-- Recent Movements (6 cols) --}}
        <div class="xl:col-span-6">
            <x-ui.card :hover="true">
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Movimientos Recientes</h3>
                    <x-ui.button variant="ghost" size="xs" href="#">Ver historial</x-ui.button>
                </x-slot:header>

                <div class="space-y-1">
                    @php
                        $movements = [
                            ['type' => 'Asignacion', 'asset' => 'Laptop Dell Latitude 5520', 'to' => 'Ana Torres - Subgerencia TI', 'time' => 'Hace 2h', 'tone' => 'success'],
                            ['type' => 'Transferencia', 'asset' => 'Impresora HP LaserJet', 'to' => 'Gerencia Admin. → Gerencia RRHH', 'time' => 'Hace 4h', 'tone' => 'info'],
                            ['type' => 'Devolucion', 'asset' => 'Monitor Samsung 27"', 'to' => 'Almacen central', 'time' => 'Ayer', 'tone' => 'warning'],
                            ['type' => 'Baja', 'asset' => 'Teclado Logitech K120', 'to' => 'Dado de baja por deterioro', 'time' => 'Ayer', 'tone' => 'danger'],
                        ];
                    @endphp

                    @foreach ($movements as $m)
                        <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <x-ui.badge tone="{{ $m['tone'] }}" size="sm">{{ $m['type'] }}</x-ui.badge>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $m['asset'] }}</p>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $m['to'] }}</p>
                            </div>
                            <span class="text-xs text-slate-400 shrink-0">{{ $m['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        {{-- Technicians --}}
        <div class="xl:col-span-6">
            <x-ui.card :hover="true">
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Tecnicos</h3>
                    <x-ui.badge tone="neutral">4 activos</x-ui.badge>
                </x-slot:header>

                <div class="space-y-3">
                    @php
                        $techs = [
                            ['name' => 'Carlos Ruiz', 'specialty' => 'Hardware', 'cases' => 5, 'status' => 'Disponible', 'tone' => 'success'],
                            ['name' => 'Maria Lopez', 'specialty' => 'Redes', 'cases' => 3, 'status' => 'Ocupado', 'tone' => 'warning'],
                            ['name' => 'Pedro Diaz', 'specialty' => 'Software', 'cases' => 7, 'status' => 'Disponible', 'tone' => 'success'],
                            ['name' => 'Luis Fernandez', 'specialty' => 'Impresoras', 'cases' => 2, 'status' => 'Ocupado', 'tone' => 'warning'],
                        ];
                    @endphp

                    @foreach ($techs as $tech)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <x-ui.avatar :name="$tech['name']" size="sm" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $tech['name'] }}</p>
                                <p class="text-xs text-slate-400">{{ $tech['specialty'] }} &middot; {{ $tech['cases'] }} casos</p>
                            </div>
                            <x-ui.badge :tone="$tech['tone']" size="sm" :dot="true">{{ $tech['status'] }}</x-ui.badge>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
