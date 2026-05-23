@extends('layouts.app', ['title' => 'Simulador - Quiniela Mundial'])

@section('content')
@php
    $roundOrder = [
        'Dieciseisavos',
        'Octavos',
        'Cuartos',
        'Semifinales',
        'Tercer lugar',
        'Final',
    ];

    $matchesByRound = $bracketMatches->groupBy('round');

    $finalMatch = $bracketMatches->firstWhere('round', 'Final');
    $champion = $finalMatch?->predictedWinnerTeam;
@endphp

<section class="relative px-6 py-12">
    <div class="absolute -left-32 top-20 h-[420px] w-[420px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -left-20 top-72 h-[340px] w-[340px] rounded-full bg-[#159447]"></div>
    <div class="absolute -right-16 top-16 h-[430px] w-[430px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-100px] top-[360px] h-[420px] w-[420px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Mi quiniela
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Eliminatorias
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Revisá las tablas generadas por tus marcadores de grupos y completá los marcadores de cada ronda hasta definir campeón.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Participante
                </p>

                <form method="GET" action="{{ route('bracket.simulator') }}" class="mt-4">
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

                @if($selectedUser->id === Auth::id())
                    <form method="POST" action="{{ route('bracket.generate') }}" class="mt-4">
                        @csrf
                        <button class="h-14 w-full rounded-2xl bg-[#1238ff] px-5 text-sm font-black text-white">
                            Generar desde grupos
                        </button>
                    </form>
                @endif

                @if($champion)
                    <div class="mt-5 rounded-2xl bg-white/10 p-5">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-white/45">
                            Campeón pronosticado
                        </p>
                        <p class="mt-2 text-2xl font-black">
                            {{ $champion->name }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-[#dcfce7] px-5 py-4 text-sm font-black text-[#166534]">
                {{ session('success') }}
            </div>
        @endif

        @if($standings->isEmpty())
            <div class="mt-10 rounded-[2rem] bg-white p-8 text-center shadow-2xl">
                <p class="text-lg font-black text-[#080f2f]">
                    Primero guardá los marcadores de fase de grupos.
                </p>

                <a
                    href="{{ route('predictions.index') }}"
                    class="mt-5 inline-flex rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white"
                >
                    Ir a pronósticos
                </a>
            </div>
        @else
            <div class="mt-10 grid gap-6 xl:grid-cols-[340px_1fr]">
                <aside class="space-y-6">
                    <div class="rounded-[2rem] bg-white p-6 shadow-2xl">
                        <h2 class="text-2xl font-black text-[#080f2f]">
                            Mejores terceros
                        </h2>

                        <div class="mt-5 space-y-3">
                            @foreach($bestThirds as $index => $row)
                                <div class="rounded-2xl bg-[#f4f6ff] p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-black text-[#080f2f]">
                                                {{ $index + 1 }}. {{ $row->team->name }}
                                            </p>
                                            <p class="mt-1 text-xs font-bold text-[#080f2f]/45">
                                                Grupo {{ $row->group_name }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-sm font-black text-[#080f2f]">
                                                {{ $row->points }}
                                            </p>
                                            <p class="text-xs font-bold text-[#080f2f]/45">
                                                pts
                                            </p>
                                        </div>
                                    </div>

                                    <p class="mt-2 text-xs font-black {{ $row->qualified ? 'text-[#159447]' : 'text-[#e51b2b]' }}">
                                        {{ $row->qualified ? 'Clasifica' : 'Fuera' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-[2rem] bg-white p-6 shadow-2xl">
                        <h2 class="text-2xl font-black text-[#080f2f]">
                            Tablas
                        </h2>

                        <div class="mt-5 space-y-4">
                            @foreach($standings as $groupName => $teams)
                                <details class="overflow-hidden rounded-2xl bg-[#f4f6ff]" {{ $loop->first ? 'open' : '' }}>
                                    <summary class="cursor-pointer px-4 py-3 text-sm font-black text-[#080f2f]">
                                        Grupo {{ $groupName }}
                                    </summary>

                                    <div class="space-y-2 px-4 pb-4">
                                        @foreach($teams as $row)
                                            <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2">
                                                <div>
                                                    <p class="text-xs font-black text-[#080f2f]">
                                                        {{ $row->position }}. {{ $row->team->name }}
                                                    </p>
                                                    <p class="text-[11px] font-bold {{ $row->qualified ? 'text-[#159447]' : 'text-[#080f2f]/35' }}">
                                                        {{ $row->qualified ? ucfirst($row->qualification_type) : 'Eliminado' }}
                                                    </p>
                                                </div>

                                                <div class="text-right">
                                                    <p class="text-xs font-black text-[#080f2f]">
                                                        {{ $row->points }} pts
                                                    </p>
                                                    <p class="text-[11px] font-bold text-[#080f2f]/40">
                                                        DG {{ $row->goal_difference }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <main class="rounded-[2rem] bg-white p-6 shadow-2xl">
                    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-3xl font-black text-[#080f2f]">
                                Llave completa
                            </h2>

                            <p class="mt-2 text-sm font-medium text-[#080f2f]/55">
                                Guardá cada ronda por marcador. Si hay empate, seleccioná quién avanza por desempate.
                            </p>
                        </div>

                        @if($selectedUser->id === Auth::id())
                            <button
                                form="bracket-form"
                                type="submit"
                                class="rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white shadow-xl"
                            >
                                Guardar eliminatorias
                            </button>
                        @endif
                    </div>

                    @if($bracketMatches->isEmpty())
                        <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center">
                            <p class="font-black text-[#080f2f]/50">
                                Todavía no hay cruces generados.
                            </p>
                        </div>
                    @else
                        <form id="bracket-form" method="POST" action="{{ route('bracket.generate') }}">
                            @csrf

                            <div class="overflow-x-auto pb-4">
                                <div class="grid min-w-[1180px] grid-cols-6 gap-4">
                                    @foreach($roundOrder as $roundName)
                                        @php
                                            $roundMatches = $matchesByRound[$roundName] ?? collect();
                                        @endphp

                                        <div>
                                            <div class="sticky top-0 z-10 mb-4 rounded-2xl bg-[#080f2f] px-4 py-3 text-center text-sm font-black text-white">
                                                {{ $roundName }}
                                            </div>

                                            <div class="space-y-4">
                                                @forelse($roundMatches as $match)
                                                    <div class="rounded-3xl border border-[#dbe3ff] bg-[#f4f6ff] p-4">
                                                        <div class="mb-3 flex items-center justify-between">
                                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-[#080f2f]/55">
                                                                M{{ $match->slot }}
                                                            </span>

                                                            @if($match->predictedWinnerTeam)
                                                                <span class="rounded-full bg-[#dcfce7] px-3 py-1 text-xs font-black text-[#166534]">
                                                                    {{ $match->predictedWinnerTeam->name }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="space-y-3">
                                                            <div class="rounded-2xl bg-white p-3">
                                                                <p class="mb-2 text-center text-xs font-black text-[#080f2f]">
                                                                    {{ $match->homeTeam?->name ?? 'Pendiente' }}
                                                                </p>

                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    name="bracket[{{ $match->id }}][home]"
                                                                    value="{{ $match->predicted_home_score !== null ? $match->predicted_home_score : '' }}"
                                                                    {{ $selectedUser->id !== Auth::id() ? 'disabled' : '' }}
                                                                    class="h-12 w-full rounded-2xl border border-[#080f2f]/10 bg-[#f4f6ff] text-center text-xl font-black text-[#080f2f] outline-none"
                                                                >
                                                            </div>

                                                            <div class="text-center text-xs font-black text-[#080f2f]/35">
                                                                VS
                                                            </div>

                                                            <div class="rounded-2xl bg-white p-3">
                                                                <p class="mb-2 text-center text-xs font-black text-[#080f2f]">
                                                                    {{ $match->awayTeam?->name ?? 'Pendiente' }}
                                                                </p>

                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    name="bracket[{{ $match->id }}][away]"
                                                                    value="{{ $match->predicted_away_score !== null ? $match->predicted_away_score : '' }}"
                                                                    {{ $selectedUser->id !== Auth::id() ? 'disabled' : '' }}
                                                                    class="h-12 w-full rounded-2xl border border-[#080f2f]/10 bg-[#f4f6ff] text-center text-xl font-black text-[#080f2f] outline-none"
                                                                >
                                                            </div>

                                                            @if($match->homeTeam && $match->awayTeam)
                                                                <div class="rounded-2xl bg-white p-3">
                                                                    <p class="mb-2 text-xs font-black text-[#080f2f]/45">
                                                                        Desempate / clasifica
                                                                    </p>

                                                                    <label class="mb-2 flex items-center gap-2 text-xs font-black text-[#080f2f]">
                                                                        <input
                                                                            type="radio"
                                                                            name="bracket[{{ $match->id }}][winner]"
                                                                            value="{{ $match->home_team_id }}"
                                                                            @checked((int) $match->predicted_winner_team_id === (int) $match->home_team_id)
                                                                            {{ $selectedUser->id !== Auth::id() ? 'disabled' : '' }}
                                                                        >
                                                                        {{ $match->homeTeam->name }}
                                                                    </label>

                                                                    <label class="flex items-center gap-2 text-xs font-black text-[#080f2f]">
                                                                        <input
                                                                            type="radio"
                                                                            name="bracket[{{ $match->id }}][winner]"
                                                                            value="{{ $match->away_team_id }}"
                                                                            @checked((int) $match->predicted_winner_team_id === (int) $match->away_team_id)
                                                                            {{ $selectedUser->id !== Auth::id() ? 'disabled' : '' }}
                                                                        >
                                                                        {{ $match->awayTeam->name }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="rounded-3xl border border-dashed border-[#080f2f]/15 bg-[#f4f6ff] p-5 text-center">
                                                        <p class="text-xs font-black text-[#080f2f]/35">
                                                            Pendiente
                                                        </p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    @endif
                </main>
            </div>
        @endif
    </div>
</section>
@endsection