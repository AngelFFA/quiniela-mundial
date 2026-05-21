@extends('layouts.app', ['title' => 'Quiniela Mundial 2026'])

@section('content')
<section class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-2 lg:py-20">
    <div class="absolute left-0 top-20 h-72 w-72 rounded-full bg-[#173B8F]/30 blur-3xl"></div>
    <div class="absolute right-0 top-28 h-72 w-72 rounded-full bg-[#D71920]/25 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-[#00843D]/25 blur-3xl"></div>

    <div class="relative">
        <div class="mb-8 max-w-xl overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-2xl backdrop-blur-xl">
            <div class="grid grid-cols-3">
                <div class="bg-[#173B8F] px-5 py-5 text-center">
                    <p class="text-3xl font-black">USA</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-widest text-white/70">Host</p>
                </div>

                <div class="bg-[#00843D] px-5 py-5 text-center">
                    <p class="text-3xl font-black">MEX</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-widest text-white/70">Host</p>
                </div>

                <div class="bg-[#D71920] px-5 py-5 text-center">
                    <p class="text-3xl font-black">CAN</p>
                    <p class="mt-1 text-xs font-bold uppercase tracking-widest text-white/70">Host</p>
                </div>
            </div>
        </div>

        <p class="mb-4 text-sm font-black uppercase tracking-[0.35em] text-white/45">
            Mundial 2026 · Quiniela privada
        </p>

        <h1 class="max-w-3xl text-5xl font-black leading-tight md:text-7xl">
            Pronosticá.
            <span class="block bg-gradient-to-r from-[#4d8dff] via-white to-[#ff4b55] bg-clip-text text-transparent">
                Competí.
            </span>
            Ganá.
        </h1>

        <p class="mt-6 max-w-xl text-lg leading-8 text-white/70">
            Una quiniela para registrar marcadores, comparar pronósticos con otros participantes
            y calcular puntos automáticamente con los resultados reales del torneo.
        </p>

        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-2xl bg-white px-8 py-4 text-center text-base font-black text-[#050b18] shadow-xl transition hover:scale-105">
                    Ir al panel
                </a>
            @else
                <a href="{{ route('auth.google') }}" class="rounded-2xl bg-white px-8 py-4 text-center text-base font-black text-[#050b18] shadow-xl transition hover:scale-105">
                    Entrar con Google
                </a>
            @endauth

            <a href="{{ route('rules') }}" class="rounded-2xl border border-white/20 bg-white/10 px-8 py-4 text-center text-base font-bold text-white backdrop-blur transition hover:bg-white/20">
                Ver reglamento
            </a>
        </div>

        <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
            <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5 text-center backdrop-blur">
                <p class="text-4xl font-black">104</p>
                <p class="mt-1 text-xs font-semibold text-white/50">partidos</p>
            </div>

            <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5 text-center backdrop-blur">
                <p class="text-4xl font-black">48</p>
                <p class="mt-1 text-xs font-semibold text-white/50">equipos</p>
            </div>

            <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5 text-center backdrop-blur">
                <p class="text-4xl font-black">1</p>
                <p class="mt-1 text-xs font-semibold text-white/50">premio</p>
            </div>
        </div>
    </div>

    <div class="relative">
        <div class="absolute -inset-6 rounded-[3rem] bg-gradient-to-br from-[#173B8F]/40 via-white/10 to-[#D71920]/35 blur-2xl"></div>

        <div class="relative overflow-hidden rounded-[2.5rem] border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-xl">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-[#D71920]/30 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-[#00843D]/25 blur-3xl"></div>

            <div class="relative rounded-[2rem] bg-[#07101f]/90 p-6">
                <div class="mb-7 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.3em] text-white/40">Vista previa</p>
                        <h2 class="mt-2 text-3xl font-black">Centro de quiniela</h2>
                    </div>

                    <div class="rounded-full bg-white px-4 py-2 text-sm font-black text-[#050b18]">
                        En vivo
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-[1.7rem] border border-white/10 bg-white/10 p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm font-bold text-white/50">Partido de grupo</p>
                            <span class="rounded-full bg-[#173B8F]/70 px-3 py-1 text-xs font-black">Abierto</span>
                        </div>

                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                            <div>
                                <div class="h-12 rounded-xl bg-gradient-to-r from-[#173B8F] to-[#4d8dff]"></div>
                                <p class="mt-2 font-black">Equipo A</p>
                            </div>

                            <div class="rounded-2xl bg-white px-5 py-3 text-3xl font-black text-[#050b18]">
                                2 - 1
                            </div>

                            <div class="text-right">
                                <div class="h-12 rounded-xl bg-gradient-to-r from-[#ff4b55] to-[#D71920]"></div>
                                <p class="mt-2 font-black">Equipo B</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5">
                            <p class="text-sm text-white/50">Resultado oficial</p>
                            <p class="mt-2 text-2xl font-black text-yellow-300">Pendiente</p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-5">
                            <p class="text-sm text-white/50">Puntos máximos</p>
                            <p class="mt-2 text-2xl font-black text-green-300">7 pts</p>
                        </div>
                    </div>

                    <div class="rounded-[1.7rem] border border-white/10 bg-white/5 p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm font-bold text-white/60">Tabla preliminar</p>
                            <p class="text-xs font-semibold text-white/35">visible para todos</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="font-bold">Participante 1</span>
                                <span class="font-black text-green-300">18 pts</span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="font-bold">Participante 2</span>
                                <span class="font-black text-blue-300">15 pts</span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl bg-white/10 px-4 py-3">
                                <span class="font-bold">Participante 3</span>
                                <span class="font-black text-red-300">12 pts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-3 gap-3">
            <div class="h-2 rounded-full bg-[#173B8F]"></div>
            <div class="h-2 rounded-full bg-[#00843D]"></div>
            <div class="h-2 rounded-full bg-[#D71920]"></div>
        </div>
    </div>
</section>
@endsection