@extends('layouts.app', ['title' => 'Reglamento - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="relative mx-auto max-w-7xl">
        <div class="rounded-[2rem] bg-white p-8 shadow-xl">
            <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                Reglamento oficial
            </div>

            <h1 class="mt-6 text-5xl font-black leading-tight text-[#080f2f]">
                Reglas de la quiniela
            </h1>

            <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                Un solo premio al final del torneo. La quiniela se divide en fase de grupos
                y fase eliminatoria, con reglas distintas para calcular los puntos.
            </p>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-[2rem] bg-white p-7 shadow-xl">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                            Fase 1
                        </p>

                        <h2 class="mt-1 text-3xl font-black text-[#080f2f]">
                            Fase de grupos
                        </h2>
                    </div>

                    <span class="rounded-2xl bg-[#1238ff] px-5 py-3 text-sm font-black text-white">
                        Antes del inicio
                    </span>
                </div>

                <p class="text-sm font-medium leading-7 text-[#080f2f]/65">
                    Antes del primer partido del torneo, cada participante entrega el marcador exacto
                    de todos los partidos de la fase de grupos. No se aceptan cambios después del cierre.
                </p>

                <div class="mt-7 space-y-4">
                    <div class="rounded-2xl bg-[#f4f6ff] p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-[#080f2f]">Marcador exacto</p>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">
                                    Dijiste 2-1 y quedó 2-1.
                                </p>
                            </div>

                            <p class="text-4xl font-black text-[#159447]">5</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#f4f6ff] p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-[#080f2f]">Resultado correcto</p>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">
                                    Acertaste ganador, perdedor o empate.
                                </p>
                            </div>

                            <p class="text-4xl font-black text-[#1238ff]">3</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#f4f6ff] p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-[#080f2f]">Fallo</p>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">
                                    No acertaste marcador ni resultado.
                                </p>
                            </div>

                            <p class="text-4xl font-black text-[#e51b2b]">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white p-7 shadow-xl">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.25em] text-[#e51b2b]">
                            Fase 2
                        </p>

                        <h2 class="mt-1 text-3xl font-black text-[#080f2f]">
                            Eliminatoria
                        </h2>
                    </div>

                    <span class="rounded-2xl bg-[#e51b2b] px-5 py-3 text-sm font-black text-white">
                        Por ronda
                    </span>
                </div>

                <p class="text-sm font-medium leading-7 text-[#080f2f]/65">
                    La llave acertada suma 2 puntos. Los partidos de dieciseisavos mantienen un máximo de 5 puntos.
                </p>

                <div class="mt-7 space-y-4">
                    <div class="rounded-2xl bg-[#fff4f4] p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-lg font-black text-[#080f2f]">Acertar la llave</p>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">Acertaste los dos equipos que se enfrentan.</p>
                            </div>
                            <p class="text-4xl font-black text-[#159447]">2</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#f3f6ff] p-5">
                        <p class="text-lg font-black text-[#080f2f]">Si hay ganador</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-white p-4 text-center">
                                <p class="text-3xl font-black text-[#159447]">5</p>
                                <p class="mt-1 text-sm font-bold text-[#080f2f]">Marcador exacto</p>
                            </div>
                            <div class="rounded-xl bg-white p-4 text-center">
                                <p class="text-3xl font-black text-[#1238ff]">3</p>
                                <p class="mt-1 text-sm font-bold text-[#080f2f]">Ganador correcto</p>
                            </div>
                            <div class="rounded-xl bg-white p-4 text-center">
                                <p class="text-3xl font-black text-[#e51b2b]">0</p>
                                <p class="mt-1 text-sm font-bold text-[#080f2f]">Resultado incorrecto</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#f3f6ff] p-5">
                        <p class="text-lg font-black text-[#080f2f]">Si hay empate</p>
                        <div class="mt-4 space-y-3 text-sm font-bold text-[#080f2f]">
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span>Empate exacto y clasificado correcto</span><strong class="text-xl text-[#159447]">5</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span>Empate no exacto y clasificado correcto</span><strong class="text-xl text-[#1238ff]">3</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span>Empate exacto y clasificado incorrecto</span><strong class="text-xl text-[#ff9f1c]">2</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span>Empate no exacto y clasificado incorrecto</span><strong class="text-xl text-[#ff9f1c]">1</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-3"><span>No acertó el empate</span><strong class="text-xl text-[#e51b2b]">0</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-[2rem] bg-white p-7 shadow-xl">
            <h2 class="text-3xl font-black text-[#080f2f]">
                Reglas generales
            </h2>

            <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Un solo premio</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Gana quien tenga más puntos al final del torneo.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Empate</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Si hay empate, gana quien tenga más marcadores exactos.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Empate final</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Si sigue el empate, se divide el premio.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Cierre de grupos</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Se cierra antes del primer partido del torneo.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Cierre por ronda</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Cada ronda se cierra antes del primer partido de esa ronda.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f4f6ff] p-5">
                    <p class="text-lg font-black text-[#080f2f]">Suspendidos</p>
                    <p class="mt-2 text-sm font-medium leading-6 text-[#080f2f]/60">
                        Partidos suspendidos o anulados no puntúan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection