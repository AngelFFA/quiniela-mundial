@extends('layouts.app', ['title' => 'Simulador - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute -right-20 top-10 h-[430px] w-[430px] rounded-full bg-[#e51b2b]/60"></div>
    <div class="absolute right-10 top-72 h-[390px] w-[390px] rounded-full bg-[#ffc400]/70"></div>
    <div class="absolute right-48 top-[430px] h-[320px] w-[320px] rounded-full bg-[#159447]/45"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_0.8fr]">
            <div>
                <h1 class="text-5xl font-black text-[#080f2f]">Simulador</h1>
                <h2 class="mt-3 text-2xl font-black text-[#080f2f]">Grupos y mejores terceros</h2>

                <p class="mt-4 max-w-xl text-base font-medium leading-7 text-[#080f2f]/65">
                    Seleccioná un participante para ver su simulación.
                </p>
            </div>

            <div class="rounded-[2rem] bg-white p-6 shadow-xl">
                <div class="grid gap-4 md:grid-cols-2">
                    <form method="GET" action="{{ route('bracket.simulator') }}">
                        <label class="text-sm font-black text-[#080f2f]/60">Participante</label>
                        <select name="user_id" onchange="this.form.submit()" class="mt-2 h-12 w-full rounded-2xl border border-[#080f2f]/10 bg-white px-4 text-sm font-bold text-[#080f2f] outline-none">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected($selectedUser->id === $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    @if($selectedUser->id === Auth::id())
                        <form method="POST" action="{{ route('bracket.generate') }}" class="flex items-end">
                            @csrf
                            <button class="h-12 w-full rounded-2xl bg-[#1238ff] px-5 text-sm font-black text-white">
                                Generar nueva simulación
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-[#dcfce7] px-5 py-4 text-sm font-black text-[#166534]">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-10 grid gap-6 lg:grid-cols-[1fr_300px]">
            <div>
                <h3 class="mb-5 text-2xl font-black text-[#080f2f]">Tablas de grupos</h3>

                @if($standings->isEmpty())
                    <div class="rounded-3xl bg-white p-8 text-center font-bold text-[#080f2f]/50 shadow-xl">
                        Este participante todavía no tiene simulación generada.
                    </div>
                @else
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($standings as $groupName => $teams)
                            <div class="overflow-hidden rounded-3xl bg-white shadow-xl">
                                <div class="px-5 py-4 {{ $loop->iteration % 4 == 1 ? 'bg-[#1238ff]' : ($loop->iteration % 4 == 2 ? 'bg-[#e51b2b]' : ($loop->iteration % 4 == 3 ? 'bg-[#159447]' : 'bg-[#7c3aed]')) }} text-white">
                                    <h4 class="text-xl font-black">Grupo {{ $groupName }}</h4>
                                </div>

                                <table class="w-full text-sm">
                                    <thead class="text-[#080f2f]/45">
                                        <tr>
                                            <th class="px-4 py-3 text-left">#</th>
                                            <th class="px-4 py-3 text-left">Equipo</th>
                                            <th class="px-4 py-3 text-center">PTS</th>
                                            <th class="px-4 py-3 text-center">DG</th>
                                            <th class="px-4 py-3 text-center">GF</th>
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
                                                <td class="px-4 py-3 text-center font-bold">{{ $row->goals_for }}</td>
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
                <h3 class="mb-5 text-2xl font-black text-[#080f2f]">Mejores terceros</h3>

                <div class="rounded-3xl bg-white p-5 shadow-xl">
                    @if($bestThirds->isEmpty())
                        <p class="text-sm font-bold text-[#080f2f]/50">Todavía no hay terceros calculados.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($bestThirds as $index => $row)
                                <div class="rounded-2xl bg-[#f4f6ff] p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-black">{{ $index + 1 }}. {{ $row->team->name }}</p>
                                            <p class="text-xs font-bold text-[#080f2f]/45">Grupo {{ $row->group_name }}</p>
                                        </div>

                                        <div class="text-right">
                                            <p class="font-black">{{ $row->points }}</p>
                                            <p class="text-xs font-bold text-[#080f2f]/45">pts</p>
                                        </div>
                                    </div>

                                    <p class="mt-2 text-xs font-black {{ $row->qualified ? 'text-[#159447]' : 'text-[#e51b2b]' }}">
                                        {{ $row->qualified ? 'Clasifica' : 'Fuera' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection