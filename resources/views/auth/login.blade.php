<x-layouts.auth title="Iniciar Sesión">

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Bienvenido</h2>
        <p class="text-slate-500 text-sm mt-1">Ingresa tus credenciales para continuar</p>
    </div>

    <x-ui.flash />

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        <x-ui.input
            name="username"
            label="Usuario"
            type="text"
            :value="old('username')"
            placeholder="usuario@mdsj.gob.pe"
            autocomplete="username"
            autofocus
        />

        <x-ui.input
            name="password"
            label="Contraseña"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
        />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-sigat-600 focus:ring-sigat-500">
                Recordarme
            </label>
        </div>

        <x-ui.button type="submit" class="w-full justify-center">
            Iniciar sesión
        </x-ui.button>
    </form>

</x-layouts.auth>
