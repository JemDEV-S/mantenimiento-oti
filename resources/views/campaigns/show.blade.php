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
