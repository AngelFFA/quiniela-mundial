@extends('layouts.app', ['title' => 'Simulador - Quiniela Mundial'])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-12">
    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.3em] text-white/45">Simulador</p>
            <h1 class="mt-3 text-4xl font-black">Grupos y llaves predichas</h1>
            <p class="mt-3 max-w-2xl text-white/60">
                Aquí se calculan las tablas de grupos, mejores terceros y dieciseisavos según tus pronósticos.
            </p>
        </div>

        <form method="POST" action="{{ route('bracket.generate') }}">
            @csrf
            <button type="submit" class="rounded-2xl bg-white px-6 py-3 font-black text-[#050b18] shadow-xl transition hover:scale-105">
                Generar simulación
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mt-6 rounded-2xl border border-green-400/20 bg-green-400/10 px-5 py-4 text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-10">
        <h2 class="text-2xl font-black">Tablas simuladas</h2>

        @if($standings->isEmpty())
            <div class="mt-5 rounded-3xl border border-dashed border-white/20 bg-white/5 p-8 text-center text-white/50">
                Todavía no hay tablas simuladas. Primero deben existir partidos de grupos y pronósticos guardados.
            </div>
        @else
            <div class="mt-5 grid gap-6 lg:grid-cols-2">
                @foreach($standings as $groupName => $teams)
                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/10 backdrop-blur">
                        <div class="bg-white/10 px-5 py-4">
                            <h3 class="text-xl font-black">Grupo {{ $groupName }}</h3>
                        </div>

                        <table class="w-full text-sm">
                            <thead class="text-white/50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Pos</th>
                                    <th class="px-4 py-3 text-left">Equipo</th>
                                    <th class="px-4 py-3 text-center">Pts</th>
                                    <th class="px-4 py-3 text-center">DG</th>
                                    <th class="px-4 py-3 text-center">GF</th>
                                    <th class="px-4 py-3 text-center">Clasifica</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($teams as $row)
                                    <tr>
                                        <td class="px-4 py-3">{{ $row->position }}</td>
                                        <td class="px-4 py-3 font-bold">{{ $row->team->name }}</td>
                                        <td class="px-4 py-3 text-center font-black">{{ $row->points }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row->goal_difference }}</td>
                                        <td class="px-4 py-3 text-center">{{ $row->goals_for }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($row->qualified)
                                                <span class="rounded-full bg-green-400/15 px-3 py-1 text-xs font-bold text-green-300">
                                                    {{ $row->qualification_type }}
                                                </span>
                                            @else
                                                <span class="text-white/30">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-12">
        <h2 class="text-2xl font-black">Dieciseisavos generados</h2>

        @if($bracketMatches->isEmpty())
            <div class="mt-5 rounded-3xl border border-dashed border-white/20 bg-white/5 p-8 text-center text-white/50">
                Todavía no hay llaves generadas.
            </div>
        @else
            <div class="mt-5 grid gap-5 lg:grid-cols-2">
                @foreach($bracketMatches as $match)
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <p class="mb-3 text-xs font-bold uppercase tracking-[0.25em] text-white/40">
                            {{ $match->round }} · Partido {{ $match->slot }}
                        </p>

                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                            <div class="rounded-2xl bg-white/10 p-4 font-black">
                                {{ $match->homeTeam?->name ?? 'Pendiente' }}
                            </div>

                            <div class="text-white/40">vs</div>

                            <div class="rounded-2xl bg-white/10 p-4 text-right font-black">
                                {{ $match->awayTeam?->name ?? 'Pendiente' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection