@extends('layouts.app', ['title' => 'Quiniela Mundial 2026'])

@section('content')
<section class="px-4 py-8 sm:px-6 sm:py-12">
    <div class="mx-auto max-w-7xl">
        <div class="grid items-center gap-8 lg:grid-cols-[1fr_520px] lg:gap-14">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-[#1238ff] sm:px-5 sm:text-xs sm:tracking-[0.25em]">
                    Mundial 2026 · Quiniela privada
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] sm:text-5xl md:text-7xl">
                    Quiniela
                    <span class="block">Mundial 2026</span>
                </h1>

                <p class="mt-5 max-w-xl text-base font-medium leading-7 text-[#080f2f]/65 sm:text-lg sm:leading-8">
                    Llená tu quiniela completa por marcadores, revisá tu avance y seguí la tabla de puntos durante todo el torneo.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    @auth
                        <a href="{{ route('predictions.index') }}" class="rounded-2xl bg-[#1238ff] px-6 py-4 text-center text-sm font-black text-white shadow-lg sm:px-8 sm:text-base">
                            Ir a mi quiniela →
                        </a>
                    @else
                        <a href="{{ route('auth.google') }}" class="rounded-2xl bg-[#1238ff] px-6 py-4 text-center text-sm font-black text-white shadow-lg sm:px-8 sm:text-base">
                            Entrar con Google →
                        </a>
                    @endauth

                    <a href="{{ route('rules') }}" class="rounded-2xl border border-[#080f2f]/15 bg-white px-6 py-4 text-center text-sm font-black text-[#080f2f] shadow-sm sm:px-8 sm:text-base">
                        Ver reglamento
                    </a>
                </div>
            </div>

            <div class="rounded-[1.5rem] bg-white p-4 shadow-2xl ring-1 ring-black/5 sm:rounded-[2rem] sm:p-6">
                <div class="rounded-[1.2rem] bg-[#080f2f] p-4 text-white sm:rounded-[1.5rem] sm:p-6">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/45 sm:text-xs sm:tracking-[0.28em]">
                        Vista previa
                    </p>

                    <h2 class="mt-2 text-2xl font-black sm:text-3xl">
                        Camino al campeón
                    </h2>

                    <div class="mt-5 rounded-3xl bg-[#f1f5ff] p-4 text-[#080f2f] sm:p-5">
                        <p class="font-black text-[#080f2f]/65">
                            Grupo A
                        </p>

                        <div class="mt-4 grid grid-cols-[1fr_auto_1fr] items-center gap-2 sm:gap-4">
                            <div class="rounded-xl bg-[#1238ff] px-2 py-3 text-center text-xs font-black text-white sm:rounded-2xl sm:px-4 sm:py-4 sm:text-base">
                                Equipo 1
                            </div>

                            <div class="text-center text-xl font-black sm:text-3xl">
                                2 - 1
                            </div>

                            <div class="rounded-xl bg-[#e51b2b] px-2 py-3 text-center text-xs font-black text-white sm:rounded-2xl sm:px-4 sm:py-4 sm:text-base">
                                Equipo 2
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2 sm:mt-6 sm:gap-4">
                        <div class="rounded-xl bg-[#1238ff] p-3 text-center sm:rounded-2xl sm:p-5">
                            <p class="text-2xl font-black sm:text-4xl">104</p>
                            <p class="mt-1 text-[8px] font-black uppercase tracking-widest text-white/70 sm:text-xs">Partidos</p>
                        </div>

                        <div class="rounded-xl bg-[#159447] p-3 text-center sm:rounded-2xl sm:p-5">
                            <p class="text-2xl font-black sm:text-4xl">48</p>
                            <p class="mt-1 text-[8px] font-black uppercase tracking-widest text-white/70 sm:text-xs">Equipos</p>
                        </div>

                        <div class="rounded-xl bg-[#ffc400] p-3 text-center text-[#080f2f] sm:rounded-2xl sm:p-5">
                            <p class="text-2xl font-black sm:text-4xl">1</p>
                            <p class="mt-1 text-[8px] font-black uppercase tracking-widest sm:text-xs">Campeón</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            <div class="mt-10 grid overflow-hidden rounded-[1.5rem] shadow-2xl sm:rounded-[2rem] md:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('predictions.index') }}" class="bg-[#1238ff] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Mi Quiniela</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Llená marcadores desde grupos hasta campeón.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>

                <a href="{{ route('predictions.public') }}" class="bg-[#e51b2b] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Quinielas</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Se habilitarán cuando corresponda.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>

                <a href="{{ route('ranking') }}" class="bg-[#159447] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Tabla General</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Seguimiento de puntos y posiciones.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>

                <a href="{{ route('results.index') }}" class="bg-[#ffc400] p-6 text-[#080f2f] sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Resultados</h3>
                    <p class="mt-2 text-sm font-medium text-[#080f2f]/70">
                        Marcadores reales del torneo.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>
            </div>
        @else
            <div class="mt-10 grid overflow-hidden rounded-[1.5rem] shadow-2xl sm:rounded-[2rem] md:grid-cols-3">
                <a href="{{ route('auth.google') }}" class="bg-[#1238ff] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Entrar</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Iniciá sesión para llenar tu quiniela.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>

                <a href="{{ route('rules') }}" class="bg-[#e51b2b] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Reglamento</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Consultá las reglas de puntuación.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>

                <a href="{{ route('ranking') }}" class="bg-[#159447] p-6 text-white sm:p-8">
                    <h3 class="text-xl font-black sm:text-2xl">Tabla General</h3>
                    <p class="mt-2 text-sm font-medium text-white/75">
                        Tabla general de participantes.
                    </p>
                    <p class="mt-5 text-2xl sm:mt-7 sm:text-3xl">→</p>
                </a>
            </div>
        @endauth
    </div>
</section>
@endsection
