<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quiniela Mundial 2026' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#050b18] text-white">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,#173b8f55,transparent_30%),radial-gradient(circle_at_top_right,#d7192050,transparent_28%),radial-gradient(circle_at_bottom,#00843d40,transparent_34%)]">
        <header class="sticky top-0 z-50 border-b border-white/10 bg-[#050b18]/85 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#173B8F] via-[#D71920] to-[#00843D] shadow-xl">
                        <span class="text-xl font-black text-white">Q</span>
                    </div>

                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.25em]">Quiniela</p>
                        <p class="text-xs text-white/55">Mundial 2026</p>
                    </div>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-semibold text-white/65 md:flex">
                    <a href="{{ route('landing') }}" class="hover:text-white">Inicio</a>
                    <a href="{{ route('rules') }}" class="hover:text-white">Reglamento</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-white">Panel</a>
                    @endauth
                </nav>

                <div>
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-bold leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-white/45">Participante</p>
                            </div>

                            @if(Auth::user()->avatar)
                                <img
                                    src="{{ Auth::user()->avatar }}"
                                    alt="Avatar de {{ Auth::user()->name }}"
                                    class="h-10 w-10 rounded-full border-2 border-white/20 object-cover shadow-lg"
                                >
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white/20 bg-white text-sm font-black text-[#050b18] shadow-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/20">
                                    Salir
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('auth.google') }}" class="rounded-full bg-white px-5 py-2 text-sm font-black text-[#050b18] shadow-xl transition hover:scale-105">
                            Entrar con Google
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>