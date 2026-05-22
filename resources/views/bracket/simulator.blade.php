@extends('layouts.app', ['title' => 'Simulador - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Simulador
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Grupos y cruces
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Revisá la simulación de grupos, mejores terceros y dieciseisavos.
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
                            Generar simulación
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-[#dcfce7] px-5 py-4 text-sm font-black text-[#166534]">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-10 grid gap-6 lg:grid-cols-[1fr_360px]">
            <div>
                <h2 class="mb-5 text-3xl font-black text-[#080f2f]">
                    Tablas de grupos
                </h2>

                @if($standings->isEmpty())
                    <div class="rounded-[2rem] bg-white p-8 text-center font-black text-[#080f2f]/45 shadow-xl">
                        Todavía no hay simulación generada.
                    </div>
                @else
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($standings as $groupName => $teams)
                            <div class="overflow-hidden rounded-3xl bg-white shadow-xl">
                                <div class="bg-[#1238ff] px-5 py-4 text-white">
                                    <h3 class="text-xl font-black">Grupo {{ $groupName }}</h3>
                                </div>

                                <table class="w-full text-sm">
                                    <thead class="text-[#080f2f]/45">
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
                                                <td class="px-4 py-3 font-black">
                                                    {{ $row->position }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    <p class="font-black">
                                                        {{ $row->team->name }}
                                                    </p>

                                                    <p class="text-xs font-bold {{ $row->qualified ? 'text-[#159447]' : 'text-[#080f2f]/35' }}">
                                                        {{ $row->qualified ? ucfirst($row->qualification_type) : 'Eliminado' }}
                                                    </p>
                                                </td>

                                                <td class="px-4 py-3 text-center font-black">
                                                    {{ $row->points }}
                                                </td>

                                                <td class="px-4 py-3 text-center font-bold">
                                                    {{ $row->goal_difference }}
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

            <div>
                <h2 class="mb-5 text-3xl font-black text-[#080f2f]">
                    Mejores terceros
                </h2>

                <div class="rounded-3xl bg-white p-5 shadow-xl">
                    @forelse($bestThirds as $index => $row)
                        <div class="mb-3 rounded-2xl bg-[#f4f6ff] p-4 last:mb-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-black">
                                        {{ $index + 1 }}. {{ $row->team->name }}
                                    </p>

                                    <p class="text-xs font-bold text-[#080f2f]/45">
                                        Grupo {{ $row->group_name }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="font-black">
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
                    @empty
                        <p class="text-sm font-bold text-[#080f2f]/45">
                            Todavía no hay terceros calculados.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="mb-5 text-3xl font-black text-[#080f2f]">
                Dieciseisavos
            </h2>

            @if($bracketMatches->isEmpty())
                <div class="rounded-[2rem] bg-white p-8 text-center font-black text-[#080f2f]/45 shadow-xl">
                    Todavía no hay cruces generados.
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($bracketMatches as $match)
                        <div class="rounded-3xl bg-white p-5 shadow-xl">
                            <div class="mb-4 flex items-center justify-between">
                                <p class="rounded-full bg-[#edf1ff] px-3 py-1 text-xs font-black text-[#1238ff]">
                                    Partido {{ $match->match_number }}
                                </p>

                                <p class="text-xs font-black text-[#080f2f]/45">
                                    Ronda 32
                                </p>
                            </div>

                            <div class="space-y-3">
                                <div class="rounded-2xl bg-[#f4f6ff] p-4 text-center">
                                    <p class="font-black text-[#080f2f]">
                                        {{ $match->homeTeam->name }}
                                    </p>
                                </div>

                                <div class="text-center text-xs font-black uppercase tracking-[0.25em] text-[#080f2f]/35">
                                    vs
                                </div>

                                <div class="rounded-2xl bg-[#f4f6ff] p-4 text-center">
                                    <p class="font-black text-[#080f2f]">
                                        {{ $match->awayTeam->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection