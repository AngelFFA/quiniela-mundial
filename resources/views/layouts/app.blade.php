<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quiniela Mundial 2026' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f7f7fb] text-[#080f2f]">
    <div class="min-h-screen overflow-hidden">
        <header class="sticky top-0 z-50 px-6 pt-5">
            <div class="mx-auto flex max-w-7xl items-center justify-between rounded-3xl bg-white/95 px-6 py-4 shadow-lg ring-1 ring-black/5 backdrop-blur">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#1238ff] via-[#e51b2b] to-[#ffc400] shadow-md">
                        <span class="text-xl font-black text-white">Q</span>
                    </div>

                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#080f2f]">
                            Quiniela Mundial
                        </p>
                        <p class="text-xs font-semibold text-[#080f2f]/50">
                            FIFA 2026
                        </p>
                    </div>
                </a>

                <nav class="hidden items-center gap-5 md:flex">
                    <a href="{{ route('landing') }}" class="text-sm font-bold text-[#080f2f]/70 hover:text-[#1238ff]">
                        Inicio
                    </a>

                    <a href="{{ route('rules') }}" class="text-sm font-bold text-[#080f2f]/70 hover:text-[#1238ff]">
                        Reglamento
                    </a>

                    @auth
                        <a href="{{ route('predictions.index') }}" class="text-sm font-bold text-[#080f2f]/70 hover:text-[#1238ff]">
                            Mi Quiniela
                        </a>

                        <a href="{{ route('predictions.public') }}" class="text-sm font-bold text-[#080f2f]/70 hover:text-[#1238ff]">
                            Quinielas
                        </a>

                        <a href="{{ route('Tabla General') }}" class="text-sm font-bold text-[#080f2f]/70 hover:text-[#1238ff]">
                            Tabla General
                        </a>

                        <a href="{{ route('results.index') }}" class="text-sm font-bold text-[#e51b2b] hover:text-[#1238ff]">
                            Resultados
                        </a>
                    @endauth
                </nav>

                @auth
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="h-11 w-11 rounded-full object-cover shadow-md" alt="Avatar">
                        @else
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#1238ff] text-sm font-black text-white shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-2xl border border-[#080f2f]/10 bg-white px-5 py-2 text-sm font-black text-[#080f2f] shadow-sm hover:bg-slate-50">
                                Salir
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('auth.google') }}" class="rounded-2xl bg-[#1238ff] px-5 py-3 text-sm font-black text-white shadow-lg hover:bg-[#0926c7]">
                        Entrar con Google
                    </a>
                @endauth
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
