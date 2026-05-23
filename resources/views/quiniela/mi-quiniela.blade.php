@extends('layouts.app', ['title' => 'Mi Quiniela'])

@section('content')
<section class="px-4 py-8">
    <div class="mx-auto max-w-6xl">

        <div class="rounded-[2rem] bg-white p-6 shadow-xl">
            <p class="text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                Quiniela Mundial 2026
            </p>

            <h1 class="mt-4 text-4xl font-black text-[#080f2f]">
                Mi quiniela
            </h1>

            <p class="mt-2 text-sm font-bold text-[#080f2f]/55">
                Desde aquí llenarás grupos, eliminatorias y campeón.
            </p>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <a href="/pronosticos" class="rounded-3xl bg-[#1238ff] p-6 text-white shadow-xl">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-white/60">
                    Paso 1
                </p>
                <h2 class="mt-3 text-2xl font-black">
                    Llenar partidos
                </h2>
                <p class="mt-2 text-sm font-bold text-white/70">
                    Fase de grupos y marcadores.
                </p>
            </a>

            <a href="/simulador" class="rounded-3xl bg-[#080f2f] p-6 text-white shadow-xl">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-white/50">
                    Paso 2
                </p>
                <h2 class="mt-3 text-2xl font-black">
                    Ver cruces
                </h2>
                <p class="mt-2 text-sm font-bold text-white/65">
                    Tablas, terceros y llaves.
                </p>
            </a>
        </div>

        <div class="mt-6 rounded-3xl bg-white p-6 shadow-xl">
            <h2 class="text-2xl font-black text-[#080f2f]">
                Falta integrar aquí
            </h2>

            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl bg-[#f4f6ff] p-4 font-black text-[#080f2f]">
                    16avos editables
                </div>
                <div class="rounded-2xl bg-[#f4f6ff] p-4 font-black text-[#080f2f]">
                    Octavos editables
                </div>
                <div class="rounded-2xl bg-[#f4f6ff] p-4 font-black text-[#080f2f]">
                    Cuartos editables
                </div>
                <div class="rounded-2xl bg-[#f4f6ff] p-4 font-black text-[#080f2f]">
                    Semis / final / campeón
                </div>
            </div>
        </div>

    </div>
</section>
@endsection