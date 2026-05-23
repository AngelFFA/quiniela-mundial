@extends('layouts.app', ['title' => 'Panel - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute right-0 top-0 h-[420px] w-[420px] rounded-full bg-[#ffc400]/50"></div>
    <div class="absolute right-40 top-20 h-[330px] w-[330px] rounded-full bg-[#159447]/35"></div>
    <div class="absolute -right-20 top-60 h-[380px] w-[380px] rounded-full bg-[#1238ff]/35"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Panel principal
                </div>

                <h1 class="mt-5 text-6xl font-black text-[#080f2f]">
                    Quiniela Mundial 2026
                </h1>

                <h2 class="mt-4 text-2xl font-black text-[#080f2f]">
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h2>

                <p class="mt-4 max-w-xl text-base font-medium leading-7 text-[#080f2f]/65">
                    Desde aquí podés llenar tu quiniela, revisar las quinielas de otros participantes, consultar el ranking y cargar resultados reales.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-6 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Estado
                </p>

                <p class="mt-2 text-4xl font-black">
                    Activa
                </p>

                <p class="mt-2 text-sm leading-6 text-white/55">
                    Sistema listo para registrar quinielas del Mundial 2026.
                </p>
            </div>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('predictions.index') }}" class="rounded-3xl bg-[#1238ff] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">01</p>

                <h3 class="mt-4 text-2xl font-black">
                    Mi Quiniela
                </h3>

                <p class="mt-2 text-sm font-bold text-white/75">
                    Llenar marcadores desde grupos hasta campeón.
                </p>

                <p class="mt-6 text-2xl font-black">
                    →
                </p>
            </a>

            <a href="{{ route('predictions.public') }}" class="rounded-3xl bg-[#e51b2b] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">02</p>

                <h3 class="mt-4 text-2xl font-black">
                    Quinielas
                </h3>

                <p class="mt-2 text-sm font-bold text-white/75">
                    Ver lo que registró cada participante.
                </p>

                <p class="mt-6 text-2xl font-black">
                    →
                </p>
            </a>

            <a href="{{ route('ranking') }}" class="rounded-3xl bg-[#159447] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">03</p>

                <h3 class="mt-4 text-2xl font-black">
                    Ranking
                </h3>

                <p class="mt-2 text-sm font-bold text-white/75">
                    Consultar puntos y posiciones.
                </p>

                <p class="mt-6 text-2xl font-black">
                    →
                </p>
            </a>

            <a href="{{ route('results.index') }}" class="rounded-3xl bg-[#ffc400] p-7 text-[#080f2f] shadow-xl">
                <p class="text-4xl font-black">04</p>

                <h3 class="mt-4 text-2xl font-black">
                    Resultados
                </h3>

                <p class="mt-2 text-sm font-black text-[#080f2f]/70">
                    Cargar marcadores reales del torneo.
                </p>

                <p class="mt-6 text-2xl font-black">
                    →
                </p>
            </a>
        </div>
    </div>
</section>
@endsection