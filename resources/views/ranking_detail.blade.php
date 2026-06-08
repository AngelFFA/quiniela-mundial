@extends('layouts.app', ['title' => 'Detalle de puntos - Quiniela Mundial'])

@section('content')
<section class="px-6 py-12">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-2xl bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Detalle de puntos
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] md:text-6xl">
                    {{ $user->name }}
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-8 text-[#080f2f]/65 md:text-lg">
                    Detalle de cada partido, comparando el pronóstico ingresado contra el resultado oficial y los puntos obtenidos.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">Resumen</p>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-[#1238ff] p-4 text-center">
                        <p class="text-3xl font-black">{{ $totalPoints }}</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.16em] text-white/70">Puntos</p>
                    </div>

                    <div class="rounded-2xl bg-[#159447] p-4 text-center">
                        <p class="text-3xl font-black">{{ $exactResults }}</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.16em] text-white/70">Exactos</p>
                    </div>

                    <div class="rounded-2xl bg-[#ffc400] p-4 text-center text-[#080f2f]">
                        <p class="text-3xl font-black">{{ $playedMatches }}</p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-[0.16em]">Jugados</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('ranking') }}" class="inline-flex rounded-2xl border border-[#080f2f]/10 bg-white px-5 py-3 text-sm font-black text-[#080f2f] shadow-sm">
                ← Volver a Tabla General
            </a>
        </div>

        <div class="mt-8 overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#080f2f] text-white">
                        <tr>
                            <th class="px-5 py-5 text-left text-sm font-black uppercase">Fecha</th>
                            <th class="px-5 py-5 text-left text-sm font-black uppercase">Partido</th>
                            <th class="px-5 py-5 text-center text-sm font-black uppercase">Pronóstico</th>
                            <th class="px-5 py-5 text-center text-sm font-black uppercase">Resultado</th>
                            <th class="px-5 py-5 text-center text-sm font-black uppercase">Puntos</th>
                            <th class="px-5 py-5 text-left text-sm font-black uppercase">Detalle</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($details as $row)
                            <tr class="border-b border-slate-100 hover:bg-[#f8f9ff]">
                                <td class="px-5 py-4 text-sm font-bold text-[#080f2f]/60">
                                    {{ $row->match_date ? \Illuminate\Support\Carbon::parse($row->match_date)->format('d/m/Y') : 'Sin fecha' }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="text-sm font-black text-[#080f2f]">
                                        {{ $row->home_team_name }} vs {{ $row->away_team_name }}
                                    </p>
                                    <p class="mt-1 text-xs font-bold text-[#080f2f]/45">
                                        {{ $row->stage }} @if($row->group_name) · Grupo {{ $row->group_name }} @endif
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="rounded-xl bg-[#edf1ff] px-4 py-2 text-sm font-black text-[#080f2f]">
                                        {{ $row->predicted_home_score }} - {{ $row->predicted_away_score }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($row->is_finished && $row->home_score !== null && $row->away_score !== null)
                                        <span class="rounded-xl bg-[#159447]/10 px-4 py-2 text-sm font-black text-[#159447]">
                                            {{ $row->home_score }} - {{ $row->away_score }}
                                        </span>
                                    @else
                                        <span class="rounded-xl bg-[#ffc400]/20 px-4 py-2 text-sm font-black text-[#080f2f]/60">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="text-2xl font-black text-[#1238ff]">
                                        {{ $row->points }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-sm font-bold text-[#080f2f]/60">
                                    {{ $row->reason ?? 'Pendiente de resultado oficial' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-lg font-black text-[#080f2f]/45">
                                    Este participante todavía no tiene pronósticos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
