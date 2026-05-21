@extends('layouts.app', ['title' => 'Mis Pronósticos - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute -left-32 top-20 h-[420px] w-[420px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -left-20 top-72 h-[340px] w-[340px] rounded-full bg-[#159447]"></div>
    <div class="absolute -right-16 top-16 h-[430px] w-[430px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-100px] top-[360px] h-[420px] w-[420px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Fase de grupos
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Mis Pronósticos
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Completá los marcadores. Los campos vacíos no tienen pronóstico guardado.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">Participante</p>
                <h2 class="mt-3 text-3xl font-black">{{ Auth::user()->name }}</h2>
                <p class="mt-3 text-sm leading-6 text-white/65">Guardá tus pronósticos y luego revisá el simulador.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-[#dcfce7] px-5 py-4 text-sm font-black text-[#166534]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 rounded-2xl bg-[#fee2e2] px-5 py-4 text-sm font-black text-[#991b1b]">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('predictions.store') }}" class="mt-10">
            @csrf

            <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-[#080f2f]">Calendario de grupos</h2>
                        <p class="mt-2 text-sm font-medium text-[#080f2f]/60">
                            Los partidos se muestran ordenados por fecha.
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="rounded-2xl bg-[#f4f6ff] px-5 py-3 text-sm font-black text-[#080f2f]">
                            {{ $matches->flatten()->count() }} partidos
                        </div>

                        <button type="submit" class="rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white shadow-xl">
                            Guardar pronósticos
                        </button>
                    </div>
                </div>

                @if($matches->isEmpty())
                    <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                        Todavía no hay partidos cargados.
                    </div>
                @else
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
                                        @php
                                            $prediction = $predictions[$match->id] ?? null;
                                        @endphp

                                        <div class="rounded-3xl bg-[#f4f6ff] p-5">
                                            <div class="mb-4 flex items-center justify-between">
                                                <p class="text-sm font-black text-[#080f2f]/50">
                                                    {{ optional($match->match_date)->format('d/m/Y') ?? 'Sin fecha' }}
                                                </p>

                                                <p class="rounded-full bg-white px-3 py-1 text-xs font-black text-[#080f2f]/50">
                                                    Grupo {{ $groupName }}
                                                </p>
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
                                                        name="predictions[{{ $match->id }}][home]"
                                                        value="{{ $prediction?->predicted_home_score !== null ? $prediction->predicted_home_score : '' }}"
                                                        placeholder=""
                                                        class="h-14 w-16 rounded-2xl border border-[#080f2f]/10 bg-white text-center text-xl font-black text-[#080f2f] outline-none shadow-sm"
                                                    >

                                                    <span class="font-black text-[#080f2f]/35">-</span>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        name="predictions[{{ $match->id }}][away]"
                                                        value="{{ $prediction?->predicted_away_score !== null ? $prediction->predicted_away_score : '' }}"
                                                        placeholder=""
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
                @endif
            </div>
        </form>
    </div>
</section>
@endsection