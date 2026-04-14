<x-layouts.app title="Detalle Activo" moduleLabel="Activos" pageTitle="Detalle de Activo">

    @php
        $statusTone = match($asset->status->value) {
            'activo' => 'success', 'en_uso' => 'info', 'en_almacen' => 'neutral',
            'en_reparacion' => 'warning', 'dado_de_baja' => 'danger', 'extraviado' => 'danger',
            default => 'neutral',
        };
        $conditionTone = match($asset->condition->value) {
            'bueno' => 'success', 'regular' => 'warning', 'malo' => 'danger', 'obsoleto' => 'neutral',
            default => 'neutral',
        };
    @endphp

    <x-ui.page-header
        :title="$asset->name"
        :description="$asset->asset_type->label() . ($asset->brand ? ' — ' . $asset->brand . ($asset->model ? ' ' . $asset->model : '') : '')"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('assets.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('asset-movement.create')
            <x-ui.button variant="secondary" size="sm" href="{{ route('asset-movements.create', ['asset_id' => $asset->id]) }}">Registrar movimiento</x-ui.button>
            @endcan
            @can('maintenance-case.create')
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.create', ['asset_id' => $asset->id]) }}">Nuevo caso</x-ui.button>
            @endcan
            @can('asset.edit')
            <x-ui.button size="sm" href="{{ route('assets.edit', $asset) }}">Editar</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Left column --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Estado del activo --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado del activo</h3>
                </x-slot:header>

                <div class="flex gap-2 mb-4">
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $asset->status->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$conditionTone">{{ $asset->condition->label() }}</x-ui.badge>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Código interno</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $asset->internal_code }}</span>
                    </div>
                    @if ($asset->patrimonial_code)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Código patrimonial</span>
                        <span class="font-mono font-medium text-slate-800">{{ $asset->patrimonial_code }}</span>
                    </div>
                    @endif
                    @if ($asset->serial_number)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">N.° de serie</span>
                        <span class="font-mono text-slate-700">{{ $asset->serial_number }}</span>
                    </div>
                    @endif
                    @if ($asset->purchase_date)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Fecha de compra</span>
                        <span class="text-slate-700">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if ($asset->reference_value)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Valor referencial</span>
                        <span class="font-semibold text-slate-800">S/ {{ number_format($asset->reference_value, 2) }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Ubicación y responsable --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Ubicación</h3>
                </x-slot:header>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Unidad organizacional</p>
                        <p class="text-sm font-medium text-slate-800">{{ $asset->organizationalUnit?->name ?? '—' }}</p>
                        @if ($asset->organizationalUnit?->full_path)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $asset->organizationalUnit->full_path }}</p>
                        @endif
                    </div>
                    @if ($asset->responsible)
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-2">Responsable</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$asset->responsible->full_name" size="sm" />
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $asset->responsible->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $asset->responsible->position ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Agente de monitoreo --}}
            @if ($asset->agentDevice)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Agente de monitoreo</h3>
                    @php
                        $agentTone = match($asset->agentDevice->status->value) {
                            'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
                        };
                    @endphp
                    <x-ui.badge :tone="$agentTone" :dot="true">{{ $asset->agentDevice->status->label() }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">UUID</span>
                        <span class="font-mono text-xs text-slate-600 truncate max-w-[160px]">{{ $asset->agentDevice->uuid }}</span>
                    </div>
                    @if ($asset->agentDevice->last_heartbeat_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Último heartbeat</span>
                        <span class="text-slate-700">{{ $asset->agentDevice->last_heartbeat_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
                @can('agent.view')
                <x-slot:footer>
                    <x-ui.button variant="ghost" size="xs" href="{{ route('agents.show', $asset->agentDevice) }}">Ver detalles del agente</x-ui.button>
                </x-slot:footer>
                @endcan
            </x-ui.card>
            @endif
        </div>

        {{-- Right columns --}}
        <div class="xl:col-span-2">
            <x-ui.tabs active="historial" :tabs="['historial' => 'Historial', 'casos' => 'Casos de mantenimiento', 'documentos' => 'Documentos']">

                {{-- Historial de movimientos --}}
                <x-ui.tab-panel name="historial" x-show="activeTab === 'historial'">
                    @if ($asset->movements->isEmpty())
                        <x-ui.empty-state title="Sin movimientos" description="Este activo no tiene movimientos registrados." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->movements as $movement)
                            @php
                                $movTone = match($movement->movement_type->value) {
                                    'asignacion' => 'success', 'traslado' => 'info', 'devolucion' => 'warning',
                                    'baja' => 'danger', 'ingreso' => 'primary', 'prestamo' => 'neutral', default => 'neutral',
                                };
                            @endphp
                            <div class="flex gap-3 p-3 rounded-xl bg-slate-50">
                                <x-ui.badge :tone="$movTone" size="sm">{{ $movement->movement_type->label() }}</x-ui.badge>
                                <div class="flex-1 min-w-0">
                                    @if ($movement->destination_unit)
                                        <p class="text-sm text-slate-700">→ {{ $movement->destination_unit }}</p>
                                    @endif
                                    @if ($movement->notes)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $movement->notes }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400 shrink-0">{{ $movement->created_at->format('d/m/Y') }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>

                {{-- Casos de mantenimiento --}}
                <x-ui.tab-panel name="casos" x-show="activeTab === 'casos'">
                    @if ($asset->maintenanceCases->isEmpty())
                        <x-ui.empty-state title="Sin casos" description="Este activo no tiene casos de mantenimiento." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->maintenanceCases as $case)
                            @php
                                $caseTone = match($case->status->value) {
                                    'pendiente' => 'neutral', 'en_progreso' => 'warning',
                                    'completado' => 'success', 'cancelado' => 'danger', default => 'neutral',
                                };
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $case->code }}</p>
                                    <p class="text-xs text-slate-400">{{ $case->maintenance_type->label() }} &middot; {{ $case->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.badge :tone="$caseTone" size="sm">{{ $case->status->label() }}</x-ui.badge>
                                    @can('maintenance-case.view')
                                    <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.show', $case) }}">Ver</x-ui.button>
                                    @endcan
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>

                {{-- Documentos --}}
                <x-ui.tab-panel name="documentos" x-show="activeTab === 'documentos'">
                    @if ($asset->documents->isEmpty())
                        <x-ui.empty-state title="Sin documentos" description="No hay documentos asociados a este activo." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->documents as $doc)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $doc->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $doc->document_type->label() }} &middot; {{ $doc->created_at->format('d/m/Y') }}</p>
                                </div>
                                @can('document.view')
                                <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">Descargar</x-ui.button>
                                @endcan
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>
            </x-ui.tabs>
        </div>
    </div>

</x-layouts.app>
