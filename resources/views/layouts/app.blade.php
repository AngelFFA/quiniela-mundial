<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quiniela Mundial' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,#00684733,transparent_35%),radial-gradient(circle_at_top_right,#c8102e33,transparent_35%),radial-gradient(circle_at_bottom,#00286844,transparent_40%)]">
        <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-lg font-black text-slate-950">
                        Q
                    </div>
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.25em] text-white">Quiniela</p>
                        <p class="text-xs text-white/60">Mundial 2026</p>
                    </div>
                </a>

                <nav class="hidden items-center gap-6 text-sm text-white/75 md:flex">
                    <a href="{{ route('landing') }}" class="hover:text-white">Inicio</a>
                    <a href="{{ route('rules') }}" class="hover:text-white">Reglamento</a>
                    <a href="{{ route('dashboard') }}" class="hover:text-white">Panel</a>
                </nav>

                <a href="#"
                   class="rounded-full bg-white px-5 py-2 text-sm font-bold text-slate-950 transition hover:bg-slate-200">
                    Iniciar con Google
                </a>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>