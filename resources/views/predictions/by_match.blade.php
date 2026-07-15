@extends('layouts.app', ['title' => 'Pronósticos por partido - Mundial 2026'])

@section('content')
@php
    $flagUrl = function ($team) {
        if (!$team || !$team->flag) {
            return null;
        }

        if (str_starts_with($team->flag, 'http')) {
            return $team->flag;
        }

        return 'https://flagcdn.com/w80/' . strtolower($team->flag) . '.png';
    };
@endphp

<section class="px-6 py-12">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex rounded-2xl bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Comparación general
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] md:text-6xl">
                    Pronósticos por partido
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-8 text-[#080f2f]/65 md:text-lg">
                    En cada partido aparecen los pronósticos de todos los participantes que ya finalizaron su quiniela.
                </p>
            </div>

<div class="mt-6 rounded-[1.5rem] bg-white p-2 shadow-xl ring-1 ring-black/5">
                <div class="grid gap-2 md:grid-cols-6">
                    <a href="{{ route('predictions.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Grupos</a>
                    @if(auth()->user()->dieciseisavos_finalizados)
                        <a href="{{ route('round32.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Dieciseisavos</a>
                    @endif
                    @if(auth()->user()->octavos_finalizados)
                        <a href="{{ route('round16.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Octavos</a>
                    @endif
                    @if(auth()->user()->cuartos_finalizados)
                        <a href="{{ route('round8.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Cuartos</a>
                    @endif
                    @if(auth()->user()->semifinales_finalizados)
                        <a href="{{ route('round4.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Semifinales</a>
                    @endif
                    @if(auth()->user()->final_finalizada)
                        <a href="{{ route('round2.by_match') }}" class="rounded-2xl px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-[#080f2f] ring-1 ring-[#dbe2f1] transition hover:bg-[#edf1ff]">Final</a>
                    @endif
                    <a href="{{ route('predictions.public') }}" class="rounded-2xl bg-[#080f2f] px-4 py-3 text-center text-xs font-black uppercase tracking-[0.12em] text-white transition hover:bg-[#1238ff]">Quinielas</a>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-[2rem] bg-white p-4 shadow-xl ring-1 ring-black/5">
            <div class="flex flex-wrap gap-2">
                @foreach(range('A', 'L') as $groupLetter)
                    <a
                        href="#grupo-{{ $groupLetter }}"
                        class="rounded-xl bg-[#edf1ff] px-4 py-2 text-sm font-black text-[#1238ff] transition hover:bg-[#1238ff] hover:text-white"
                    >
                        Grupo {{ $groupLetter }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-8 space-y-10">
            @foreach($matches as $groupName => $groupMatches)
                <section id="grupo-{{ $groupName }}" class="scroll-mt-6">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-3xl font-black text-[#080f2f]">
                            Grupo {{ $groupName }}
                        </h2>

                        <span class="rounded-full bg-[#1238ff] px-4 py-2 text-xs font-black uppercase tracking-[0.14em] text-white">
                            {{ $groupMatches->count() }} partidos
                        </span>
                    </div>

                    <div class="grid gap-6 xl:grid-cols-2">
                        @foreach($groupMatches as $match)
                            @php
                                $matchPredictions = $predictions->get($match->id, collect())->keyBy('user_id');
                                $homeFlag = $flagUrl($match->homeTeam);
                                $awayFlag = $flagUrl($match->awayTeam);
                            @endphp

                            <article class="overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-black/5">
                                <div class="bg-[#080f2f] px-6 py-5 text-white">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-white/45">
                                        {{ $match->match_date ? \Illuminate\Support\Carbon::parse($match->match_date)->format('d/m/Y H:i') : 'Sin fecha' }}
                                    </p>

                                    <div class="mt-4 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                                        <div class="text-center">
                                            @if($homeFlag)
                                                <img src="{{ $homeFlag }}" class="mx-auto h-9 w-13 rounded-lg object-cover" alt="">
                                            @endif
                                            <p class="mt-2 text-sm font-black">
                                                {{ $match->homeTeam?->name ?? 'Equipo 1' }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl bg-white/10 px-4 py-2 text-sm font-black">
                                            VS
                                        </div>

                                        <div class="text-center">
                                            @if($awayFlag)
                                                <img src="{{ $awayFlag }}" class="mx-auto h-9 w-13 rounded-lg object-cover" alt="">
                                            @endif
                                            <p class="mt-2 text-sm font-black">
                                                {{ $match->awayTeam?->name ?? 'Equipo 2' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="grid grid-cols-[1fr_110px] rounded-xl bg-[#1238ff] px-4 py-3 text-xs font-black uppercase tracking-[0.12em] text-white">
                                        <div>Participante</div>
                                        <div class="text-center">Pronóstico</div>
                                    </div>

                                    <div class="mt-3 divide-y divide-[#e8edf6]">
                                        @foreach($users as $user)
                                            @php
                                                $prediction = $matchPredictions->get($user->id);
                                            @endphp

                                            <div class="grid grid-cols-[1fr_110px] items-center gap-3 px-4 py-3">
                                                <div class="text-sm font-black text-[#080f2f]">
                                                    {{ $user->name }}
                                                </div>

                                                <div class="rounded-xl bg-[#edf1ff] px-3 py-2 text-center text-lg font-black text-[#080f2f]">
                                                    @if($prediction && $prediction->predicted_home_score !== null && $prediction->predicted_away_score !== null)
                                                        {{ $prediction->predicted_home_score }} - {{ $prediction->predicted_away_score }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</section>
@endsection
