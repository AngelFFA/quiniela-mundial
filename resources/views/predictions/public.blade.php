@extends('layouts.app', ['title' => 'Quinielas - Mundial 2026'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute -left-32 top-20 h-[420px] w-[420px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -right-16 top-16 h-[430px] w-[430px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-100px] top-[360px] h-[420px] w-[420px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Transparencia
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Quinielas de todos
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Revisá los pronósticos guardados por cada participante.
                </p>
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

                <p class="mt-4 text-sm leading-6 text-white/65">
                    Mostrando quiniela de:
                    <strong>{{ $selectedUser->name }}</strong>
                </p>
            </div>
        </div>

        <div class="mt-10 rounded-[2rem] bg-white p-7 shadow-2xl">
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
                                style="background-color: {{ $groupColor }}; color: {{ $textColor }};"
                            >
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

                                            <div class="rounded-2xl bg-white px-5 py-4 text-center shadow">
                                                @if($prediction)
                                                    <p class="text-3xl font-black text-[#080f2f]">
                                                        {{ $prediction->predicted_home_score }}
                                                        -
                                                        {{ $prediction->predicted_away_score }}
                                                    </p>
                                                @else
                                                    <p class="text-sm font-black text-[#080f2f]/35">
                                                        Sin pronóstico
                                                    </p>
                                                @endif
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
    </div>
</section>
@endsection