<x-layouts.app title="Detalle Campaña" moduleLabel="Mantenimiento" pageTitle="Detalle de Campaña">

    @php
        $statusTone = match($campaign->status->value) {
            'planificada' => 'neutral', 'en_curso' => 'info',
            'pausada' => 'warning', 'completada' => 'success', 'cancelada' => 'danger',
            default => 'neutral',
        };
        $totalAssets   = $campaign->campaignAssets->count();
        $atendidos     = $campaign->campaignAssets->where('status', 'atendido')->count();
        $progress      = $totalAssets > 0 ? round(($atendidos / $totalAssets) * 100) : 0;
    @endphp

    <x-ui.page-header
        :title="$campaign->name"
        :description="$campaign->code . ' — ' . $campaign->status->label()"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('campaigns.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('campaign.edit')
            <x-ui.button variant="secondary" size="sm" href="{{ route('campaigns.edit', $campaign) }}">Editar</x-ui.button>
            @endcan
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Acta de mantenimiento
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute right-0 top-full mt-1 z-30 w-72 rounded-xl border border-slate-200 bg-white shadow-lg p-3">
                    <p class="text-xs text-slate-500 mb-2 font-medium">Seleccionar unidad orgánica</p>
                    <form method="POST" action="{{ route('campaigns.generate-acta', $campaign) }}">
                        @csrf
                        <select name="unit_id" required
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2">
                            <option value="">-- Elegir unidad --</option>
                            @foreach ($organizationalUnits as $ou)
                            <option value="{{ $ou->id }}">{{ $ou->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="w-full rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                            Generar y descargar PDF
                        </button>
                    </form>
                </div>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Info sidebar --}}
        <div class="xl:col-span-1 space-y-4">

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información</h3>
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $campaign->status->label() }}</x-ui.badge>
                </x-slot:header>

                <div class="space-y-3">
                    @if ($campaign->objective)
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Objetivo</p>
                        <p class="text-sm text-slate-700">{{ $campaign->objective }}</p>
                    </div>
                    @endif

                    @if ($campaign->coordinator)
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-2">Coordinador</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$campaign->coordinator->full_name" size="sm" />
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $campaign->coordinator->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $campaign->coordinator->position ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-slate-500">Inicio</p>
                            <p class="text-sm font-medium text-slate-800">{{ $campaign->start_date?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Fin estimado</p>
                            <p class="text-sm font-medium text-slate-800">{{ $campaign->end_date?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Progress --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Progreso</h3>
                    <span class="text-sm font-bold text-slate-700">{{ $progress }}%</span>
                </x-slot:header>
                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-sigat-500 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                    @php
                        $statusCounts = [
                            'pendiente'  => ['label' => 'Pendientes', 'tone' => 'neutral'],
                            'programado' => ['label' => 'Programados', 'tone' => 'info'],
                            'atendido'   => ['label' => 'Atendidos', 'tone' => 'success'],
                            'omitido'    => ['label' => 'Omitidos', 'tone' => 'danger'],
                        ];
                    @endphp
                    @foreach ($statusCounts as $statusVal => $info)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                        <span class="text-slate-500 text-xs">{{ $info['label'] }}</span>
                        <span class="font-semibold text-slate-800">{{ $campaign->campaignAssets->where('status', $statusVal)->count() }}</span>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            {{-- Agregar activo --}}
            @can('campaign.edit')
            @if (!in_array($campaign->status->value, ['completada', 'cancelada']))
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Agregar activo</h3>
                </x-slot:header>
                <form method="POST" action="{{ route('campaigns.assets.add', $campaign) }}" class="space-y-3">
                    @csrf
                    <x-ui.select
                        name="asset_id"
                        label="Activo"
                        :value="old('asset_id')"
                        :options="$availableAssets->pluck('name', 'id')->toArray()"
                        placeholder="Seleccionar activo..."
                    />
                    <x-ui.select
                        name="assigned_technician_id"
                        label="Técnico asignado"
                        :value="old('assigned_technician_id')"
                        :options="$technicians->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin técnico"
                    />
                    <x-ui.input name="scheduled_date" type="date" label="Fecha programada" :value="old('scheduled_date')" />
                    <x-ui.button type="submit" class="w-full justify-center" size="sm">Agregar a campaña</x-ui.button>
                </form>
            </x-ui.card>

            {{-- Bulk: agregar por unidad --}}
            <x-ui.card>
                <x-slot:header>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Agregar activos por unidad</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Agrega todos los activos de una unidad a un solo técnico</p>
                    </div>
                </x-slot:header>
                <form method="POST" action="{{ route('campaigns.assets.bulk-unit', $campaign) }}" class="space-y-3">
                    @csrf
                    <x-ui.select
                        name="unit_id"
                        label="Unidad organizacional"
                        :value="old('unit_id')"
                        :options="$organizationalUnits->pluck('name', 'id')->toArray()"
                        placeholder="Seleccionar unidad..."
                    />
                    <x-ui.select
                        name="assigned_technician_id"
                        label="Técnico asignado"
                        :value="old('assigned_technician_id')"
                        :options="$technicians->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin técnico"
                    />
                    <x-ui.input name="scheduled_date" type="date" label="Fecha programada" :value="old('scheduled_date')" />
                    <x-ui.button type="submit" class="w-full justify-center" size="sm" variant="secondary">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Agregar todos por unidad
                    </x-ui.button>
                </form>
            </x-ui.card>

            {{-- Bulk: crear casos para todos los activos --}}
            <x-ui.card>
                <x-slot:header>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Crear casos masivos</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Crea casos para todos los activos sin caso asignado</p>
                    </div>
                </x-slot:header>
                @php $pendingCases = $campaign->campaignAssets->whereNull('maintenance_case_id')->count(); @endphp
                @if ($pendingCases > 0)
                <form method="POST" action="{{ route('campaigns.cases.bulk-create', $campaign) }}" class="space-y-3"
                      onsubmit="return confirm('¿Crear casos de mantenimiento para los {{ $pendingCases }} activo(s) sin caso?')">
                    @csrf
                    <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                        <span class="text-xs text-amber-700">{{ $pendingCases }} activo(s) sin caso de mantenimiento</span>
                    </div>
                    <x-ui.select
                        name="assigned_technician_id"
                        label="Técnico responsable"
                        :value="old('assigned_technician_id')"
                        :options="$technicians->pluck('full_name', 'id')->toArray()"
                        placeholder="Seleccionar técnico..."
                    />
                    <x-ui.select
                        name="maintenance_type"
                        label="Tipo de mantenimiento"
                        :value="old('maintenance_type', 'preventivo')"
                        :options="collect($maintenanceTypes)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                    />
                    <x-ui.select
                        name="priority"
                        label="Prioridad"
                        :value="old('priority', 'media')"
                        :options="collect($priorities)->mapWithKeys(fn($p) => [$p->value => $p->label()])->toArray()"
                    />
                    <x-ui.textarea name="problem_description" label="Descripción del problema" rows="2"
                        :value="old('problem_description')" placeholder="Descripción general para todos los casos..." />
                    <x-ui.button type="submit" class="w-full justify-center" size="sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                        Crear {{ $pendingCases }} caso(s)
                    </x-ui.button>
                </form>
                @else
                <div class="flex items-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs text-emerald-700">Todos los activos tienen caso asignado.</span>
                </div>
                @endif
            </x-ui.card>
            @endif
            @endcan
        </div>

        {{-- Activos de la campaña --}}
        <div class="xl:col-span-2">
            <x-ui.data-table>
                <x-slot:toolbar>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Activos en campaña</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $totalAssets }} activo(s)</p>
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <x-ui.th>Activo</x-ui.th>
                    <x-ui.th>Técnico</x-ui.th>
                    <x-ui.th>Fecha prog.</x-ui.th>
                    <x-ui.th>Estado</x-ui.th>
                    <x-ui.th>Caso</x-ui.th>
                    <x-ui.th></x-ui.th>
                </x-slot:head>

                @forelse ($campaign->campaignAssets as $ca)
                @php
                    $caTone = match($ca->status) {
                        'pendiente' => 'neutral', 'programado' => 'info',
                        'atendido' => 'success', 'omitido' => 'danger', default => 'neutral',
                    };
                    $caStatusLabel = match($ca->status) {
                        'pendiente' => 'Pendiente', 'programado' => 'Programado',
                        'atendido' => 'Atendido', 'omitido' => 'Omitido', default => $ca->status,
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <div>
                            @can('asset.view')
                            <a href="{{ route('assets.show', $ca->asset) }}" class="font-medium text-slate-900 hover:text-sigat-600 text-sm">{{ $ca->asset->name }}</a>
                            @else
                            <span class="font-medium text-slate-900 text-sm">{{ $ca->asset->name }}</span>
                            @endcan
                            <p class="text-xs text-slate-400">{{ $ca->asset->internal_code }}</p>
                        </div>
                    </x-ui.td>
                    <x-ui.td>
                        @if ($ca->assignedTechnician)
                            <div class="flex items-center gap-1.5">
                                <x-ui.avatar :name="$ca->assignedTechnician->full_name" size="xs" />
                                <span class="text-sm text-slate-600">{{ $ca->assignedTechnician->full_name }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </x-ui.td>
                    <x-ui.td>
                        <span class="text-sm text-slate-600">{{ $ca->scheduled_date?->format('d/m/Y') ?? '—' }}</span>
                    </x-ui.td>
                    <x-ui.td>
                        <x-ui.badge :tone="$caTone" :dot="true">{{ $caStatusLabel }}</x-ui.badge>
                    </x-ui.td>
                    <x-ui.td>
                        @if ($ca->maintenance_case_id)
                            @can('maintenance-case.view')
                            <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.show', $ca->maintenance_case_id) }}">Ver caso</x-ui.button>
                            @endcan
                        @else
                            @can('maintenance-case.create')
                            <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.create', ['asset_id' => $ca->asset_id, 'campaign_id' => $campaign->id]) }}">
                                Crear caso
                            </x-ui.button>
                            @endcan
                        @endif
                    </x-ui.td>
                    <x-ui.td>
                        @can('campaign.edit')
                        @if (!in_array($campaign->status->value, ['completada', 'cancelada']))
                        <form method="POST" action="{{ route('campaigns.assets.remove', [$campaign, $ca->asset_id]) }}"
                              onsubmit="return confirm('¿Quitar este activo de la campaña?')" class="inline">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Quitar</x-ui.button>
                        </form>
                        @endif
                        @endcan
                    </x-ui.td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                        Sin activos en esta campaña. Agrega activos desde el panel izquierdo.
                    </td>
                </tr>
                @endforelse
            </x-ui.data-table>
        </div>
    </div>

</x-layouts.app>
