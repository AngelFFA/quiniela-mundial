@extends('layouts.app', ['title' => 'Detalle de puntos - Quiniela Mundial'])

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-10">
    <div class="mx-auto max-w-7xl">
        <a href="{{ route('ranking') }}" class="inline-flex items-center gap-2 text-sm font-black text-[#080f2f]/60 hover:text-[#1238ff]">
            ← Tabla General
        </a>

        <div class="mt-5 flex items-center gap-4">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" class="h-16 w-16 rounded-full border border-slate-200 object-cover shadow" alt="{{ $user->name }}">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#080f2f] text-2xl font-black text-white shadow">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#1238ff]">Detalle de puntos</p>
                <h1 class="mt-1 truncate text-2xl font-black text-[#080f2f] sm:text-4xl">{{ $user->name }}</h1>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="col-span-2 rounded-3xl bg-[#080f2f] p-5 text-white shadow-lg sm:col-span-1">
                <p class="text-[10px] font-black uppercase tracking-[0.16em] text-white/50">Total</p>
                <p class="mt-2 text-4xl font-black">{{ $totalPoints }}</p>
                <p class="mt-1 text-xs font-bold text-white/60">puntos acumulados</p>
            </div>
            <div class="rounded-3xl bg-[#edf1ff] p-5 text-center">
                <p class="text-3xl font-black text-[#1238ff]">{{ $groupPoints }}</p>
                <p class="mt-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#080f2f]/50">Grupos</p>
            </div>
            <div class="rounded-3xl bg-[#fff7d6] p-5 text-center">
                <p class="text-3xl font-black text-[#9a6b00]">{{ $bracketPoints }}</p>
                <p class="mt-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#080f2f]/50">Llaves</p>
            </div>
            <div class="rounded-3xl bg-[#e9f8ef] p-5 text-center">
                <p class="text-3xl font-black text-[#159447]">{{ $exactResults }}</p>
                <p class="mt-1 text-[10px] font-black uppercase tracking-[0.12em] text-[#080f2f]/50">Exactos</p>
            </div>
        </div>

        <div class="mt-8 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-[#1238ff]">Fase de grupos</p>
                <h2 class="mt-1 text-2xl font-black text-[#080f2f]">Partidos y puntos</h2>
            </div>
            <p class="text-xs font-bold text-[#080f2f]/45">{{ $playedMatches }} jugados</p>
        </div>

        <div class="mt-4 space-y-3 md:hidden">
            @forelse($details as $row)
                @php
                    $pointClass = match((int) $row->points) {
                        5 => 'bg-[#159447] text-white',
                        3 => 'bg-[#1238ff] text-white',
                        2 => 'bg-[#ffc400] text-[#080f2f]',
                        1 => 'bg-[#ff9f1c] text-white',
                        default => 'bg-slate-200 text-slate-600',
                    };
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-[0.12em] text-[#080f2f]/40">
                                {{ $row->match_date ? \Illuminate\Support\Carbon::parse($row->match_date)->format('d/m/Y') : 'Sin fecha' }}
                                @if($row->group_name) · Grupo {{ $row->group_name }} @endif
                            </p>
                            <p class="mt-2 text-sm font-black leading-5 text-[#080f2f]">
                                {{ $row->home_team_name }} vs {{ $row->away_team_name }}
                            </p>
                        </div>
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-base font-black {{ $pointClass }}">
                            +{{ (int) $row->points }}
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <div class="rounded-2xl bg-[#edf1ff] p-3 text-center">
                            <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#080f2f]/45">Pronóstico</p>
                            <p class="mt-1 text-xl font-black text-[#080f2f]">{{ $row->predicted_home_score }} - {{ $row->predicted_away_score }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-100 p-3 text-center">
                            <p class="text-[9px] font-black uppercase tracking-[0.12em] text-[#080f2f]/45">Resultado</p>
                            <p class="mt-1 text-xl font-black text-[#080f2f]">
                                @if($row->is_finished && $row->home_score !== null && $row->away_score !== null)
                                    {{ $row->home_score }} - {{ $row->away_score }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>

                    <p class="mt-3 text-xs font-bold text-[#080f2f]/55">{{ $row->reason ?? 'Pendiente de resultado oficial' }}</p>
                </div>
            @empty
                <div class="rounded-3xl bg-white p-10 text-center font-black text-[#080f2f]/40">No hay pronósticos registrados.</div>
            @endforelse
        </div>

        <div class="mt-4 hidden overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-black/5 md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#080f2f] text-white">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase">Partido</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Pronóstico</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Resultado</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Puntos</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $row)
                            @php
                                $pointClass = match((int) $row->points) {
                                    5 => 'bg-[#159447] text-white',
                                    3 => 'bg-[#1238ff] text-white',
                                    2 => 'bg-[#ffc400] text-[#080f2f]',
                                    1 => 'bg-[#ff9f1c] text-white',
                                    default => 'bg-slate-200 text-slate-600',
                                };
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-[#f8f9ff]">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-[#080f2f]">{{ $row->home_team_name }} vs {{ $row->away_team_name }}</p>
                                    <p class="mt-1 text-xs font-bold text-[#080f2f]/40">{{ $row->match_date ? \Illuminate\Support\Carbon::parse($row->match_date)->format('d/m/Y') : 'Sin fecha' }} @if($row->group_name) · Grupo {{ $row->group_name }} @endif</p>
                                </td>
                                <td class="px-5 py-4 text-center font-black text-[#080f2f]">{{ $row->predicted_home_score }} - {{ $row->predicted_away_score }}</td>
                                <td class="px-5 py-4 text-center font-black text-[#080f2f]">@if($row->is_finished){{ $row->home_score }} - {{ $row->away_score }}@else—@endif</td>
                                <td class="px-5 py-4 text-center"><span class="inline-flex h-11 min-w-11 items-center justify-center rounded-full px-3 font-black {{ $pointClass }}">+{{ (int) $row->points }}</span></td>
                                <td class="px-5 py-4 text-sm font-bold text-[#080f2f]/55">{{ $row->reason ?? 'Pendiente' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-16 text-center font-black text-[#080f2f]/40">No hay pronósticos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-[#9a6b00]">Dieciseisavos</p>
            <h2 class="mt-1 text-2xl font-black text-[#080f2f]">Puntos por llaves acertadas</h2>
            <p class="mt-2 text-sm font-semibold text-[#080f2f]/55">Cada enfrentamiento acertado suma 2 puntos.</p>
        </div>

        @if(!$bracketAvailable)
            <div class="mt-4 rounded-3xl border border-[#ffc400]/30 bg-[#fff7d6] p-5 text-sm font-bold text-[#7a5700]">
                Los puntos de llaves se calcularán cuando termine toda la fase de grupos.
            </div>
        @else
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($bracketDetails as $bracket)
                    <div class="rounded-3xl border {{ $bracket['correct'] ? 'border-[#159447]/30 bg-[#e9f8ef]' : 'border-slate-200 bg-white' }} p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[10px] font-black uppercase tracking-[0.14em] text-[#080f2f]/45">Llave {{ $bracket['slot'] }}</p>
                            <span class="rounded-full px-3 py-1 text-xs font-black {{ $bracket['correct'] ? 'bg-[#159447] text-white' : 'bg-slate-200 text-slate-600' }}">
                                +{{ $bracket['points'] }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-black text-[#080f2f]">
                            {{ $bracket['predicted_home']?->name ?? 'Sin equipo' }} vs {{ $bracket['predicted_away']?->name ?? 'Sin equipo' }}
                        </p>
                        @if(!$bracket['correct'])
                            <p class="mt-2 text-xs font-bold text-[#080f2f]/45">
                                Real: {{ $bracket['official_home']?->name ?? 'Pendiente' }} vs {{ $bracket['official_away']?->name ?? 'Pendiente' }}
                            </p>
                        @else
                            <p class="mt-2 text-xs font-black text-[#159447]">Enfrentamiento acertado</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
