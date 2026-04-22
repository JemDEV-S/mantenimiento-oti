<x-layouts.app title="Editar Campaña" moduleLabel="Mantenimiento" pageTitle="Editar Campaña">

    <x-ui.page-header
        :title="'Editar: ' . $campaign->name"
        :description="$campaign->code"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('campaigns.show', $campaign) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-6">

        {{-- Formulario principal --}}
        <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="max-w-2xl">
            @csrf @method('PUT')

            <div class="space-y-4">
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-sm font-semibold text-slate-900">Datos de la campaña</h3>
                    </x-slot:header>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-ui.input name="name" label="Nombre de la campaña" :value="old('name', $campaign->name)" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.textarea name="objective" label="Objetivo" :value="old('objective', $campaign->objective)" rows="2" />
                        </div>
                        <x-ui.select
                            name="coordinator_id"
                            label="Coordinador"
                            :value="old('coordinator_id', $campaign->coordinator_id)"
                            :options="$coordinators->pluck('full_name', 'id')->toArray()"
                        />
                        <x-ui.select
                            name="status"
                            label="Estado"
                            :value="old('status', $campaign->status->value)"
                            :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
                        />
                        <x-ui.input name="start_date" type="date" label="Fecha de inicio" :value="old('start_date', $campaign->start_date?->format('Y-m-d'))" />
                        <x-ui.input name="end_date" type="date" label="Fecha de fin estimada" :value="old('end_date', $campaign->end_date?->format('Y-m-d'))" />
                    </div>
                </x-ui.card>

                <div class="flex gap-3">
                    <x-ui.button type="submit">Guardar cambios</x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('campaigns.show', $campaign) }}">Cancelar</x-ui.button>
                </div>
            </div>
        </form>

        {{-- Sección de asignación masiva --}}
        @can('campaign.edit')
        @if (!in_array($campaign->status->value, ['completada', 'cancelada']))
        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Asignación masiva de activos</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Agregar activo individual --}}
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
                            label="Técnico"
                            :value="old('assigned_technician_id')"
                            :options="$technicians->pluck('full_name', 'id')->toArray()"
                            placeholder="Sin técnico"
                        />
                        <x-ui.input name="scheduled_date" type="date" label="Fecha programada" :value="old('scheduled_date')" />
                        <x-ui.button type="submit" class="w-full justify-center" size="sm">Agregar activo</x-ui.button>
                    </form>
                </x-ui.card>

                {{-- Bulk: agregar por unidad --}}
                <x-ui.card>
                    <x-slot:header>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Agregar por unidad</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Todos los activos de una unidad a un técnico</p>
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

                {{-- Bulk: crear casos masivos --}}
                <x-ui.card>
                    <x-slot:header>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Crear casos masivos</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Un caso por cada activo sin caso asignado</p>
                        </div>
                    </x-slot:header>
                    @php $pendingCasesEdit = $campaign->campaignAssets->whereNull('maintenance_case_id')->count(); @endphp
                    @if ($pendingCasesEdit > 0)
                    <form method="POST" action="{{ route('campaigns.cases.bulk-create', $campaign) }}" class="space-y-3"
                          onsubmit="return confirm('¿Crear casos para los {{ $pendingCasesEdit }} activo(s) sin caso?')">
                        @csrf
                        <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                            <span class="text-xs text-amber-700">{{ $pendingCasesEdit }} activo(s) sin caso</span>
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
                            label="Tipo"
                            :value="old('maintenance_type', 'preventivo')"
                            :options="collect($maintenanceTypes)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                        />
                        <x-ui.select
                            name="priority"
                            label="Prioridad"
                            :value="old('priority', 'media')"
                            :options="collect($priorities)->mapWithKeys(fn($p) => [$p->value => $p->label()])->toArray()"
                        />
                        <x-ui.textarea name="problem_description" label="Descripción" rows="2"
                            :value="old('problem_description')" placeholder="Descripción para todos los casos..." />
                        <x-ui.button type="submit" class="w-full justify-center" size="sm">
                            Crear {{ $pendingCasesEdit }} caso(s)
                        </x-ui.button>
                    </form>
                    @else
                    <div class="flex items-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs text-emerald-700">Todos los activos tienen caso asignado.</span>
                    </div>
                    @endif
                </x-ui.card>

            </div>
        </div>

        {{-- Tabla de activos en la campaña --}}
        @if ($campaign->campaignAssets->count() > 0)
        <x-ui.data-table>
            <x-slot:toolbar>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Activos en campaña</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $campaign->campaignAssets->count() }} activo(s)</p>
                </div>
            </x-slot:toolbar>
            <x-slot:head>
                <x-ui.th>Activo</x-ui.th>
                <x-ui.th>Técnico</x-ui.th>
                <x-ui.th>Estado</x-ui.th>
                <x-ui.th>Caso</x-ui.th>
                <x-ui.th></x-ui.th>
            </x-slot:head>
            @foreach ($campaign->campaignAssets as $ca)
            @php
                $caTone = match($ca->status) {
                    'pendiente' => 'neutral', 'programado' => 'info',
                    'atendido' => 'success', 'omitido' => 'danger', default => 'neutral',
                };
                $caLabel = match($ca->status) {
                    'pendiente' => 'Pendiente', 'programado' => 'Programado',
                    'atendido' => 'Atendido', 'omitido' => 'Omitido', default => $ca->status,
                };
            @endphp
            <tr class="hover:bg-slate-50/50 transition-colors">
                <x-ui.td>
                    <div>
                        <p class="font-medium text-slate-900 text-sm">{{ $ca->asset->name }}</p>
                        <p class="text-xs text-slate-400">{{ $ca->asset->internal_code }}</p>
                    </div>
                </x-ui.td>
                <x-ui.td>
                    @if ($ca->assignedTechnician)
                        <span class="text-sm text-slate-600">{{ $ca->assignedTechnician->full_name }}</span>
                    @else
                        <span class="text-xs text-slate-400">—</span>
                    @endif
                </x-ui.td>
                <x-ui.td><x-ui.badge :tone="$caTone" :dot="true">{{ $caLabel }}</x-ui.badge></x-ui.td>
                <x-ui.td>
                    @if ($ca->maintenance_case_id)
                        <span class="text-xs text-emerald-600 font-medium">Con caso</span>
                    @else
                        <span class="text-xs text-slate-400">Sin caso</span>
                    @endif
                </x-ui.td>
                <x-ui.td>
                    <form method="POST" action="{{ route('campaigns.assets.remove', [$campaign, $ca->asset_id]) }}"
                          onsubmit="return confirm('¿Quitar este activo?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Quitar</x-ui.button>
                    </form>
                </x-ui.td>
            </tr>
            @endforeach
        </x-ui.data-table>
        @endif

        @endif
        @endcan

    </div>

</x-layouts.app>
