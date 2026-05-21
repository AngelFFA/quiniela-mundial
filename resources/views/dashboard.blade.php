@extends('layouts.app', ['title' => 'Panel - Quiniela Mundial'])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-12">
    <div class="relative overflow-hidden rounded-[2.2rem] border border-white/10 bg-white/10 p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-[#D71920]/30 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-[#173B8F]/40 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.3em] text-white/45">
                    Panel privado
                </p>

                <h1 class="mt-3 text-4xl font-black">
                    Bienvenido a tu quiniela
                </h1>

                <p class="mt-3 max-w-2xl text-white/60">
                    Desde aquí vas a llenar tus pronósticos,
                    revisar resultados oficiales, comparar
                    quinielas y seguir la tabla de posiciones.
                </p>
            </div>

            <div class="grid grid-cols-3 overflow-hidden rounded-2xl border border-white/15">
                <div class="bg-[#173B8F] px-4 py-3 text-center font-black">
                    USA
                </div>

                <div class="bg-[#00843D] px-4 py-3 text-center font-black">
                    MEX
                </div>

                <div class="bg-[#D71920] px-4 py-3 text-center font-black">
                    CAN
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-6 backdrop-blur">
            <p class="text-sm text-white/50">Tus puntos</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>

        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-6 backdrop-blur">
            <p class="text-sm text-white/50">Marcadores exactos</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>

        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-6 backdrop-blur">
            <p class="text-sm text-white/50">Partidos pronosticados</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-6 backdrop-blur">
            <h2 class="text-2xl font-black">
                Simulador de clasificación
            </h2>

            <p class="mt-2 text-white/50">
                Calcula automáticamente grupos, mejores terceros
                y cruces eliminatorios según tus predicciones.
            </p>

            <a href="{{ route('bracket.simulator') }}"
               class="mt-6 inline-flex rounded-2xl bg-white px-6 py-3 font-black text-[#050b18] shadow-xl transition hover:scale-105">
                Abrir simulador
            </a>
        </div>

        <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-6 backdrop-blur">
            <h2 class="text-2xl font-black">
                Tabla de posiciones
            </h2>

            <p class="mt-2 text-white/50">
                Ranking transparente para todos
                los participantes.
            </p>

            <div class="mt-6 rounded-2xl border border-dashed border-white/20 p-6 text-center text-white/45">
                Pendiente de calcular puntos.
            </div>
        </div>
    </div>
</section>
@endsection