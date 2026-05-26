@extends('layouts.app', ['title' => 'Resultados - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Resultados oficiales
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Cargar resultados
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Ingresá los marcadores reales. Al guardar, el sistema recalcula los puntos automáticamente.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Punteo automático
                </p>

                <h2 class="mt-3 text-4xl font-black">
                    5 / 3 / 0
                </h2>

                <p class="mt-3 text-sm leading-6 text-white/65">
                    Exacto: 5 puntos. Resultado correcto: 3 puntos. Fallo: 0 puntos.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('results.store') }}" class="mt-10">
            @csrf

            <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-[#080f2f]">
                            Marcadores reales
                        </h2>

                        <p class="mt-2 text-sm font-medium text-[#080f2f]/60">
                            Estos datos serán reemplazados después por la API.
                        </p>
                    </div>

                    <button type="submit" class="rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white shadow-xl">
                        Guardar resultados
                    </button>
                </div>

                <div class="space-y-5">
                    @foreach($matches as $groupName => $games)
                        @php
                            $groupColors = ['#1238ff', '#e51b2b', '#159447', '#7c3aed', '#ffc400', '#ff7a1a'];
                            $groupColor = $groupColors[($loop->iteration - 1) % count($groupColors)];
                            $textColor = in_array($groupColor, ['#ffc400', '#ff7a1a']) ? '#080f2f' : '#ffffff';
                        @endphp

                        <details class="overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-black/5" {{ $loop->first ? 'open' : '' }}>
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between px-6 py-5"
                                style="background-color: {{ $groupColor }}; color: {{ $textColor }};">
                                <span class="text-xl font-black">Grupo {{ $groupName }}</span>
                                <span class="text-sm font-black opacity-80">{{ $games->count() }} partidos</span>
                            </summary>

                            <div class="grid gap-4 bg-white p-5 lg:grid-cols-2">
                                @foreach($games->sortBy('match_date') as $match)
                                    <div class="rounded-3xl bg-[#f4f6ff] p-5">
                                        <div class="mb-4 flex items-center justify-between">
                                            <p class="text-sm font-black text-[#080f2f]/50">
                                                {{ optional($match->match_date)->format('d/m/Y') ?? 'Sin fecha' }}
                                            </p>

                                            @if($match->is_finished)
                                                <p class="rounded-full bg-[#dcfce7] px-3 py-1 text-xs font-black text-[#166534]">
                                                    Finalizado
                                                </p>
                                            @else
                                                <p class="rounded-full bg-white px-3 py-1 text-xs font-black text-[#080f2f]/50">
                                                    Pendiente
                                                </p>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                                            <div class="text-center">
                                                <img
                                                    src="https://flagcdn.com/w80/{{ $match->homeTeam->flag }}.png"
                                                    class="mx-auto h-10 w-14 rounded-md object-cover shadow"
                                                    alt="{{ $match->homeTeam->name }}"
                                                >

                                                <p class="mt-2 text-sm font-black text-[#080f2f]">
                                                    {{ $match->homeTeam->name }}
                                                </p>
                                            </div>

                                            <div class="flex items-center justify-center gap-2">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="results[{{ $match->id }}][home]"
                                                    value="{{ $match->home_score !== null ? $match->home_score : '' }}"
                                                    class="h-14 w-16 rounded-2xl border border-[#080f2f]/10 bg-white text-center text-xl font-black text-[#080f2f] outline-none shadow-sm"
                                                >

                                                <span class="font-black text-[#080f2f]/35">-</span>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="results[{{ $match->id }}][away]"
                                                    value="{{ $match->away_score !== null ? $match->away_score : '' }}"
                                                    class="h-14 w-16 rounded-2xl border border-[#080f2f]/10 bg-white text-center text-xl font-black text-[#080f2f] outline-none shadow-sm"
                                                >
                                            </div>

                                            <div class="text-center">
                                                <img
                                                    src="https://flagcdn.com/w80/{{ $match->awayTeam->flag }}.png"
                                                    class="mx-auto h-10 w-14 rounded-md object-cover shadow"
                                                    alt="{{ $match->awayTeam->name }}"
                                                >

                                                <p class="mt-2 text-sm font-black text-[#080f2f]">
                                                    {{ $match->awayTeam->name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</section>
@endsection