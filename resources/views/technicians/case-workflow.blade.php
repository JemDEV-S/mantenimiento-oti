@php
    $page = [
        'title' => 'Workflow ' . $maintenanceCase->code,
        'moduleLabel' => 'Panel Tecnico',
        'pageTitle' => 'Continuar mantenimiento',
    ];
@endphp

<x-layouts.app :page="$page">

    <x-ui.page-header
        :title="'Continuar ' . $maintenanceCase->code"
        :description="$maintenanceCase->asset?->name ?? 'Sin activo asignado'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('tecnico.work-queue') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Cola
            </x-ui.button>
            <x-ui.button variant="ghost" size="sm" href="{{ route('tecnico.cases.show', $maintenanceCase) }}">
                Ver resumen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mx-auto max-w-4xl">
        @include('technicians.partials.maintenance-form', [
            'mode' => 'workflow',
            'submitRoute' => route('tecnico.cases.progress', $maintenanceCase),
            'maintenanceCase' => $maintenanceCase,
            'assets' => collect(),
            'preselectedAsset' => null,
            'types' => $types,
            'priorities' => $priorities,
            'itemTypes' => $itemTypes,
            'templates' => $templates,
            'isClosed' => $isClosed,
        ])
    </div>

</x-layouts.app>
