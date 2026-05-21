@extends('layouts.app', ['title' => 'Reglamento - Quiniela Mundial'])

@section('content')
<section class="mx-auto max-w-5xl px-6 py-12">
    <h1 class="text-4xl font-black">Reglamento de la quiniela</h1>
    <p class="mt-4 text-white/60">
        La quiniela tendrá un solo premio al final del torneo. Los puntos se calculan automáticamente
        según los resultados registrados para cada partido.
    </p>

    <div class="mt-10 space-y-8">
        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <h2 class="text-2xl font-black">Fase 1: Grupos</h2>
            <p class="mt-3 text-white/70">
                Antes del primer partido del torneo, cada participante deberá entregar el marcador exacto
                de todos los partidos de la fase de grupos. No se aceptan cambios después del cierre.
            </p>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/10 text-white">
                        <tr>
                            <th class="px-4 py-3">Caso</th>
                            <th class="px-4 py-3">Puntos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-white/70">
                        <tr>
                            <td class="px-4 py-3">Marcador exacto</td>
                            <td class="px-4 py-3 font-bold text-green-300">5</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Resultado correcto</td>
                            <td class="px-4 py-3 font-bold text-yellow-300">3</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Fallo</td>
                            <td class="px-4 py-3 font-bold text-red-300">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <h2 class="text-2xl font-black">Fase 2: Eliminatoria</h2>
            <p class="mt-3 text-white/70">
                Antes de cada ronda, cada participante actualizará sus pronósticos: marcador al minuto 90
                y equipo que avanza. Los pronósticos se cierran antes del primer partido de cada ronda.
            </p>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/10 text-white">
                        <tr>
                            <th class="px-4 py-3">Caso</th>
                            <th class="px-4 py-3">Puntos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-white/70">
                        <tr>
                            <td class="px-4 py-3">Acertaste la llave</td>
                            <td class="px-4 py-3 font-bold text-blue-300">2</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Marcador exacto al 90' + quién avanza correcto</td>
                            <td class="px-4 py-3 font-bold text-green-300">5</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Resultado correcto al 90' + quién avanza correcto</td>
                            <td class="px-4 py-3 font-bold text-yellow-300">3</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Fallaste marcador pero acertaste quién avanza</td>
                            <td class="px-4 py-3 font-bold text-yellow-300">2</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Marcador exacto al 90' pero fallaste quién avanza</td>
                            <td class="px-4 py-3 font-bold text-orange-300">1</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">Todo mal</td>
                            <td class="px-4 py-3 font-bold text-red-300">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-white/60">
                Los 2 puntos por llave acertada se suman encima de los puntos por resultado.
                El máximo por partido es de 7 puntos.
            </p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <h2 class="text-2xl font-black">Reglas generales</h2>

            <ul class="mt-4 space-y-3 text-white/70">
                <li>Un solo premio al participante con más puntos totales al final del torneo.</li>
                <li>En caso de empate, gana quien tenga más marcadores exactos.</li>
                <li>Si el empate continúa, el premio se divide.</li>
                <li>Los pronósticos de grupos se cierran antes del primer partido del torneo.</li>
                <li>Los pronósticos de cada ronda eliminatoria se cierran antes del primer partido de esa ronda.</li>
                <li>No se aceptan cambios después del cierre de cada fase.</li>
                <li>Partidos suspendidos o anulados no puntúan.</li>
            </ul>
        </div>
    </div>
</section>
@endsection