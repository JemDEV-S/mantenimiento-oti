<x-layouts.app title="Nuevo Rol" moduleLabel="Administración" pageTitle="Crear Rol">

    <x-ui.page-header
        title="Nuevo rol"
        description="Define un nuevo rol con sus permisos correspondientes."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('admin.roles.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Datos del rol</h3></x-slot:header>
                <x-ui.input name="name" label="Nombre del rol" :value="old('name')" placeholder="Ej: supervisor" hint="Usa minúsculas y sin espacios." />
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Permisos</h3>
                    <p class="text-xs text-slate-500">Selecciona los permisos que tendrá este rol</p>
                </x-slot:header>

                @php
                    $grouped = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                @endphp

                <div class="space-y-4">
                    @foreach ($grouped as $module => $modulePerms)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{{ $module }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($modulePerms as $perm)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-sigat-300 hover:bg-sigat-50/50 cursor-pointer transition-colors has-[:checked]:border-sigat-500 has-[:checked]:bg-sigat-50">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-sigat-600 focus:ring-sigat-500">
                                <span class="text-xs font-mono text-slate-700">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Crear rol</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('admin.roles.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>
