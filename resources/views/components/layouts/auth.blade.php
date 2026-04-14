@props([
    'title' => 'Iniciar Sesion',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('components.layouts.partials.head', ['title' => $title])
    </head>
    <body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-slate-900 via-slate-800 to-sigat-900 relative overflow-hidden">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-sigat-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-sigat-600/8 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sigat-400/5 rounded-full blur-3xl"></div>
        </div>

        {{-- Auth Card --}}
        <div class="relative z-10 w-full max-w-md">
            {{-- Brand --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-sigat-600 text-white font-extrabold text-xl shadow-2xl shadow-sigat-600/30 mb-4">
                    S
                </div>
                <h1 class="text-2xl font-bold text-white">SIGAT</h1>
                <p class="text-slate-400 text-sm mt-1">Sistema Integral de Gestion de Activos</p>
            </div>

            {{-- Card --}}
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-black/20 border border-white/20 p-8">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p class="text-center text-slate-500 text-xs mt-6">
                Municipalidad Distrital de San Juan &mdash; {{ date('Y') }}
            </p>
        </div>
    </body>
</html>
