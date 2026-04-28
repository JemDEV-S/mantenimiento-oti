@php
    $typeTone = match($maintenanceCase->maintenance_type->value) {
        'preventivo' => 'primary', 'correctivo' => 'info',
        'predictivo' => 'neutral', 'emergencia' => 'danger', default => 'neutral',
    };
    $priorityTone = match($maintenanceCase->priority->value) {
        'baja' => 'neutral', 'media' => 'warning', 'alta' => 'warning', 'critica' => 'danger', default => 'neutral',
    };
    $statusTone = match($maintenanceCase->status->value) {
        'pendiente' => 'neutral',
        'en_progreso', 'en_espera' => 'warning',
        'completado' => 'success',
        'cancelado' => 'danger',
        default => 'neutral',
    };

    $page = [
        'title' => $maintenanceCase->code,
        'moduleLabel' => 'Panel Tecnico',
        'pageTitle' => 'Resumen del caso',
    ];
@endphp

<x-layouts.app :page="$page">

    <x-ui.page-header
        :title="$maintenanceCase->code"
        :description="$maintenanceCase->asset?->name ?? 'Sin activo asignado'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('tecnico.work-queue') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Cola
            </x-ui.button>
            @if (! $isClosed)
            <x-ui.button size="sm" href="{{ route('tecnico.cases.workflow', $maintenanceCase) }}">
                Continuar mantenimiento
            </x-ui.button>
            @endif
            @can('maintenance-case.view')
            <x-ui.button variant="ghost" size="sm" href="{{ route('maintenance-cases.show', $maintenanceCase) }}">
                Vista admin
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-1">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado</h3>
                </x-slot:header>
                <div class="flex flex-wrap gap-2">
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $maintenanceCase->status->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$typeTone">{{ $maintenanceCase->maintenance_type->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$priorityTone">{{ $maintenanceCase->priority->label() }}</x-ui.badge>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Contexto</h3>
                </x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Creado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($maintenanceCase->finished_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cerrado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->finished_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if ($maintenanceCase->reportedBy)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Reportado por</span>
                        <span class="text-slate-700">{{ $maintenanceCase->reportedBy->full_name }}</span>
                    </div>
                    @endif
                    @if ($maintenanceCase->assignedTechnician)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tecnico</span>
                        <span class="text-slate-700">{{ $maintenanceCase->assignedTechnician->full_name }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            @if ($maintenanceCase->items->isNotEmpty())
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Costo estimado</h3>
                </x-slot:header>
                <p class="text-2xl font-bold text-slate-900">S/ {{ number_format($maintenanceCase->total_cost ?? $maintenanceCase->items->sum('total_cost'), 2) }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $maintenanceCase->items->count() }} item(s)</p>
            </x-ui.card>
            @endif
        </div>

        <div class="space-y-4 xl:col-span-2">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Problema y diagnostico</h3>
                </x-slot:header>
                <div class="space-y-4">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Problema</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->problem_description ?: 'Sin descripcion registrada.' }}</p>
                    </div>
                    @if ($maintenanceCase->diagnosis)
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Diagnostico</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->diagnosis }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->actions_taken)
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones realizadas</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->actions_taken }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->notes)
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Notas</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->notes }}</p>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Items registrados</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->items->count() }}</x-ui.badge>
                </x-slot:header>
                @if ($maintenanceCase->items->isEmpty())
                <p class="text-sm text-slate-400">Aun no se registraron items para este caso.</p>
                @else
                <div class="space-y-2">
                    @foreach ($maintenanceCase->items as $item)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->name }}</p>
                            <p class="text-xs text-slate-400">{{ $item->quantity }} x S/ {{ number_format($item->unit_cost, 2) }}</p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">S/ {{ number_format($item->total_cost, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Documentos</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->documents->count() }}</x-ui.badge>
                </x-slot:header>
                @if ($maintenanceCase->documents->isEmpty())
                <p class="text-sm text-slate-400">Sin documentos adjuntos.</p>
                @else
                <div class="space-y-2">
                    @foreach ($maintenanceCase->documents as $doc)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $doc->title }}</p>
                            <p class="text-xs text-slate-400">{{ $doc->document_type->label() }}</p>
                        </div>
                        <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">Descargar</x-ui.button>
                    </div>
                    @endforeach
                </div>
                @endif
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
