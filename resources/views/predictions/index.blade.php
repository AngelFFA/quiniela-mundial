@extends('layouts.app', ['title' => 'Mi Quiniela - Quiniela Mundial'])

@section('content')
@php
    use Illuminate\Support\Carbon;

    $groupBuckets = [
        'set_1' => ['A', 'B', 'C', 'D'],
        'set_2' => ['E', 'F', 'G', 'H'],
        'set_3' => ['I', 'J', 'K', 'L'],
    ];

    $totalMatches = $matches->flatten(1)->count();

    $completedMatches = $predictions->filter(function ($prediction) {
        return $prediction->predicted_home_score !== null
            && $prediction->predicted_away_score !== null;
    })->count();

    $progress = $totalMatches > 0 ? round(($completedMatches / $totalMatches) * 100) : 0;

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
        [
            'title' => 'Round of 32',
            'class' => 'level-1',
            'side' => '',
            'slots' => [74, 77, 73, 75, 83, 84, 81, 82],
        ],
        [
            'title' => 'R16',
            'class' => 'level-2',
            'side' => '',
            'slots' => [89, 90, 93, 94],
        ],
        [
            'title' => 'QF',
            'class' => 'level-3',
            'side' => '',
            'slots' => [97, 98],
        ],
        [
            'title' => 'SF',
            'class' => 'level-4',
            'side' => '',
            'slots' => [101],
        ],
        [
            'title' => 'Final',
            'class' => 'level-5',
            'side' => '',
            'slots' => [104, 103],
        ],
        [
            'title' => 'SF',
            'class' => 'level-4',
            'side' => 'right',
            'slots' => [102],
        ],
        [
            'title' => 'QF',
            'class' => 'level-3',
            'side' => 'right',
            'slots' => [99, 100],
        ],
        [
            'title' => 'R16',
            'class' => 'level-2',
            'side' => 'right',
            'slots' => [91, 92, 95, 96],
        ],
        [
            'title' => 'Round of 32',
            'class' => 'level-1',
            'side' => 'right',
            'slots' => [76, 78, 79, 80, 86, 88, 85, 87],
        ],
    ];

    $activeTab = $activeTab ?? 'groups';
    $quinielaFinalizada = (bool) (auth()->user()->quiniela_finalizada ?? false);
    $quinielaFinalizadaAt = auth()->user()->quiniela_finalizada_at ?? null;
    $yaTieneLlaves = (bool) ($yaTieneLlaves ?? false);
@endphp

<style>
    .main-tab-btn,
    .sub-tab-btn {
        border: 1px solid #dbe2f1;
        background: #ffffff;
        color: #5d6785;
        font-weight: 900;
        transition: 0.2s ease;
    }

    .main-tab-btn.active,
    .sub-tab-btn.active {
        background: #1238ff;
        border-color: #1238ff;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(18, 56, 255, 0.18);
    }

    .main-tab-panel,
    .group-tab-panel {
        display: none;
    }

    .main-tab-panel.active,
    .group-tab-panel.active {
        display: block;
    }

    .bracket-board {
        width: 100%;
        overflow: hidden;
        border-radius: 28px;
        background: linear-gradient(135deg, #071833 0%, #0b2145 55%, #061329 100%);
        padding: 18px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .bracket-grid {
        width: 100%;
        display: grid;
        grid-template-columns: 1.22fr 1.06fr 0.96fr 0.88fr 1.08fr 0.88fr 0.96fr 1.06fr 1.22fr;
        gap: 10px;
        align-items: start;
    }

    .bracket-stage-title {
        height: 22px;
        text-align: center;
        font-size: 8px;
        letter-spacing: 0.12em;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.72);
        text-transform: uppercase;
        margin-bottom: 8px;
        white-space: nowrap;
    }

    .bracket-column {
        position: relative;
        height: 1010px;
    }

    .bracket-match {
        position: absolute;
        left: 0;
        right: 0;
        min-height: 104px;
        border-radius: 13px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: linear-gradient(180deg, rgba(15, 39, 78, 0.98) 0%, rgba(8, 25, 53, 0.98) 100%);
        padding: 7px;
        color: white;
        box-shadow:
            0 10px 22px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .bracket-match::before,
    .bracket-match::after {
        display: none;
        content: none;
    }

    .bracket-column.level-1 .bracket-match:nth-child(1) { top: 0; }
    .bracket-column.level-1 .bracket-match:nth-child(2) { top: 128px; }
    .bracket-column.level-1 .bracket-match:nth-child(3) { top: 256px; }
    .bracket-column.level-1 .bracket-match:nth-child(4) { top: 384px; }
    .bracket-column.level-1 .bracket-match:nth-child(5) { top: 512px; }
    .bracket-column.level-1 .bracket-match:nth-child(6) { top: 640px; }
    .bracket-column.level-1 .bracket-match:nth-child(7) { top: 768px; }
    .bracket-column.level-1 .bracket-match:nth-child(8) { top: 896px; }

    .bracket-column.level-2 .bracket-match:nth-child(1) { top: 64px; }
    .bracket-column.level-2 .bracket-match:nth-child(2) { top: 320px; }
    .bracket-column.level-2 .bracket-match:nth-child(3) { top: 576px; }
    .bracket-column.level-2 .bracket-match:nth-child(4) { top: 832px; }

    .bracket-column.level-3 .bracket-match:nth-child(1) { top: 192px; }
    .bracket-column.level-3 .bracket-match:nth-child(2) { top: 704px; }

    .bracket-column.level-4 .bracket-match:nth-child(1) { top: 448px; }

    .bracket-column.level-5 .bracket-match:nth-child(1) { top: 382px; }
    .bracket-column.level-5 .bracket-match:nth-child(2) { top: 536px; }

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
        grid-template-columns: 18px minmax(0, 1fr) 28px;
        align-items: center;
        gap: 4px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.075);
        padding: 4px;
    }

    .bracket-team-line + .bracket-team-line {
        margin-top: 4px;
    }

    .bracket-team-line img {
        width: 18px;
        height: 13px;
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

    .bracket-score-input {
        height: 22px;
        width: 28px;
        border: 0;
        border-radius: 7px;
        background: #ffffff;
        text-align: center;
        font-size: 10px;
        font-weight: 900;
        color: #080f2f;
        outline: none;
    }

    .bracket-score-input:focus {
        box-shadow: 0 0 0 2px rgba(18, 56, 255, 0.5);
    }

    .bracket-winner-select {
        display: block;
        margin-top: 5px;
        width: 100%;
        height: 24px;
        border: 0;
        border-radius: 8px;
        background: #ffffff;
        padding: 0 5px;
        font-size: 8px;
        font-weight: 800;
        color: #080f2f;
        outline: none;
    }

    .bracket-winner-select:focus {
        box-shadow: 0 0 0 2px rgba(18, 56, 255, 0.5);
    }

    @media (max-width: 1279px) {
        .bracket-board {
            padding: 10px;
        }

        .bracket-grid {
            gap: 4px;
            grid-template-columns: repeat(9, minmax(0, 1fr));
        }

        .bracket-stage-title {
            font-size: 6px;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }

        .bracket-column {
            height: 930px;
        }

        .bracket-match {
            min-height: 96px;
            border-radius: 8px;
            padding: 4px;
        }

        .bracket-column.level-1 .bracket-match:nth-child(1) { top: 0; }
        .bracket-column.level-1 .bracket-match:nth-child(2) { top: 118px; }
        .bracket-column.level-1 .bracket-match:nth-child(3) { top: 236px; }
        .bracket-column.level-1 .bracket-match:nth-child(4) { top: 354px; }
        .bracket-column.level-1 .bracket-match:nth-child(5) { top: 472px; }
        .bracket-column.level-1 .bracket-match:nth-child(6) { top: 590px; }
        .bracket-column.level-1 .bracket-match:nth-child(7) { top: 708px; }
        .bracket-column.level-1 .bracket-match:nth-child(8) { top: 826px; }

        .bracket-column.level-2 .bracket-match:nth-child(1) { top: 59px; }
        .bracket-column.level-2 .bracket-match:nth-child(2) { top: 295px; }
        .bracket-column.level-2 .bracket-match:nth-child(3) { top: 531px; }
        .bracket-column.level-2 .bracket-match:nth-child(4) { top: 767px; }

        .bracket-column.level-3 .bracket-match:nth-child(1) { top: 177px; }
        .bracket-column.level-3 .bracket-match:nth-child(2) { top: 649px; }

        .bracket-column.level-4 .bracket-match:nth-child(1) { top: 413px; }

        .bracket-column.level-5 .bracket-match:nth-child(1) { top: 354px; }
        .bracket-column.level-5 .bracket-match:nth-child(2) { top: 502px; }

        .bracket-code {
            font-size: 6px;
            padding: 1px 4px;
        }

        .bracket-team-line {
            grid-template-columns: 11px 1fr 18px;
            gap: 2px;
            border-radius: 6px;
            padding: 3px;
        }

        .bracket-team-line img {
            width: 11px;
            height: 8px;
        }

        .bracket-team-name {
            font-size: 6px;
        }

        .bracket-score-input {
            width: 22px;
            height: 20px;
            font-size: 7px;
            border-radius: 5px;
        }

        .bracket-winner-select {
            height: 22px;
            font-size: 6px;
            border-radius: 5px;
            padding: 0 2px;
        }
    }

    @media (max-width: 640px) {
        .bracket-board {
            padding: 6px;
            border-radius: 18px;
            overflow-x: auto;
        }

        .bracket-grid {
            min-width: 980px;
            gap: 4px;
        }

        .bracket-column {
            height: 900px;
        }

        .bracket-match {
            min-height: 94px;
        }
    }

    .winner-card {
        background: linear-gradient(180deg, #102554 0%, #091935 100%);
        border: 1px solid rgba(255, 196, 0, 0.45);
    }

    .third-card {
        background: linear-gradient(180deg, #3a1d00 0%, #241300 100%);
        border: 1px solid rgba(255, 138, 0, 0.45);
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
</style>

<section class="px-6 py-10">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.28em] text-[#1238ff]">
                    Mi Quiniela
                </div>

                <h1 class="mt-5 text-4xl font-black text-[#080f2f] md:text-6xl">
                    Completá tu quiniela
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-7 text-[#080f2f]/65">
                    Completá primero la fase de grupos. Después podés revisar tablas, mejores terceros y llenar la llave de eliminación.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-5 text-white shadow-2xl">
                <p class="text-[11px] font-black uppercase tracking-[0.26em] text-white/50">Resumen</p>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-[#1238ff] p-3 text-center">
                        <p class="text-2xl font-black">{{ $completedMatches }}</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.18em] text-white/70">Guardados</p>
                    </div>

                    <div class="rounded-2xl bg-[#159447] p-3 text-center">
                        <p class="text-2xl font-black">{{ $totalMatches }}</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.18em] text-white/70">Partidos</p>
                    </div>

                    <div class="rounded-2xl bg-[#ffc400] p-3 text-center text-[#080f2f]">
                        <p class="text-2xl font-black">{{ $progress }}%</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.18em] text-[#080f2f]/75">Avance</p>
                    </div>
                </div>

                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full bg-[#1238ff]" style="width: {{ $progress }}%;"></div>
                </div>

                <p class="mt-4 text-sm font-medium text-white/65">
                    Guardá la fase de grupos para calcular tablas, mejores terceros y cruces. Si después cambiás grupos, la llave puede recalcularse.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-3xl border border-[#159447]/20 bg-[#159447]/10 px-5 py-4 text-sm font-bold text-[#0c6f32]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 rounded-3xl border border-[#e51b2b]/20 bg-[#e51b2b]/10 px-5 py-4 text-sm font-bold text-[#b61522]">
                {{ session('error') }}
            </div>
        @endif

        @if($quinielaFinalizada)
            <div class="mt-6 rounded-3xl border border-[#159447]/20 bg-[#159447]/10 px-5 py-4 text-sm font-bold text-[#0c6f32]">
                Fase de grupos finalizada. Los dieciseisavos se completan en su apartado correspondiente.
            </div>
        @endif

        <div class="mt-8 rounded-[2rem] bg-white p-4 shadow-xl ring-1 ring-black/5">
            <div class="grid gap-3 md:grid-cols-7">
                <button type="button" class="main-tab-btn active rounded-2xl px-4 py-3 text-sm" data-main-tab="groups">
                    Pronósticos
                </button>

                <button type="button" class="main-tab-btn rounded-2xl px-4 py-3 text-sm" data-main-tab="tables">
                    Tablas
                </button>

                <button type="button" class="main-tab-btn rounded-2xl px-4 py-3 text-sm" data-main-tab="thirds">
                    Mejores terceros
                </button>

                <button type="button" class="main-tab-btn rounded-2xl px-4 py-3 text-sm" data-main-tab="bracket">
                    Llave
                </button>

                <a href="{{ route('round32.index') }}" class="main-tab-btn rounded-2xl px-4 py-3 text-center text-sm">
                    Dieciseisavos
                </a>

                <a href="{{ route('round16.index') }}" class="main-tab-btn rounded-2xl px-4 py-3 text-center text-sm">
                    Octavos
                </a>

                <a href="{{ route('round8.index') }}" class="main-tab-btn rounded-2xl px-4 py-3 text-center text-sm">
                    Cuartos
                </a>
            </div>
        </div>

        <div class="mt-6">
            <div class="main-tab-panel active" data-main-panel="groups">
                <form id="grupos-form" method="POST" action="{{ route('predictions.store') }}" onsubmit="return confirmarGuardadoGrupos();">
                    @csrf
                    <input type="hidden" name="active_tab" value="tables">
                    <input type="hidden" name="reset_bracket" id="reset_bracket" value="0">

                    <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-black/5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-2xl font-black text-[#080f2f]">Fase de grupos</h2>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">
                                    Llená los marcadores de grupos. Al guardar, se actualizan las tablas y se calculan los cruces.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 md:grid-cols-3">
                            <button type="button" class="sub-tab-btn active rounded-2xl px-4 py-3 text-sm" data-group-tab="set_1">
                                Grupos A - D
                            </button>

                            <button type="button" class="sub-tab-btn rounded-2xl px-4 py-3 text-sm" data-group-tab="set_2">
                                Grupos E - H
                            </button>

                            <button type="button" class="sub-tab-btn rounded-2xl px-4 py-3 text-sm" data-group-tab="set_3">
                                Grupos I - L
                            </button>
                        </div>

                        @foreach($groupBuckets as $bucketKey => $bucketGroups)
                            <div class="group-tab-panel {{ $bucketKey === 'set_1' ? 'active' : '' }} mt-6" data-group-panel="{{ $bucketKey }}">
                                <div class="grid gap-6 xl:grid-cols-2">
                                    @foreach($bucketGroups as $groupLetter)
                                        @php
                                            $groupMatches = $matches->get($groupLetter, collect());
                                            $groupDone = $groupMatches->filter(function ($match) use ($predictions) {
                                                $prediction = $predictions->get($match->id);

                                                return $prediction
                                                    && $prediction->predicted_home_score !== null
                                                    && $prediction->predicted_away_score !== null;
                                            })->count();
                                        @endphp

                                        <div class="rounded-[2rem] border border-[#dfe5f3] bg-[#f8faff] p-5">
                                            <div class="flex items-center justify-between">
                                                <div class="rounded-2xl bg-[#1238ff] px-4 py-2 text-sm font-black text-white">
                                                    Grupo {{ $groupLetter }}
                                                </div>

                                                <div class="rounded-full bg-white px-4 py-2 text-xs font-black text-[#6b7592] shadow-sm">
                                                    {{ $groupDone }}/{{ $groupMatches->count() }} completos
                                                </div>
                                            </div>

                                            <div class="mt-5 grid gap-4">
                                                @foreach($groupMatches as $match)
                                                    @php
                                                        $prediction = $predictions->get($match->id);
                                                        $homeValue = old("predictions.{$match->id}.home", $prediction->predicted_home_score ?? '');
                                                        $awayValue = old("predictions.{$match->id}.away", $prediction->predicted_away_score ?? '');
                                                        $homeFlag = $flagUrl($match->homeTeam);
                                                        $awayFlag = $flagUrl($match->awayTeam);
                                                    @endphp

                                                    <div class="rounded-[1.6rem] border border-[#e4eaf6] bg-white p-3 shadow-sm sm:p-4">
                                                        <div class="mb-3 flex items-center justify-between gap-2">
                                                            <p class="text-sm font-black text-[#66708b]">
                                                                {{ $match->match_date ? Carbon::parse($match->match_date)->format('d/m/Y') : '--/--/----' }}
                                                            </p>

                                                            <span class="shrink-0 rounded-full bg-[#edf1ff] px-3 py-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#1238ff] sm:text-[11px]">
                                                                Grupo {{ $groupLetter }}
                                                            </span>
                                                        </div>

                                                        <div class="grid grid-cols-[64px_104px_64px] items-center justify-center gap-2 sm:grid-cols-[minmax(0,1fr)_120px_minmax(0,1fr)] sm:gap-4">
                                                            <div class="flex min-w-0 flex-col items-center justify-center gap-1 text-center sm:flex-row sm:justify-start sm:gap-3 sm:text-left">
                                                                @if($homeFlag)
                                                                    <img src="{{ $homeFlag }}" alt="{{ $match->homeTeam->name }}" class="h-8 w-11 shrink-0 rounded-lg object-cover ring-1 ring-black/5 sm:h-11 sm:w-14 sm:rounded-xl">
                                                                @else
                                                                    <div class="flex h-8 w-11 shrink-0 items-center justify-center rounded-lg bg-[#edf1ff] text-[10px] font-black text-[#1238ff] sm:h-11 sm:w-14 sm:rounded-xl sm:text-xs">
                                                                        ---
                                                                    </div>
                                                                @endif

                                                                <p class="w-full min-w-0 text-center text-[11px] font-black leading-tight text-[#080f2f] sm:text-left sm:text-sm">
                                                                    <span class="block sm:hidden">{{ $match->homeTeam?->short_name ?? $match->homeTeam?->code ?? 'EQ1' }}</span>
                                                                    <span class="hidden truncate sm:block">{{ $match->homeTeam?->name ?? 'Equipo 1' }}</span>
                                                                </p>
                                                            </div>

                                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    name="predictions[{{ $match->id }}][home]"
                                                                    value="{{ $homeValue }}"
                                                                    data-original="{{ $homeValue }}"
                                                                    @disabled($quinielaFinalizada)
                                                                    class="group-score-input h-12 w-11 rounded-xl border border-[#d7dfef] bg-white text-center text-lg font-black text-[#080f2f] outline-none focus:border-[#1238ff] focus:ring-2 focus:ring-[#1238ff]/15 sm:h-14 sm:w-14 sm:rounded-2xl sm:text-xl"
                                                                >

                                                                <span class="text-sm font-black text-[#9ba5bf] sm:text-lg">-</span>

                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    name="predictions[{{ $match->id }}][away]"
                                                                    value="{{ $awayValue }}"
                                                                    data-original="{{ $awayValue }}"
                                                                    @disabled($quinielaFinalizada)
                                                                    class="group-score-input h-12 w-11 rounded-xl border border-[#d7dfef] bg-white text-center text-lg font-black text-[#080f2f] outline-none focus:border-[#1238ff] focus:ring-2 focus:ring-[#1238ff]/15 sm:h-14 sm:w-14 sm:rounded-2xl sm:text-xl"
                                                                >
                                                            </div>

                                                            <div class="flex min-w-0 flex-col-reverse items-center justify-center gap-1 text-center sm:flex-row sm:justify-end sm:gap-3 sm:text-right">
                                                                <p class="w-full min-w-0 text-center text-[11px] font-black leading-tight text-[#080f2f] sm:text-right sm:text-sm">
                                                                    <span class="block sm:hidden">{{ $match->awayTeam?->short_name ?? $match->awayTeam?->code ?? 'EQ2' }}</span>
                                                                    <span class="hidden truncate sm:block">{{ $match->awayTeam?->name ?? 'Equipo 2' }}</span>
                                                                </p>

                                                                @if($awayFlag)
                                                                    <img src="{{ $awayFlag }}" alt="{{ $match->awayTeam->name }}" class="h-8 w-11 shrink-0 rounded-lg object-cover ring-1 ring-black/5 sm:h-11 sm:w-14 sm:rounded-xl">
                                                                @else
                                                                    <div class="flex h-8 w-11 shrink-0 items-center justify-center rounded-lg bg-[#edf1ff] text-[10px] font-black text-[#1238ff] sm:h-11 sm:w-14 sm:rounded-xl sm:text-xs">
                                                                        ---
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8 flex flex-col justify-end gap-3 sm:flex-row">
                            <button
                                type="submit"
                                class="rounded-2xl bg-[#1238ff] px-6 py-3 text-sm font-black text-white shadow-lg transition hover:bg-[#0e2ed1] {{ $quinielaFinalizada ? 'cursor-not-allowed opacity-50' : '' }}"
                                @disabled($quinielaFinalizada)
                            >
                                Guardar fase de grupos
                            </button>

                            <button
                                type="submit"
                                form="finalizar-quiniela-form"
                                onclick="return confirmarFinalizacion();"
                                class="rounded-2xl bg-[#159447] px-6 py-3 text-sm font-black text-white shadow-lg transition hover:bg-[#0c6f32] {{ $quinielaFinalizada ? 'cursor-not-allowed opacity-50' : '' }}"
                                @disabled($quinielaFinalizada)
                            >
                                Finalizar quiniela
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="main-tab-panel" data-main-panel="tables">
                <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-black/5">
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
                                        @php
                                            $teamFlag = $flagUrl($row->team);
                                        @endphp

                                        <div class="mb-2 grid grid-cols-[34px_28px_1fr_50px_50px] items-center gap-2 rounded-2xl bg-[#f8faff] px-2 py-3">
                                            <div class="text-sm font-black text-[#080f2f]">
                                                {{ $row->position }}
                                            </div>

                                            <div>
                                                @if($teamFlag)
                                                    <img src="{{ $teamFlag }}" class="h-5 w-7 rounded object-cover" alt="{{ $row->team?->name }}">
                                                @endif
                                            </div>

                                            <div>
                                                <p class="text-sm font-black text-[#080f2f]">
                                                    {{ $row->team?->name ?? 'Pendiente' }}
                                                </p>
                                            </div>

                                            <div class="text-center text-sm font-black text-[#080f2f]">
                                                {{ $row->points }}
                                            </div>

                                            <div class="text-center text-sm font-black text-[#080f2f]">
                                                {{ $row->goal_difference > 0 ? '+' . $row->goal_difference : $row->goal_difference }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl bg-[#f8faff] px-4 py-5 text-sm font-bold text-[#7c86a3]">
                                            Aún no hay datos calculados.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="main-tab-panel" data-main-panel="thirds">
                <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-black/5">
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
                                    @php
                                        $teamFlag = $flagUrl($row->team);
                                    @endphp

                                    <div class="grid grid-cols-[60px_34px_1fr_80px_80px_130px] items-center gap-2 px-5 py-4">
                                        <div class="text-sm font-black text-[#080f2f]">
                                            {{ $index + 1 }}
                                        </div>

                                        <div>
                                            @if($teamFlag)
                                                <img src="{{ $teamFlag }}" class="h-5 w-7 rounded object-cover" alt="{{ $row->team?->name }}">
                                            @endif
                                        </div>

                                        <p class="text-sm font-black text-[#080f2f]">
                                            {{ $row->team?->name ?? 'Pendiente' }}
                                        </p>

                                        <div class="text-center text-sm font-black text-[#080f2f]">
                                            {{ $row->group_name }}
                                        </div>

                                        <div class="text-center text-sm font-black text-[#080f2f]">
                                            {{ $row->points }} pts
                                        </div>

                                        <div class="text-center">
                                            @if($index < 8)
                                                <span class="inline-flex rounded-full bg-[#159447]/10 px-3 py-2 text-xs font-black text-[#159447]">
                                                    Clasifica
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-[#e51b2b]/10 px-3 py-2 text-xs font-black text-[#e51b2b]">
                                                    Eliminado
                                                </span>
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

            <div class="main-tab-panel" data-main-panel="bracket">
                <form method="POST" action="{{ route('predictions.store') }}" onsubmit="return confirmarGuardadoLlave();">
                    @csrf
                    <input type="hidden" name="active_tab" value="bracket">

                    <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-black/5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black text-[#080f2f]">Llave de eliminación</h2>
                                <p class="mt-1 text-sm font-medium text-[#080f2f]/55">
                                    Ingresá los marcadores de cada cruce y guardá la llave.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="bracket-board">
                                <div class="bracket-grid">
                                    @foreach($roundColumns as $column)
                                        <div>
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

                                                                <span class="bracket-team-name">
                                                                    {{ $matchModel?->homeTeam?->name ?? 'Pendiente' }}
                                                                </span>

                                                                @if($matchModel)
                                                                    <input
                                                                        type="number"
                                                                        min="0"
                                                                        name="bracket[{{ $matchModel->id }}][home]"
                                                                        value="{{ old("bracket.{$matchModel->id}.home", $matchModel->predicted_home_score) }}"
                                                                        @disabled($quinielaFinalizada)
                                                                        class="bracket-score-input"
                                                                    >
                                                                @else
                                                                    <input type="text" value="-" class="bracket-score-input" disabled>
                                                                @endif
                                                            </div>

                                                            <div class="bracket-team-line">
                                                                @if($awayFlag)
                                                                    <img src="{{ $awayFlag }}" alt="">
                                                                @else
                                                                    <span></span>
                                                                @endif

                                                                <span class="bracket-team-name">
                                                                    {{ $matchModel?->awayTeam?->name ?? 'Pendiente' }}
                                                                </span>

                                                                @if($matchModel)
                                                                    <input
                                                                        type="number"
                                                                        min="0"
                                                                        name="bracket[{{ $matchModel->id }}][away]"
                                                                        value="{{ old("bracket.{$matchModel->id}.away", $matchModel->predicted_away_score) }}"
                                                                        @disabled($quinielaFinalizada)
                                                                        class="bracket-score-input"
                                                                    >
                                                                @else
                                                                    <input type="text" value="-" class="bracket-score-input" disabled>
                                                                @endif
                                                            </div>

                                                            @if($matchModel && $matchModel->homeTeam && $matchModel->awayTeam)
                                                                <select name="bracket[{{ $matchModel->id }}][winner]" class="bracket-winner-select" @disabled($quinielaFinalizada)>
                                                                    <option value="">Desempate</option>

                                                                    <option
                                                                        value="{{ $matchModel->home_team_id }}"
                                                                        @selected((int) old("bracket.{$matchModel->id}.winner", $matchModel->predicted_winner_team_id) === (int) $matchModel->home_team_id)
                                                                    >
                                                                        {{ $matchModel->homeTeam->name }}
                                                                    </option>

                                                                    <option
                                                                        value="{{ $matchModel->away_team_id }}"
                                                                        @selected((int) old("bracket.{$matchModel->id}.winner", $matchModel->predicted_winner_team_id) === (int) $matchModel->away_team_id)
                                                                    >
                                                                        {{ $matchModel->awayTeam->name }}
                                                                    </option>
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button
                                type="submit"
                                class="rounded-2xl bg-[#1238ff] px-6 py-3 text-sm font-black text-white shadow-lg transition hover:bg-[#0e2ed1] {{ $quinielaFinalizada ? 'cursor-not-allowed opacity-50' : '' }}"
                                @disabled($quinielaFinalizada)
                            >
                                Guardar llave
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="finalizar-quiniela-form" method="POST" action="{{ route('predictions.finalize') }}" class="hidden">
        @csrf
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentMainTab = @json($activeTab);
        const yaTieneLlaves = @json($yaTieneLlaves);

        const mainButtons = document.querySelectorAll('[data-main-tab]');
        const mainPanels = document.querySelectorAll('[data-main-panel]');

        function activateMainTab(tabName) {
            mainButtons.forEach((button) => {
                button.classList.toggle('active', button.dataset.mainTab === tabName);
            });

            mainPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.mainPanel === tabName);
            });
        }

        mainButtons.forEach((button) => {
            button.addEventListener('click', function () {
                activateMainTab(this.dataset.mainTab);
            });
        });

        activateMainTab(currentMainTab || 'groups');

        const groupButtons = document.querySelectorAll('[data-group-tab]');
        const groupPanels = document.querySelectorAll('[data-group-panel]');

        function activateGroupTab(tabName) {
            groupButtons.forEach((button) => {
                button.classList.toggle('active', button.dataset.groupTab === tabName);
            });

            groupPanels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.groupPanel === tabName);
            });
        }

        groupButtons.forEach((button) => {
            button.addEventListener('click', function () {
                activateGroupTab(this.dataset.groupTab);
            });
        });

        activateGroupTab('set_1');

        function gruposCambiaron() {
            const inputs = document.querySelectorAll('.group-score-input');

            for (const input of inputs) {
                const actual = input.value ?? '';
                const original = input.dataset.original ?? '';

                if (actual !== original) {
                    return true;
                }
            }

            return false;
        }

        window.confirmarGuardadoGrupos = function () {
            @if($quinielaFinalizada)
                alert('Su quiniela ya fue finalizada y no puede modificarse.');
                return false;
            @else
                const resetBracket = document.getElementById('reset_bracket');
                resetBracket.value = '0';

                if (yaTieneLlaves && gruposCambiaron()) {
                    const confirmar = confirm(
                        'Ya tenías pronósticos guardados en la llave de eliminación.\n\n' +
                        'Si cambias la fase de grupos, pueden cambiar los clasificados y los cruces.\n\n' +
                        'Al continuar, se guardará la fase de grupos, se recalculará la llave y se borrarán los pronósticos que ya habías llenado en octavos, cuartos, semifinales y final.\n\n' +
                        '¿Deseas continuar?'
                    );

                    if (!confirmar) {
                        return false;
                    }

                    resetBracket.value = '1';
                }

                return true;
            @endif
        };

        window.confirmarGuardadoLlave = function () {
            @if($quinielaFinalizada)
                alert('Su quiniela ya fue finalizada y no puede modificarse.');
                return false;
            @else
                return confirm(
                    'Se guardarán los marcadores y ganadores de la llave de eliminación.\n\n' +
                    '¿Deseas continuar?'
                );
            @endif
        };

        window.confirmarFinalizacion = function () {
            @if($quinielaFinalizada)
                alert('Su quiniela ya fue finalizada anteriormente.');
                return false;
            @else
                return confirm(
                    '¿Deseas finalizar tu quiniela?\n\n' +
                    'Al finalizar, tus pronósticos quedarán enviados definitivamente y ya no podrás modificarlos.'
                );
            @endif
        };
    });
</script>
@endsection