@extends('layouts.app', ['title' => 'Quiniela Mundial 2026'])

@section('content')
<section class="relative min-h-[calc(100vh-92px)] px-6 py-14">
    <div class="absolute -left-40 top-0 h-[560px] w-[560px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -left-24 top-[250px] h-[430px] w-[430px] rounded-full bg-[#51c855]"></div>
    <div class="absolute -left-6 top-[430px] h-[360px] w-[360px] rounded-full bg-[#8edcff]"></div>

    <div class="absolute -right-20 top-10 h-[500px] w-[500px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-110px] top-[360px] h-[450px] w-[450px] rounded-full bg-[#ff7a1a]"></div>
    <div class="absolute right-16 bottom-4 h-[410px] w-[410px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-2">
        <div>
            <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                Mundial 2026 · Quiniela privada
            </div>

            <h1 class="mt-8 text-6xl font-black leading-[0.95] text-[#080f2f] md:text-7xl">
                Quiniela
                <span class="block">Mundial 2026</span>
            </h1>

            <p class="mt-7 max-w-xl text-lg font-medium leading-8 text-[#080f2f]/65">
                Llená tus marcadores, revisá las quinielas de otros participantes
                y seguí la tabla de puntos durante todo el torneo.
            </p>

            <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                <a href="{{ route('predictions.index') }}" class="rounded-2xl bg-[#1238ff] px-8 py-4 text-center font-black text-white shadow-lg">
                    Ir a mi quiniela →
                </a>

                <a href="{{ route('rules') }}" class="rounded-2xl border border-[#080f2f]/15 bg-white px-8 py-4 text-center font-black text-[#080f2f] shadow-sm">
                    Ver reglamento
                </a>
            </div>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-2xl">
            <div class="rounded-[1.5rem] bg-[#080f2f] p-6 text-white">
                <p class="text-xs font-black uppercase tracking-[0.28em] text-white/45">
                    Vista previa
                </p>

                <h2 class="mt-2 text-3xl font-black">
                    Camino al campeón
                </h2>

                <div class="mt-6 rounded-3xl bg-[#f1f5ff] p-5 text-[#080f2f]">
                    <p class="font-black text-[#080f2f]/65">
                        Grupo A
                    </p>

                    <div class="mt-4 grid grid-cols-3 items-center gap-4">
                        <div class="rounded-2xl bg-[#1238ff] px-4 py-4 text-center font-black text-white">
                            Equipo 1
                        </div>

                        <div class="text-center text-3xl font-black">
                            2 - 1
                        </div>

                        <div class="rounded-2xl bg-[#e51b2b] px-4 py-4 text-center font-black text-white">
                            Equipo 2
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="rounded-2xl bg-[#1238ff] p-5 text-center">
                        <p class="text-4xl font-black">104</p>
                        <p class="mt-1 text-xs font-black uppercase tracking-widest text-white/70">
                            Partidos
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[#159447] p-5 text-center">
                        <p class="text-4xl font-black">48</p>
                        <p class="mt-1 text-xs font-black uppercase tracking-widest text-white/70">
                            Equipos
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[#ffc400] p-5 text-center text-[#080f2f]">
                        <p class="text-4xl font-black">1</p>
                        <p class="mt-1 text-xs font-black uppercase tracking-widest">
                            Campeón
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative mx-auto mt-14 grid max-w-7xl overflow-hidden rounded-[2rem] shadow-2xl md:grid-cols-4">
        <a href="{{ route('predictions.index') }}" class="bg-[#1238ff] p-8 text-white">
            <h3 class="text-2xl font-black">
                Mi Quiniela
            </h3>

            <p class="mt-2 text-sm font-medium text-white/75">
                Llená marcadores desde grupos hasta campeón.
            </p>

            <p class="mt-7 text-3xl">
                →
            </p>
        </a>

        <a href="{{ route('predictions.public') }}" class="bg-[#e51b2b] p-8 text-white">
            <h3 class="text-2xl font-black">
                Quinielas
            </h3>

            <p class="mt-2 text-sm font-medium text-white/75">
                Revisá las quinielas de otros participantes.
            </p>

            <p class="mt-7 text-3xl">
                →
            </p>
        </a>

        <a href="{{ route('ranking') }}" class="bg-[#159447] p-8 text-white">
            <h3 class="text-2xl font-black">
                Ranking
            </h3>

            <p class="mt-2 text-sm font-medium text-white/75">
                Seguimiento de puntos y posiciones.
            </p>

            <p class="mt-7 text-3xl">
                →
            </p>
        </a>

        <a href="{{ route('results.index') }}" class="bg-[#ffc400] p-8 text-[#080f2f]">
            <h3 class="text-2xl font-black">
                Resultados
            </h3>

            <p class="mt-2 text-sm font-medium text-[#080f2f]/70">
                Marcadores reales del torneo.
            </p>

            <p class="mt-7 text-3xl">
                →
            </p>
        </a>
    </div>
</section>
@endsection