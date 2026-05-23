@extends('layouts.app', ['title' => 'Mi Quiniela - Quiniela Mundial'])

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

    $finalMatch = ($bracketMatches['Final'] ?? collect())->first();
    $champion = $finalMatch?->predictedWinnerTeam;
@endphp

<section class="relative px-6 py-10">
    <div class="absolute -left-32 top-20 h-[420px] w-[420px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -left-20 top-72 h-[340px] w-[340px] rounded-full bg-[#159447]"></div>
    <div class="absolute -right-16 top-16 h-[430px] w-[430px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-100px] top-[360px] h-[420px] w-[420px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Mi quiniela completa
                </div>

                <h1 class="mt-5 text-5xl font-black leading-tight text-[#080f2f] md:text-6xl">
                    Marcadores del Mundial 2026
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-7 text-[#080f2f]/65">
                    Llená tus marcadores por pestañas. Primero grupos, luego tablas, mejores terceros y eliminatorias.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Participante
                </p>

                <h2 class="mt-3 text-3xl font-black">
                    {{ Auth::user()->name }}
                </h2>

                @if($champion)
                    <div class="mt-5 rounded-2xl bg-white/10 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-white/45">
                            Campeón pronosticado
                        </p>
                        <p class="mt-2 text-2xl font-black">
                            {{ $champion->name }}
                        </p>
                    </div>
                @else
                    <p class="mt-3 text-sm leading-6 text-white/65">
                        La quiniela se irá completando según tus marcadores.
                    </p>
                @endif
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

        <div class="mt-8 rounded-[2rem] bg-white p-3 shadow-2xl">
            <div class="grid gap-2 md:grid-cols-4 xl:grid-cols-8">
                <button type="button" onclick="mostrarTab('grupos')" id="btn-grupos" class="tab-btn rounded-2xl bg-[#1238ff] px-4 py-3 text-sm font-black text-white">
                    Grupos
                </button>

                <button type="button" onclick="mostrarTab('tablas')" id="btn-tablas" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Tablas
                </button>

                <button type="button" onclick="mostrarTab('terceros')" id="btn-terceros" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Terceros
                </button>

                <button type="button" onclick="mostrarTab('dieciseisavos')" id="btn-dieciseisavos" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    16avos
                </button>

                <button type="button" onclick="mostrarTab('octavos')" id="btn-octavos" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Octavos
                </button>

                <button type="button" onclick="mostrarTab('cuartos')" id="btn-cuartos" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Cuartos
                </button>

                <button type="button" onclick="mostrarTab('semis')" id="btn-semis" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Semis
                </button>

                <button type="button" onclick="mostrarTab('final')" id="btn-final" class="tab-btn rounded-2xl bg-[#f4f6ff] px-4 py-3 text-sm font-black text-[#080f2f]">
                    Final
                </button>
            </div>
        </div>

        <div id="tab-grupos" class="tab-content mt-8">
            <form method="POST" action="{{ route('predictions.store') }}">
                @csrf

                <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                    <div class="mb-7 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-3xl font-black text-[#080f2f]">
                                Fase de grupos
                            </h2>

                            <p class="mt-2 text-sm font-medium text-[#080f2f]/60">
                                Completá los marcadores y guardá para generar tablas y cruces.
                            </p>
                        </div>

                        <button type="submit" class="rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white shadow-xl">
                            Guardar grupos
                        </button>
                    </div>

                    @if($matches->isEmpty())
                        <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                            Todavía no hay partidos cargados.
                        </div>
                    @else
                        <div class="grid gap-5 lg:grid-cols-2">
                            @foreach($matches as $groupName => $games)
                                @php
                                    $groupColors = ['#1238ff', '#e51b2b', '#159447', '#7c3aed', '#ffc400', '#ff7a1a'];
                                    $groupColor = $groupColors[($loop->iteration - 1) % count($groupColors)];
                                    $textColor = in_array($groupColor, ['#ffc400', '#ff7a1a']) ? '#080f2f' : '#ffffff';
                                @endphp

                                <div class="overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-black/5">
                                    <div class="px-6 py-4" style="background-color: {{ $groupColor }}; color: {{ $textColor }};">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-xl font-black">Grupo {{ $groupName }}</h3>
                                            <span class="text-sm font-black opacity-80">{{ $games->count() }} partidos</span>
                                        </div>
                                    </div>

                                    <div class="space-y-4 bg-white p-5">
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
                                                            class="h-14 w-16 rounded-2xl border border-[#080f2f]/10 bg-white text-center text-xl font-black text-[#080f2f] outline-none shadow-sm"
                                                        >

                                                        <span class="font-black text-[#080f2f]/35">-</span>

                                                        <input
                                                            type="number"
                                                            min="0"
                                                            name="predictions[{{ $match->id }}][away]"
                                                            value="{{ $prediction?->predicted_away_score !== null ? $prediction->predicted_away_score : '' }}"
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
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <div id="tab-tablas" class="tab-content mt-8 hidden">
            <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                <h2 class="mb-6 text-3xl font-black text-[#080f2f]">
                    Tablas de grupos
                </h2>

                @if($standings->isEmpty())
                    <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                        Guardá primero los marcadores de grupos.
                    </div>
                @else
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($standings as $groupName => $teams)
                            <div class="overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-black/5">
                                <div class="bg-[#080f2f] px-5 py-4 text-white">
                                    <h3 class="text-xl font-black">Grupo {{ $groupName }}</h3>
                                </div>

                                <table class="w-full text-sm">
                                    <thead class="bg-[#f4f6ff] text-[#080f2f]/45">
                                        <tr>
                                            <th class="px-4 py-3 text-left">#</th>
                                            <th class="px-4 py-3 text-left">Equipo</th>
                                            <th class="px-4 py-3 text-center">PTS</th>
                                            <th class="px-4 py-3 text-center">DG</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($teams as $row)
                                            <tr>
                                                <td class="px-4 py-3 font-black">{{ $row->position }}</td>
                                                <td class="px-4 py-3">
                                                    <p class="font-black">{{ $row->team->name }}</p>
                                                    <p class="text-xs font-bold {{ $row->qualified ? 'text-[#159447]' : 'text-[#080f2f]/35' }}">
                                                        {{ $row->qualified ? ucfirst($row->qualification_type) : 'Eliminado' }}
                                                    </p>
                                                </td>
                                                <td class="px-4 py-3 text-center font-black">{{ $row->points }}</td>
                                                <td class="px-4 py-3 text-center font-bold">{{ $row->goal_difference }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div id="tab-terceros" class="tab-content mt-8 hidden">
            <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                <h2 class="mb-6 text-3xl font-black text-[#080f2f]">
                    Mejores terceros
                </h2>

                @if($bestThirds->isEmpty())
                    <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                        Guardá primero los marcadores de grupos.
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach($bestThirds as $index => $row)
                            <div class="rounded-3xl bg-[#f4f6ff] p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-black text-[#080f2f]">
                                            {{ $index + 1 }}. {{ $row->team->name }}
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-[#080f2f]/45">
                                            Grupo {{ $row->group_name }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-2xl font-black text-[#080f2f]">
                                            {{ $row->points }}
                                        </p>
                                        <p class="text-xs font-bold text-[#080f2f]/45">
                                            pts
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 text-sm font-black {{ $row->qualified ? 'text-[#159447]' : 'text-[#e51b2b]' }}">
                                    {{ $row->qualified ? 'Clasifica' : 'Fuera' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @foreach([
            'dieciseisavos' => 'Dieciseisavos',
            'octavos' => 'Octavos',
            'cuartos' => 'Cuartos',
            'semis' => 'Semifinales',
            'final' => 'Final',
        ] as $tabId => $roundName)
            <div id="tab-{{ $tabId }}" class="tab-content mt-8 hidden">
                <form method="POST" action="{{ route('predictions.store') }}">
                    @csrf

                    <div class="rounded-[2rem] bg-white p-7 shadow-2xl">
                        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-3xl font-black text-[#080f2f]">
                                    {{ $roundName }}
                                </h2>

                                <p class="mt-2 text-sm font-medium text-[#080f2f]/55">
                                    Guardá los marcadores de esta ronda. Si hay empate, seleccioná quién clasifica.
                                </p>
                            </div>

                            <button type="submit" class="rounded-2xl bg-[#1238ff] px-7 py-4 text-sm font-black text-white shadow-xl">
                                Guardar ronda
                            </button>
                        </div>

                        @php
                            $roundMatches = $bracketMatches[$roundName] ?? collect();
                        @endphp

                        @if($roundMatches->isEmpty())
                            <div class="rounded-2xl bg-[#f4f6ff] p-8 text-center font-bold text-[#080f2f]/50">
                                Esta ronda todavía está pendiente.
                            </div>
                        @else
                            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                                @foreach($roundMatches as $match)
                                    <div class="rounded-3xl bg-[#f4f6ff] p-5 shadow-lg">
                                        <div class="mb-4 flex items-center justify-between">
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-[#080f2f]/55">
                                                M{{ $match->slot }}
                                            </span>

                                            @if($match->predictedWinnerTeam)
                                                <span class="rounded-full bg-[#dcfce7] px-3 py-1 text-xs font-black text-[#166534]">
                                                    {{ $match->predictedWinnerTeam->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-[1fr_auto_1fr] items-start gap-3">
                                            <div class="rounded-2xl bg-white p-3 text-center">
                                                <p class="mb-2 min-h-[38px] text-xs font-black text-[#080f2f]">
                                                    {{ $match->homeTeam?->name ?? 'Pendiente' }}
                                                </p>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="bracket[{{ $match->id }}][home]"
                                                    value="{{ $match->predicted_home_score !== null ? $match->predicted_home_score : '' }}"
                                                    class="h-12 w-full rounded-2xl border border-[#080f2f]/10 bg-[#f4f6ff] text-center text-xl font-black text-[#080f2f] outline-none"
                                                >
                                            </div>

                                            <div class="pt-12 text-center text-xs font-black text-[#080f2f]/35">
                                                -
                                            </div>

                                            <div class="rounded-2xl bg-white p-3 text-center">
                                                <p class="mb-2 min-h-[38px] text-xs font-black text-[#080f2f]">
                                                    {{ $match->awayTeam?->name ?? 'Pendiente' }}
                                                </p>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="bracket[{{ $match->id }}][away]"
                                                    value="{{ $match->predicted_away_score !== null ? $match->predicted_away_score : '' }}"
                                                    class="h-12 w-full rounded-2xl border border-[#080f2f]/10 bg-[#f4f6ff] text-center text-xl font-black text-[#080f2f] outline-none"
                                                >
                                            </div>
                                        </div>

                                        @if($match->homeTeam && $match->awayTeam)
                                            <div class="mt-4 rounded-2xl bg-white p-4">
                                                <p class="mb-3 text-xs font-black text-[#080f2f]/45">
                                                    Clasifica si hay empate
                                                </p>

                                                <label class="mb-2 flex items-center gap-2 text-xs font-black text-[#080f2f]">
                                                    <input
                                                        type="radio"
                                                        name="bracket[{{ $match->id }}][winner]"
                                                        value="{{ $match->home_team_id }}"
                                                        @checked((int) $match->predicted_winner_team_id === (int) $match->home_team_id)
                                                    >
                                                    {{ $match->homeTeam->name }}
                                                </label>

                                                <label class="flex items-center gap-2 text-xs font-black text-[#080f2f]">
                                                    <input
                                                        type="radio"
                                                        name="bracket[{{ $match->id }}][winner]"
                                                        value="{{ $match->away_team_id }}"
                                                        @checked((int) $match->predicted_winner_team_id === (int) $match->away_team_id)
                                                    >
                                                    {{ $match->awayTeam->name }}
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</section>

<script>
    function mostrarTab(tab) {
        document.querySelectorAll('.tab-content').forEach(function (content) {
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-btn').forEach(function (button) {
            button.classList.remove('bg-[#1238ff]', 'text-white');
            button.classList.add('bg-[#f4f6ff]', 'text-[#080f2f]');
        });

        document.getElementById('tab-' + tab).classList.remove('hidden');

        const activeButton = document.getElementById('btn-' + tab);

        activeButton.classList.remove('bg-[#f4f6ff]', 'text-[#080f2f]');
        activeButton.classList.add('bg-[#1238ff]', 'text-white');
    }
</script>
@endsection