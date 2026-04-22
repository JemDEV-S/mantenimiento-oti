<x-layouts.app title="Nuevo Empleado" moduleLabel="Empleados" pageTitle="Registrar Empleado">

    <x-ui.page-header
        title="Nuevo empleado"
        description="Registra un nuevo miembro del personal de la institución."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('employees.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('employees.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos personales</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="first_name" label="Nombres" :value="old('first_name')" placeholder="Ej: Juan Carlos" />
                    <x-ui.input name="last_name" label="Apellidos" :value="old('last_name')" placeholder="Ej: Pérez García" />
                    <x-ui.input name="dni" label="DNI" :value="old('dni')" placeholder="12345678" maxlength="8" />
                    <x-ui.input name="phone" label="Teléfono" :value="old('phone')" placeholder="Ej: 987654321" />
                    <div class="sm:col-span-2">
                        <x-ui.input name="email" type="email" label="Correo electrónico" :value="old('email')" placeholder="empleado@mdsj.gob.pe" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información laboral</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="position" label="Cargo" :value="old('position')" placeholder="Ej: Asistente administrativo" />
                    <x-ui.searchable-select
                        name="organizational_unit_id"
                        label="Unidad organizacional"
                        :value="old('organizational_unit_id')"
                        :options="$units->pluck('name', 'id')->toArray()"
                        placeholder="Seleccionar unidad..."
                        searchPlaceholder="Buscar unidad..."
                    />
                </div>

                <div
                    class="mt-4 pt-4 border-t border-slate-100"
                    x-data="{
                        isTech: {{ old('is_technician') ? 'true' : 'false' }},
                        usernamePreview: '',
                        dniPreview: '',
                        buildUsername() {
                            const normalize = s => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z]/g, '');
                            const firstName = document.querySelector('[name=first_name]')?.value ?? '';
                            const lastName  = document.querySelector('[name=last_name]')?.value ?? '';
                            const f = normalize(firstName.trim().split(/\s+/)[0] ?? '');
                            const l = normalize(lastName.trim().split(/\s+/)[0] ?? '');
                            this.usernamePreview = f ? (f[0] + l) : '';
                            this.dniPreview = document.querySelector('[name=dni]')?.value ?? '';
                        },
                        init() {
                            this.buildUsername();
                            ['first_name', 'last_name', 'dni'].forEach(field => {
                                document.querySelector('[name=' + field + ']')
                                    ?.addEventListener('input', () => this.buildUsername());
                            });
                        }
                    }"
                    x-on:toggle:is_technician.window="isTech = $event.detail.value"
                >
                    <x-ui.toggle name="is_technician" label="Es técnico de mantenimiento"
                        :checked="old('is_technician', false)"
                        hint="Los técnicos pueden ser asignados a casos de mantenimiento."
                    />

                    <div x-show="isTech" x-cloak class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-ui.input name="specialty" label="Especialidad técnica" :value="old('specialty')" placeholder="Ej: Hardware, Redes, Software..." />
                            <x-ui.input name="technical_level" label="Nivel técnico" :value="old('technical_level')" placeholder="Ej: Junior, Senior..." />
                        </div>

                        {{-- Preview cuenta de usuario --}}
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-blue-800">Se creará una cuenta de acceso al sistema</p>
                                    <div class="mt-2.5 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                        <div class="bg-white/70 rounded-lg px-3 py-2 border border-blue-100">
                                            <span class="text-xs text-blue-500 uppercase tracking-wide font-medium block mb-0.5">Usuario</span>
                                            <span class="font-mono font-semibold text-slate-800" x-text="usernamePreview || '—'"></span>
                                        </div>
                                        <div class="bg-white/70 rounded-lg px-3 py-2 border border-blue-100">
                                            <span class="text-xs text-blue-500 uppercase tracking-wide font-medium block mb-0.5">Contraseña</span>
                                            <span class="font-mono font-semibold text-slate-800" x-text="dniPreview || '—'"></span>
                                        </div>
                                        <div class="bg-white/70 rounded-lg px-3 py-2 border border-blue-100">
                                            <span class="text-xs text-blue-500 uppercase tracking-wide font-medium block mb-0.5">Rol</span>
                                            <span class="font-mono font-semibold text-slate-800">Técnico</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Registrar empleado</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('employees.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>
