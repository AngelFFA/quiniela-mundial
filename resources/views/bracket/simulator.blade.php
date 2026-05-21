@extends('layouts.app', ['title' => 'Simulador - Quiniela Mundial'])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-10">
    <div class="rounded-3xl border border-white/10 bg-[#081225] p-6 shadow-2xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.25em] text-white/40">
                    Simulador de quiniela
                </p>

                <h1 class="mt-2 text-3xl font-semibold text-white">
                    Grupos y clasificación
                </h1>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-white/55">
                    Seleccioná un participante para revisar cómo queda su fase de grupos y qué terceros clasifican.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <form method="GET" action="{{ route('bracket.simulator') }}">
                    <select name="user_id"
                            onchange="this.form.submit()"
                            class="h-11 rounded-xl border border-white/15 bg-[#101a2d] px-4 text-sm font-medium text-white outline-none">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected($selectedUser->id === $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($selectedUser->id === Auth::id())
                    <form method="POST" action="{{ route('bracket.generate') }}">
                        @csrf
                        <button type="submit"
                                class="h-11 rounded-xl bg-[#f6d36b] px-5 text-sm font-semibold text-[#07101f] shadow-lg transition hover:bg-[#ffe28a]">
                            Generar simulación
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-5 rounded-xl border border-green-400/20 bg-green-400/10 px-5 py-3 text-sm text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-[#10224a] p-5">
            <p class="text-xs uppercase tracking-widest text-white/40">Participante</p>
            <p class="mt-1 text-lg font-semibold">{{ $selectedUser->name }}</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-[#0e3b2a] p-5">
            <p class="text-xs uppercase tracking-widest text-white/40">Grupos calculados</p>
            <p class="mt-1 text-lg font-semibold">{{ $standings->count() }}</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-[#4a1119] p-5">
            <p class="text-xs uppercase tracking-widest text-white/40">Terceros clasificados</p>
            <p class="mt-1 text-lg font-semibold">{{ $bestThirds->where('qualified', true)->count() }} / 8</p>
        </div>
    </div>

    <div class="mt-10">
        <div class="mb-4">
            <p class="text-xs font-medium uppercase tracking-[0.25em] text-white/35">
                Fase de grupos
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-white">
                Tablas simuladas
            </h2>
        </div>

        @if($standings->isEmpty())
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-sm text-white/50">
                Este participante todavía no tiene simulación generada.
            </div>
        @else
            <div class="grid gap-5 xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2">
                @foreach($standings as $groupName => $teams)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#101827] shadow-xl">
                        <div class="flex items-center justify-between border-b border-white/10 bg-gradient-to-r from-[#173B8F] to-[#D71920] px-4 py-3">
                            <h3 class="text-base font-semibold">Grupo {{ $groupName }}</h3>
                            <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-medium">
                                2026
                            </span>
                        </div>

                        <div class="p-3">
                            <div class="space-y-2">
                                @foreach($teams as $row)
                                    <div class="rounded-xl border border-white/10 bg-white/[0.06] p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#f6d36b] text-sm font-semibold text-[#07101f]">
                                                    {{ $row->position }}
                                                </span>

                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-medium text-white">
                                                        {{ $row->team->name }}
                                                    </p>

                                                    <p class="mt-0.5 text-[11px] text-white/45">
                                                        DG {{ $row->goal_difference }} · GF {{ $row->goals_for }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <p class="text-lg font-semibold text-white">{{ $row->points }}</p>
                                                <p class="text-[10px] uppercase tracking-wider text-white/35">pts</p>
                                            </div>
                                        </div>

                                        @if($row->qualified)
                                            <div class="mt-2 rounded-lg bg-green-400/10 px-3 py-1 text-[11px] font-medium text-green-300">
                                                {{ ucfirst($row->qualification_type) }}
                                            </div>
                                        @else
                                            <div class="mt-2 rounded-lg bg-white/5 px-3 py-1 text-[11px] font-medium text-white/35">
                                                Eliminado
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-10">
        <div class="mb-4">
            <p class="text-xs font-medium uppercase tracking-[0.25em] text-white/35">
                Comparación
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-white">
                Mejores terceros
            </h2>
        </div>

        @if($bestThirds->isEmpty())
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center text-sm text-white/50">
                Todavía no hay terceros calculados.
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#101827] shadow-xl">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10 bg-white/[0.06] text-xs uppercase tracking-wider text-white/45">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Grupo</th>
                            <th class="px-4 py-3 text-left">Equipo</th>
                            <th class="px-4 py-3 text-center">Pts</th>
                            <th class="px-4 py-3 text-center">DG</th>
                            <th class="px-4 py-3 text-center">GF</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @foreach($bestThirds as $index => $row)
                            <tr class="text-white/80">
                                <td class="px-4 py-3 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">Grupo {{ $row->group_name }}</td>
                                <td class="px-4 py-3 font-medium text-white">{{ $row->team->name }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-white">{{ $row->points }}</td>
                                <td class="px-4 py-3 text-center">{{ $row->goal_difference }}</td>
                                <td class="px-4 py-3 text-center">{{ $row->goals_for }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($row->qualified)
                                        <span class="rounded-full bg-green-400/10 px-3 py-1 text-xs font-medium text-green-300">
                                            Clasifica
                                        </span>
                                    @else
                                        <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-medium text-white/35">
                                            Fuera
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection