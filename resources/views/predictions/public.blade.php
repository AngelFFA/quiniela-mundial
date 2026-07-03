@extends('layouts.app', ['title' => 'Quinielas - Mundial 2026'])

@section('content')
@php
    $standings = $standings ?? collect();
    $bestThirds = $bestThirds ?? collect();
    $bracketMatches = collect($bracketMatches ?? []);

    $flagUrl = function ($team) {
        if (!$team || !$team->flag) {
            return null;
        }

        if (str_starts_with($team->flag, 'http')) {
            return $team->flag;
        }

        return 'https://flagcdn.com/w80/' . strtolower($team->flag) . '.png';
    };

    $getSlot = function ($slot) use ($bracketMatches) {
        return $bracketMatches->firstWhere('slot', $slot);
    };

    $roundColumns = [
        ['title' => 'Round of 32', 'class' => 'level-1', 'side' => '', 'slots' => [74, 77, 73, 75, 83, 84, 81, 82]],
        ['title' => 'R16', 'class' => 'level-2', 'side' => '', 'slots' => [89, 90, 93, 94]],
        ['title' => 'QF', 'class' => 'level-3', 'side' => '', 'slots' => [97, 98]],
        ['title' => 'SF', 'class' => 'level-4', 'side' => '', 'slots' => [101]],
        ['title' => 'Final', 'class' => 'level-5', 'side' => '', 'slots' => [104, 103]],
        ['title' => 'SF', 'class' => 'level-4', 'side' => 'right', 'slots' => [102]],
        ['title' => 'QF', 'class' => 'level-3', 'side' => 'right', 'slots' => [99, 100]],
        ['title' => 'R16', 'class' => 'level-2', 'side' => 'right', 'slots' => [91, 92, 95, 96]],
        ['title' => 'Round of 32', 'class' => 'level-1', 'side' => 'right', 'slots' => [76, 78, 79, 80, 86, 88, 85, 87]],
    ];
@endphp

<style>
    .public-tab-btn {
        border: 1px solid #dbe2f1;
        background: #ffffff;
        color: #5d6785;
        font-weight: 900;
        transition: 0.2s ease;
    }

    .public-tab-btn.active {
        background: #1238ff;
        border-color: #1238ff;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(18, 56, 255, 0.18);
    }

    .public-tab-panel {
        display: none;
    }

    .public-tab-panel.active {
        display: block;
    }

    .table-header {
        display: grid;
        align-items: center;
        gap: 8px;
        border-radius: 16px;
        background: #1238ff;
        color: white;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .standing-header {
        grid-template-columns: 34px 28px 1fr 50px 50px;
    }

    .thirds-header {
        grid-template-columns: 60px 34px 1fr 80px 80px 130px;
    }

    .bracket-board {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border-radius: 28px;
        background: linear-gradient(135deg, #071833 0%, #0b2145 55%, #061329 100%);
        padding: 18px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .bracket-grid {
        position: relative;
        min-width: 1180px;
        height: 1120px;
        display: grid;
        grid-template-columns: 1.18fr 1fr 0.9fr 0.82fr 1.05fr 0.82fr 0.9fr 1fr 1.18fr;
        gap: 12px;
        align-items: stretch;
    }

    .bracket-column-wrap {
        position: relative;
        height: 100%;
    }

    .bracket-stage-title {
        height: 24px;
        text-align: center;
        font-size: 9px;
        letter-spacing: 0.12em;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.72);
        text-transform: uppercase;
        margin-bottom: 8px;
        white-space: nowrap;
    }

    .bracket-column {
        position: relative;
        height: calc(100% - 32px);
    }

    .bracket-match {
        position: absolute;
        left: 0;
        right: 0;
        min-height: 92px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: linear-gradient(180deg, rgba(14, 39, 80, 0.98) 0%, rgba(7, 23, 49, 0.98) 100%);
        padding: 8px;
        color: white;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.07);
    }

    .bracket-column.level-1 .bracket-match:nth-child(1) { top: 0%; }
    .bracket-column.level-1 .bracket-match:nth-child(2) { top: 12.5%; }
    .bracket-column.level-1 .bracket-match:nth-child(3) { top: 25%; }
    .bracket-column.level-1 .bracket-match:nth-child(4) { top: 37.5%; }
    .bracket-column.level-1 .bracket-match:nth-child(5) { top: 50%; }
    .bracket-column.level-1 .bracket-match:nth-child(6) { top: 62.5%; }
    .bracket-column.level-1 .bracket-match:nth-child(7) { top: 75%; }
    .bracket-column.level-1 .bracket-match:nth-child(8) { top: 87.5%; }

    .bracket-column.level-2 .bracket-match:nth-child(1) { top: 6.25%; }
    .bracket-column.level-2 .bracket-match:nth-child(2) { top: 31.25%; }
    .bracket-column.level-2 .bracket-match:nth-child(3) { top: 56.25%; }
    .bracket-column.level-2 .bracket-match:nth-child(4) { top: 81.25%; }

    .bracket-column.level-3 .bracket-match:nth-child(1) { top: 18.75%; }
    .bracket-column.level-3 .bracket-match:nth-child(2) { top: 68.75%; }

    .bracket-column.level-4 .bracket-match:nth-child(1) { top: 43.75%; }

    .bracket-column.level-5 .bracket-match:nth-child(1) { top: 37.5%; }
    .bracket-column.level-5 .bracket-match:nth-child(2) { top: 53.5%; }

    .bracket-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #1238ff;
        padding: 2px 7px;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.08em;
        color: white;
    }

    .bracket-team-line {
        display: grid;
        grid-template-columns: 17px 1fr 27px;
        align-items: center;
        gap: 5px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.075);
        padding: 5px;
    }

    .bracket-team-line + .bracket-team-line {
        margin-top: 4px;
    }

    .bracket-team-line img {
        width: 17px;
        height: 12px;
        border-radius: 3px;
        object-fit: cover;
    }

    .bracket-team-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 8px;
        font-weight: 900;
        color: #ffffff;
    }

    .bracket-score-readonly {
        height: 22px;
        width: 27px;
        border-radius: 7px;
        background: #ffffff;
        text-align: center;
        font-size: 10px;
        font-weight: 900;
        color: #080f2f;
        line-height: 22px;
    }

    .bracket-winner-readonly {
        margin-top: 5px;
        width: 100%;
        min-height: 22px;
        border-radius: 8px;
        background: #ffffff;
        padding: 5px;
        font-size: 8px;
        font-weight: 900;
        color: #080f2f;
        text-align: center;
    }

    .winner-card {
        background: linear-gradient(180deg, #102554 0%, #091935 100%);
        border: 1px solid rgba(255, 196, 0, 0.45);
    }

    .third-card {
        background: linear-gradient(180deg, #3a1d00 0%, #241300 100%);
        border: 1px solid rgba(255, 138, 0, 0.45);
    }
</style>

<section class="relative px-6 py-12">
    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-2xl bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Vista pública
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] md:text-6xl">
                    Quinielas de todos
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-8 text-[#080f2f]/65 md:text-lg">
                    Revisá la quiniela completa de cada participante en modo solo lectura.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a
                        href="{{ route('predictions.by_match') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-[#1238ff] px-6 py-4 text-sm font-black uppercase tracking-[0.14em] text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-[#080f2f]"
                    >
                        Ver pronósticos por partido
                    </a>

                    <a
                        href="{{ route('predictions.print') }}"
                        target="_blank"
                        class="inline-flex items-center justify-center rounded-2xl bg-[#080f2f] px-6 py-4 text-sm font-black uppercase tracking-[0.14em] text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-[#1238ff]"
                    >
                        Imprimir quinielas finalizadas
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Participante
                </p>

                <form method="GET" action="{{ route('predictions.public') }}" class="mt-4">
                    <select
                        name="user_id"
                        onchange="this.form.submit()"
                        class="h-14 w-full rounded-2xl border-0 bg-white px-4 text-sm font-black text-[#080f2f] outline-none"
                    >
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected($selectedUser->id === $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="mt-5 rounded-2xl bg-white/10 px-4 py-4">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-white/45">
                        Mostrando
                    </p>

                    <p class="mt-2 text-lg font-black">
                        {{ $selectedUser->name }}
                    </p>

                    <p class="mt-2 text-sm leading-6 text-white/65">
                        Esta pantalla no permite modificar marcadores.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-[2rem] bg-white p-4 shadow-xl ring-1 ring-black/5">
            <div class="grid gap-3 md:grid-cols-4">
                <button type="button" class="public-tab-btn active rounded-2xl px-4 py-3 text-sm" data-public-tab="groups">
                    Fase de grupos
                </button>

                <button type="button" class="public-tab-btn rounded-2xl px-4 py-3 text-sm" data-public-tab="tables">
                    Tablas
                </button>

                <button type="button" class="public-tab-btn rounded-2xl px-4 py-3 text-sm" data-public-tab="thirds">
                    Mejores terceros
                </button>

                <button type="button" class="public-tab-btn rounded-2xl px-4 py-3 text-sm" data-public-tab="bracket">
                    Llave
                </button>
            </div>
        </div>

        <div class="mt-6">
            <div class="public-tab-panel active" data-public-panel="groups">
                <div class="rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-black/5 md:p-7">
                    <h2 class="text-2xl font-black text-[#080f2f]">Fase de grupos</h2>

                    @if($matches->isEmpty())
                        <div class="mt-6 rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                            Todavía no hay partidos cargados.
                        </div>
                    @else
                        <div class="mt-6 grid gap-6 xl:grid-cols-2">
                            @foreach($matches as $groupName => $games)
                                @php
                                    $groupColors = ['#1238ff', '#e51b2b', '#159447', '#7c3aed', '#ffc400', '#ff7a1a'];
                                    $groupColor = $groupColors[($loop->iteration - 1) % count($groupColors)];
                                    $textColor = in_array($groupColor, ['#ffc400', '#ff7a1a']) ? '#080f2f' : '#ffffff';
                                @endphp

                                <div class="overflow-hidden rounded-[1.8rem] border border-[#dfe5f3] bg-white shadow-lg">
                                    <div class="flex items-center justify-between px-6 py-5" style="background-color: {{ $groupColor }}; color: {{ $textColor }};">
                                        <h2 class="text-xl font-black">Grupo {{ $groupName }}</h2>

                                        <span class="rounded-xl bg-white/20 px-3 py-2 text-xs font-black uppercase tracking-[0.12em]">
                                            {{ $games->count() }} partidos
                                        </span>
                                    </div>

                                    <div class="grid gap-4 bg-white p-5">
                                        @foreach($games->sortBy('match_date') as $match)
                                            @php
                                                $prediction = $predictions[$match->id] ?? null;
                                                $homeFlag = $flagUrl($match->homeTeam);
                                                $awayFlag = $flagUrl($match->awayTeam);
                                            @endphp

                                            <div class="rounded-3xl border border-[#e5ebf6] bg-[#f8faff] p-5">
                                                <div class="mb-4 flex items-center justify-between gap-3">
                                                    <p class="text-sm font-black text-[#080f2f]/50">
                                                        {{ optional($match->match_date)->format('d/m/Y') ?? 'Sin fecha' }}
                                                    </p>

                                                    <p class="rounded-xl bg-white px-3 py-1 text-xs font-black text-[#080f2f]/50 shadow-sm">
                                                        Solo lectura
                                                    </p>
                                                </div>

                                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                                                    <div class="text-center">
                                                        @if($homeFlag)
                                                            <img src="{{ $homeFlag }}" class="mx-auto h-10 w-14 rounded-lg object-cover shadow" alt="{{ $match->homeTeam?->name }}">
                                                        @else
                                                            <div class="mx-auto flex h-10 w-14 items-center justify-center rounded-lg bg-[#edf1ff] text-xs font-black text-[#1238ff]">---</div>
                                                        @endif

                                                        <p class="mt-2 text-sm font-black text-[#080f2f]">
                                                            {{ $match->homeTeam?->name ?? 'Equipo 1' }}
                                                        </p>
                                                    </div>

                                                    <div class="min-w-[105px] rounded-2xl bg-white px-5 py-4 text-center shadow">
                                                        @if($prediction && $prediction->predicted_home_score !== null && $prediction->predicted_away_score !== null)
                                                            <p class="text-3xl font-black text-[#080f2f]">
                                                                {{ $prediction->predicted_home_score }} - {{ $prediction->predicted_away_score }}
                                                            </p>
                                                        @else
                                                            <p class="text-sm font-black text-[#080f2f]/35">Sin pronóstico</p>
                                                        @endif
                                                    </div>

                                                    <div class="text-center">
                                                        @if($awayFlag)
                                                            <img src="{{ $awayFlag }}" class="mx-auto h-10 w-14 rounded-lg object-cover shadow" alt="{{ $match->awayTeam?->name }}">
                                                        @else
                                                            <div class="mx-auto flex h-10 w-14 items-center justify-center rounded-lg bg-[#edf1ff] text-xs font-black text-[#1238ff]">---</div>
                                                        @endif

                                                        <p class="mt-2 text-sm font-black text-[#080f2f]">
                                                            {{ $match->awayTeam?->name ?? 'Equipo 2' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="public-tab-panel" data-public-panel="tables">
                <div class="rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-black/5 md:p-7">
                    <h2 class="text-2xl font-black text-[#080f2f]">Tablas de grupos</h2>

                    <div class="mt-6 grid gap-5 xl:grid-cols-3">
                        @foreach(range('A', 'L') as $groupLetter)
                            @php
                                $groupRows = $standings->get($groupLetter, collect());
                            @endphp

                            <div class="overflow-hidden rounded-[1.8rem] border border-[#dfe5f3] bg-white shadow-sm">
                                <div class="bg-[#1238ff] px-5 py-4 text-white">
                                    <h3 class="text-lg font-black">Grupo {{ $groupLetter }}</h3>
                                </div>

                                <div class="p-4">
                                    <div class="table-header standing-header mb-3">
                                        <div>#</div>
                                        <div></div>
                                        <div>Equipo</div>
                                        <div class="text-center">Pts</div>
                                        <div class="text-center">DG</div>
                                    </div>

                                    @forelse($groupRows as $row)
                                        @php $teamFlag = $flagUrl($row->team); @endphp

                                        <div class="mb-2 grid grid-cols-[34px_28px_1fr_50px_50px] items-center gap-2 rounded-2xl bg-[#f8faff] px-2 py-3">
                                            <div class="text-sm font-black text-[#080f2f]">{{ $row->position }}</div>

                                            <div>
                                                @if($teamFlag)
                                                    <img src="{{ $teamFlag }}" class="h-5 w-7 rounded object-cover" alt="{{ $row->team?->name }}">
                                                @endif
                                            </div>

                                            <div>
                                                <p class="text-sm font-black text-[#080f2f]">{{ $row->team?->name ?? 'Pendiente' }}</p>
                                            </div>

                                            <div class="text-center text-sm font-black text-[#080f2f]">{{ $row->points }}</div>
                                            <div class="text-center text-sm font-black text-[#080f2f]">{{ $row->goal_difference > 0 ? '+' . $row->goal_difference : $row->goal_difference }}</div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl bg-[#f8faff] px-4 py-5 text-sm font-bold text-[#7c86a3]">
                                            Aún no hay datos calculados para este grupo.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="public-tab-panel" data-public-panel="thirds">
                <div class="rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-black/5 md:p-7">
                    <h2 class="text-2xl font-black text-[#080f2f]">Mejores terceros</h2>

                    <div class="mt-6 overflow-hidden rounded-[1.8rem] border border-[#dfe5f3] bg-white">
                        <div class="p-4">
                            <div class="table-header thirds-header mb-3">
                                <div>Pos</div>
                                <div></div>
                                <div>Equipo</div>
                                <div class="text-center">Grupo</div>
                                <div class="text-center">Pts</div>
                                <div class="text-center">Estado</div>
                            </div>

                            <div class="divide-y divide-[#edf1f8]">
                                @forelse($bestThirds as $index => $row)
                                    @php $teamFlag = $flagUrl($row->team); @endphp

                                    <div class="grid grid-cols-[60px_34px_1fr_80px_80px_130px] items-center gap-2 px-5 py-4">
                                        <div class="text-sm font-black text-[#080f2f]">{{ $index + 1 }}</div>

                                        <div>
                                            @if($teamFlag)
                                                <img src="{{ $teamFlag }}" class="h-5 w-7 rounded object-cover" alt="{{ $row->team?->name }}">
                                            @endif
                                        </div>

                                        <p class="text-sm font-black text-[#080f2f]">{{ $row->team?->name ?? 'Pendiente' }}</p>
                                        <div class="text-center text-sm font-black text-[#080f2f]">{{ $row->group_name }}</div>
                                        <div class="text-center text-sm font-black text-[#080f2f]">{{ $row->points }} pts</div>

                                        <div class="text-center">
                                            @if($index < 8)
                                                <span class="inline-flex rounded-xl bg-[#159447]/10 px-3 py-2 text-xs font-black text-[#159447]">Clasifica</span>
                                            @else
                                                <span class="inline-flex rounded-xl bg-[#e51b2b]/10 px-3 py-2 text-xs font-black text-[#e51b2b]">Eliminado</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-5 py-6 text-sm font-bold text-[#7c86a3]">
                                        Todavía no hay mejores terceros calculados.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="public-tab-panel" data-public-panel="bracket">
                <div class="rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-black/5 md:p-7">
                    <h2 class="text-2xl font-black text-[#080f2f]">Llave de eliminación</h2>
                    <p class="mt-1 text-sm font-medium text-[#080f2f]/55">Vista de solo lectura de la llave del participante.</p>

                    <div class="mt-6">
                        <div class="bracket-board">
                            <div class="bracket-grid">
                                @foreach($roundColumns as $column)
                                    <div class="bracket-column-wrap">
                                        <div class="bracket-stage-title">{{ $column['title'] }}</div>

                                        <div class="bracket-column {{ $column['class'] }} {{ $column['side'] }}">
                                            @foreach($column['slots'] as $slot)
                                                @php
                                                    $matchModel = $getSlot($slot);
                                                    $homeFlag = $flagUrl($matchModel?->homeTeam);
                                                    $awayFlag = $flagUrl($matchModel?->awayTeam);
                                                @endphp

                                                <div class="bracket-match {{ $slot === 104 ? 'winner-card' : '' }} {{ $slot === 103 ? 'third-card' : '' }}">
                                                    <div class="mb-2 flex items-center justify-between">
                                                        <span class="bracket-code">M{{ $slot }}</span>

                                                        @if($slot === 104)
                                                            <span class="text-[8px] font-black uppercase tracking-[0.18em] text-[#ffc400]">Final</span>
                                                        @elseif($slot === 103)
                                                            <span class="text-[8px] font-black uppercase tracking-[0.18em] text-[#ffb14a]">3er lugar</span>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <div class="bracket-team-line">
                                                            @if($homeFlag)
                                                                <img src="{{ $homeFlag }}" alt="">
                                                            @else
                                                                <span></span>
                                                            @endif

                                                            <span class="bracket-team-name">{{ $matchModel?->homeTeam?->name ?? 'Pendiente' }}</span>
                                                            <span class="bracket-score-readonly">{{ $matchModel?->predicted_home_score ?? '-' }}</span>
                                                        </div>

                                                        <div class="bracket-team-line">
                                                            @if($awayFlag)
                                                                <img src="{{ $awayFlag }}" alt="">
                                                            @else
                                                                <span></span>
                                                            @endif

                                                            <span class="bracket-team-name">{{ $matchModel?->awayTeam?->name ?? 'Pendiente' }}</span>
                                                            <span class="bracket-score-readonly">{{ $matchModel?->predicted_away_score ?? '-' }}</span>
                                                        </div>

                                                        <div class="bracket-winner-readonly">
                                                            Ganador: {{ $matchModel?->predictedWinnerTeam?->name ?? 'Pendiente' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        @if($canSeeRound32 ?? false)
            <div class="mt-10 rounded-[2rem] bg-white p-5 shadow-xl ring-1 ring-black/5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-black text-[#080f2f]">Dieciseisavos</h2>
                    <a href="{{ route('round32.by_match') }}" class="rounded-xl bg-[#1238ff] px-4 py-3 text-center text-sm font-black text-white">Ver los de todos</a>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach($round32Slots as $slot)
                        @php $match=$slot->match; $prediction=$match ? $round32Predictions->get($match->id) : null; @endphp
                        @if($match)
                            <div class="rounded-2xl bg-[#f4f6ff] p-4">
                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 text-center">
                                    <div class="text-sm font-black text-[#080f2f]">{{ $match->homeTeam->name }}</div>
                                    <div class="rounded-xl bg-white px-3 py-2 font-black text-[#080f2f]">{{ $prediction ? $prediction->predicted_home_score.' - '.$prediction->predicted_away_score : '-' }}</div>
                                    <div class="text-sm font-black text-[#080f2f]">{{ $match->awayTeam->name }}</div>
                                </div>
                                @if($prediction && $prediction->predicted_home_score === $prediction->predicted_away_score && $prediction->predictedWinner)
                                    <p class="mt-2 text-center text-xs font-bold text-[#080f2f]/55">Clasifica: {{ $prediction->predictedWinner->name }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        @if($canSeeRound16 ?? false)
            <div class="mt-10 rounded-[2rem] bg-white p-5 shadow-xl ring-1 ring-black/5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-black text-[#080f2f]">Octavos</h2>
                    <a href="{{ route('round16.by_match') }}" class="rounded-xl bg-[#1238ff] px-4 py-3 text-center text-sm font-black text-white">Ver los de todos</a>
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach($round16Slots as $slot)
                        @php $match=$slot->match; $prediction=$match ? $round16Predictions->get($match->id) : null; @endphp
                        @if($match)
                            <div class="rounded-2xl bg-[#f4f6ff] p-4">
                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 text-center">
                                    <div class="text-sm font-black text-[#080f2f]">{{ $match->homeTeam->name }}</div>
                                    <div class="rounded-xl bg-white px-3 py-2 font-black text-[#080f2f]">{{ $prediction ? $prediction->predicted_home_score.' - '.$prediction->predicted_away_score : '-' }}</div>
                                    <div class="text-sm font-black text-[#080f2f]">{{ $match->awayTeam->name }}</div>
                                </div>
                                @if($prediction && $prediction->predicted_home_score === $prediction->predicted_away_score && $prediction->predictedWinner)
                                    <p class="mt-2 text-center text-xs font-bold text-[#080f2f]/55">Clasifica: {{ $prediction->predictedWinner->name }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('[data-public-tab]');
        const panels = document.querySelectorAll('[data-public-panel]');

        function activatePublicTab(tabName) {
            buttons.forEach((button) => {
                button.classList.toggle('active', button.dataset.publicTab === tabName);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.publicPanel === tabName);
            });
        }

        buttons.forEach((button) => {
            button.addEventListener('click', function () {
                activatePublicTab(this.dataset.publicTab);
            });
        });

        activatePublicTab('groups');
    });
</script>
@endsection