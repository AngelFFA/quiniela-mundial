@extends('layouts.app', ['title' => 'Quiniela Mundial 2026'])

@section('content')
<section class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-20 lg:grid-cols-2">
    <div>
        <div class="mb-6 inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm text-white/80">
            México · Estados Unidos · Canadá 2026
        </div>

        <h1 class="text-5xl font-black leading-tight md:text-7xl">
            La quiniela del Mundial,
            <span class="block bg-gradient-to-r from-green-400 via-white to-red-400 bg-clip-text text-transparent">
                clara y automática.
            </span>
        </h1>

        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/70">
            Registrá tus marcadores, compará tus pronósticos con los demás participantes
            y revisá los puntos actualizados conforme se carguen los resultados oficiales.
        </p>

        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
            <a href="#"
               class="rounded-2xl bg-white px-7 py-4 text-center font-bold text-slate-950 transition hover:bg-slate-200">
                Entrar con Google
            </a>

            <a href="{{ route('rules') }}"
               class="rounded-2xl border border-white/20 px-7 py-4 text-center font-bold text-white transition hover:bg-white/10">
                Ver reglamento
            </a>
        </div>
    </div>

    <div class="rounded-[2rem] border border-white/10 bg-white/10 p-6 shadow-2xl backdrop-blur">
        <div class="rounded-[1.5rem] bg-slate-950/80 p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-white/50">Partido destacado</p>
                    <h2 class="text-2xl font-black">México vs Canadá</h2>
                </div>
                <span class="rounded-full bg-green-500/20 px-4 py-2 text-sm font-bold text-green-300">
                    Grupo A
                </span>
            </div>

            <div class="grid grid-cols-3 items-center gap-4 text-center">
                <div class="rounded-2xl bg-white/10 p-5">
                    <div class="mx-auto mb-3 h-14 w-14 rounded-full bg-green-500"></div>
                    <p class="font-bold">México</p>
                </div>

                <div>
                    <p class="text-5xl font-black">2 - 1</p>
                    <p class="mt-2 text-sm text-white/50">Tu marcador</p>
                </div>

                <div class="rounded-2xl bg-white/10 p-5">
                    <div class="mx-auto mb-3 h-14 w-14 rounded-full bg-red-500"></div>
                    <p class="font-bold">Canadá</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-white/60">Resultado real</span>
                    <span class="font-bold text-yellow-300">Pendiente</span>
                </div>
                <div class="mt-3 h-2 rounded-full bg-white/10">
                    <div class="h-2 w-2/3 rounded-full bg-gradient-to-r from-green-400 via-white to-red-400"></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection