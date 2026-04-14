<x-layouts.app title="Nuevo Permiso" moduleLabel="Administración" pageTitle="Nuevo Permiso">

    <x-ui.page-header
        title="Nuevo permiso"
        description="Registra un nuevo permiso granular para el control de acceso."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('admin.permissions.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('admin.permissions.store') }}" class="max-w-lg">
        @csrf
        <x-ui.card>
            <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Datos del permiso</h3></x-slot:header>
            <x-ui.input
                name="name"
                label="Nombre del permiso"
                :value="old('name')"
                placeholder="Ej: asset.view"
                hint="Formato: módulo.acción (todo en minúsculas)"
            />
            <x-slot:footer>
                <x-ui.button type="submit">Crear permiso</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('admin.permissions.index') }}">Cancelar</x-ui.button>
            </x-slot:footer>
        </x-ui.card>
    </form>

</x-layouts.app>
