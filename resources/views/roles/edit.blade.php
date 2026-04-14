<x-layouts.app title="Editar Rol" moduleLabel="Administración" pageTitle="Editar Rol">

    <x-ui.page-header
        :title="'Editar rol: ' . $role->name"
        description="Modifica los permisos asociados a este rol."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('admin.roles.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Datos del rol</h3></x-slot:header>
                <x-ui.input name="name" label="Nombre del rol" :value="old('name', $role->name)" />
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Permisos</h3>
                </x-slot:header>

                @php
                    $grouped        = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                    $currentPerms   = old('permissions', $role->permissions->pluck('name')->toArray());
                @endphp

                <div class="space-y-4">
                    @foreach ($grouped as $module => $modulePerms)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{{ $module }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($modulePerms as $perm)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-sigat-300 hover:bg-sigat-50/50 cursor-pointer transition-colors has-[:checked]:border-sigat-500 has-[:checked]:bg-sigat-50">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                       {{ in_array($perm->name, $currentPerms) ? 'checked' : '' }}
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
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('admin.roles.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>
