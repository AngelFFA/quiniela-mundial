@extends('layouts.app', ['title' => 'Resultados - Quiniela Mundial'])

@section('content')
<section class="px-4 py-8 sm:px-6 sm:py-12">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Resultados oficiales
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] sm:text-6xl">
                    Cargar resultados
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-7 text-[#080f2f]/65 sm:text-lg sm:leading-8">
                    Ingresá los marcadores reales. Al guardar, el sistema recalcula los puntos automáticamente.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-6 text-white shadow-2xl sm:p-7">
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

        <form method="POST" action="{{ route('results.store') }}" class="mt-8 sm:mt-10">
            @csrf

            <div class="rounded-[2rem] bg-white p-4 shadow-2xl sm:p-7">
                <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-[#080f2f] sm:text-3xl">
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
                                class="flex cursor-pointer list-none items-center justify-between px-5 py-5 sm:px-6"
                                style="background-color: {{ $groupColor }}; color: {{ $textColor }};">
                                <span class="text-xl font-black">Grupo {{ $groupName }}</span>
                                <span class="text-sm font-black opacity-80">{{ $games->count() }} partidos</span>
                            </summary>

                            <div class="grid gap-4 bg-white p-4 lg:grid-cols-2 lg:p-5">
                                @foreach($games->sortBy('match_date') as $match)
                                    <div class="rounded-3xl bg-[#f4f6ff] p-4 sm:p-5">
                                        <div class="mb-4 flex items-center justify-between gap-2">
                                            <p class="text-sm font-black text-[#080f2f]/50">
                                                {{ optional($match->match_date)->format('d/m/Y') ?? 'Sin fecha' }}
                                            </p>

                                            @if($match->is_finished)
                                                <p class="shrink-0 rounded-full bg-[#dcfce7] px-3 py-1 text-xs font-black text-[#166534]">
                                                    Finalizado
                                                </p>
                                            @else
                                                <p class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-black text-[#080f2f]/50">
                                                    Pendiente
                                                </p>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-[64px_104px_64px] items-center justify-center gap-2 sm:grid-cols-[minmax(0,1fr)_120px_minmax(0,1fr)] sm:gap-4">
                                            <div class="flex min-w-0 flex-col items-center justify-center gap-1 text-center sm:flex-row sm:justify-start sm:gap-3 sm:text-left">
                                                <img
                                                    src="https://flagcdn.com/w80/{{ $match->homeTeam->flag }}.png"
                                                    class="h-8 w-11 shrink-0 rounded-lg object-cover shadow sm:h-10 sm:w-14 sm:rounded-md"
                                                    alt="{{ $match->homeTeam->name }}"
                                                >

                                                <p class="w-full min-w-0 text-center text-[11px] font-black leading-tight text-[#080f2f] sm:text-left sm:text-sm">
                                                    <span class="block sm:hidden">{{ $match->homeTeam?->short_name ?? $match->homeTeam?->code ?? 'EQ1' }}</span>
                                                    <span class="hidden truncate sm:block">{{ $match->homeTeam?->name ?? 'Equipo 1' }}</span>
                                                </p>
                                            </div>

                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="results[{{ $match->id }}][home]"
                                                    value="{{ $match->home_score !== null ? $match->home_score : '' }}"
                                                    class="h-12 w-11 rounded-xl border border-[#080f2f]/10 bg-white text-center text-lg font-black text-[#080f2f] outline-none shadow-sm sm:h-14 sm:w-16 sm:rounded-2xl sm:text-xl"
                                                >

                                                <span class="text-sm font-black text-[#080f2f]/35 sm:text-base">-</span>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="results[{{ $match->id }}][away]"
                                                    value="{{ $match->away_score !== null ? $match->away_score : '' }}"
                                                    class="h-12 w-11 rounded-xl border border-[#080f2f]/10 bg-white text-center text-lg font-black text-[#080f2f] outline-none shadow-sm sm:h-14 sm:w-16 sm:rounded-2xl sm:text-xl"
                                                >
                                            </div>

                                            <div class="flex min-w-0 flex-col-reverse items-center justify-center gap-1 text-center sm:flex-row sm:justify-end sm:gap-3 sm:text-right">
                                                <p class="w-full min-w-0 text-center text-[11px] font-black leading-tight text-[#080f2f] sm:text-right sm:text-sm">
                                                    <span class="block sm:hidden">{{ $match->awayTeam?->short_name ?? $match->awayTeam?->code ?? 'EQ2' }}</span>
                                                    <span class="hidden truncate sm:block">{{ $match->awayTeam?->name ?? 'Equipo 2' }}</span>
                                                </p>

                                                <img
                                                    src="https://flagcdn.com/w80/{{ $match->awayTeam->flag }}.png"
                                                    class="h-8 w-11 shrink-0 rounded-lg object-cover shadow sm:h-10 sm:w-14 sm:rounded-md"
                                                    alt="{{ $match->awayTeam->name }}"
                                                >
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
