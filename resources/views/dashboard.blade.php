@extends('layouts.app', ['title' => 'Panel - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute right-0 top-0 h-[420px] w-[420px] rounded-full bg-[#ffc400]/50"></div>
    <div class="absolute right-40 top-20 h-[330px] w-[330px] rounded-full bg-[#159447]/35"></div>
    <div class="absolute -right-20 top-60 h-[380px] w-[380px] rounded-full bg-[#1238ff]/35"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <div>
                <h1 class="text-6xl font-black text-[#080f2f]">Panel</h1>
                <h2 class="mt-4 text-2xl font-black text-[#080f2f]">¡Bienvenido, {{ Auth::user()->name }}!</h2>

                <p class="mt-4 max-w-xl text-base font-medium leading-7 text-[#080f2f]/65">
                    Acá podés gestionar tus pronósticos, ver estadísticas y seguir la competencia.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-6 text-white shadow-2xl">
                <p class="text-sm font-bold text-white/60">Posición actual</p>
                <p class="mt-2 text-6xl font-black">12°</p>
                <p class="mt-2 text-sm text-white/55">de 24 participantes</p>
            </div>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-4">
            <div class="rounded-3xl bg-[#1238ff] p-7 text-white shadow-xl">
                <p class="text-5xl font-black">0</p>
                <p class="mt-2 font-black">Puntos totales</p>
                <p class="mt-7 text-sm font-bold text-white/75">Ver tabla de puntos →</p>
            </div>

            <div class="rounded-3xl bg-[#e51b2b] p-7 text-white shadow-xl">
                <p class="text-5xl font-black">0</p>
                <p class="mt-2 font-black">Marcadores exactos</p>
                <p class="mt-7 text-sm font-bold text-white/75">Ver detalles →</p>
            </div>

            <a href="{{ route('bracket.simulator') }}" class="rounded-3xl bg-[#159447] p-7 text-white shadow-xl">
                <p class="text-5xl font-black">0</p>
                <p class="mt-2 font-black">Partidos completados</p>
                <p class="mt-7 text-sm font-bold text-white/75">Ver simulador →</p>
            </a>

            <div class="rounded-3xl bg-[#ffc400] p-7 text-[#080f2f] shadow-xl">
                <p class="text-5xl font-black">0%</p>
                <p class="mt-2 font-black">Acierto general</p>
                <p class="mt-7 text-sm font-black text-[#080f2f]/70">Ver estadísticas →</p>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-[2rem] bg-white p-7 shadow-xl">
                <h3 class="text-2xl font-black">Próximos partidos</h3>

                <div class="mt-6 space-y-4">
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center rounded-2xl bg-[#f4f6ff] p-4 text-sm font-bold">
                        <span>México</span>
                        <span class="text-[#080f2f]/40">vs</span>
                        <span class="text-right">Canadá</span>
                    </div>

                    <div class="grid grid-cols-[1fr_auto_1fr] items-center rounded-2xl bg-[#f4f6ff] p-4 text-sm font-bold">
                        <span>Estados Unidos</span>
                        <span class="text-[#080f2f]/40">vs</span>
                        <span class="text-right">Irán</span>
                    </div>

                    <div class="grid grid-cols-[1fr_auto_1fr] items-center rounded-2xl bg-[#f4f6ff] p-4 text-sm font-bold">
                        <span>Argentina</span>
                        <span class="text-[#080f2f]/40">vs</span>
                        <span class="text-right">Arabia Saudita</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-7 shadow-xl">
                <h3 class="text-2xl font-black">Actividad reciente</h3>

                <div class="mt-6 space-y-4 text-sm font-bold text-[#080f2f]/70">
                    <p>Simulación generada</p>
                    <p>Pronóstico guardado</p>
                    <p>Marcador exacto acertado</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection