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
                    Accedé rápido a las partes principales de la quiniela.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-6 text-white shadow-2xl">
                <p class="text-sm font-bold text-white/60">Estado</p>
                <p class="mt-2 text-4xl font-black">Activa</p>
                <p class="mt-2 text-sm text-white/55">Sistema listo para pruebas.</p>
            </div>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-3">
            <a href="{{ route('predictions.index') }}" class="rounded-3xl bg-[#1238ff] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">01</p>
                <h3 class="mt-4 text-2xl font-black">Llenar pronósticos</h3>
                <p class="mt-2 text-sm font-bold text-white/75">Ingresar marcadores de fase de grupos.</p>
            </a>

            <a href="{{ route('predictions.public') }}" class="rounded-3xl bg-[#e51b2b] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">02</p>
                <h3 class="mt-4 text-2xl font-black">Ver quinielas</h3>
                <p class="mt-2 text-sm font-bold text-white/75">Consultar lo que puso cada participante.</p>
            </a>

            <a href="{{ route('bracket.simulator') }}" class="rounded-3xl bg-[#159447] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">03</p>
                <h3 class="mt-4 text-2xl font-black">Simulador</h3>
                <p class="mt-2 text-sm font-bold text-white/75">Ver grupos y mejores terceros calculados.</p>
            </a>

            <a href="{{ route('results.index') }}" class="rounded-3xl bg-[#ffc400] p-7 text-[#080f2f] shadow-xl">
                <p class="text-4xl font-black">04</p>
                <h3 class="mt-4 text-2xl font-black">Cargar resultados</h3>
                <p class="mt-2 text-sm font-black text-[#080f2f]/70">Ingresar marcadores reales de prueba.</p>
            </a>

            <a href="{{ route('ranking') }}" class="rounded-3xl bg-[#7c3aed] p-7 text-white shadow-xl">
                <p class="text-4xl font-black">05</p>
                <h3 class="mt-4 text-2xl font-black">Ranking</h3>
                <p class="mt-2 text-sm font-bold text-white/75">Ver puntos y marcadores exactos.</p>
            </a>

            <a href="{{ route('rules') }}" class="rounded-3xl bg-white p-7 text-[#080f2f] shadow-xl">
                <p class="text-4xl font-black">06</p>
                <h3 class="mt-4 text-2xl font-black">Reglamento</h3>
                <p class="mt-2 text-sm font-black text-[#080f2f]/60">Consultar reglas de la quiniela.</p>
            </a>
        </div>
    </div>
</section>
@endsection