@extends('layouts.app', ['title' => 'Tabla General - Quiniela Mundial'])

@section('content')
@php
    $currentUserId = auth()->id();
@endphp

<section class="px-4 py-6 sm:px-6 sm:py-10">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.22em] text-[#1238ff]">Competencia</p>
                <h1 class="mt-2 text-3xl font-black text-[#080f2f] sm:text-5xl">Tabla General</h1>
                <p class="mt-2 max-w-3xl text-sm font-semibold leading-6 text-[#080f2f]/55 sm:text-base">
                    Puntos de partidos de grupos más 2 puntos por cada llave de dieciseisavos acertada.
                </p>
            </div>

            <div class="flex items-center gap-3 rounded-2xl bg-[#080f2f] px-5 py-4 text-white shadow-lg">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-white/50">Participantes</p>
                    <p class="mt-1 text-3xl font-black">{{ $ranking->count() }}</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-5 rounded-2xl border border-[#159447]/20 bg-[#159447]/10 px-4 py-3 text-sm font-bold text-[#0c6f32]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-5 rounded-2xl border border-[#e51b2b]/20 bg-[#e51b2b]/10 px-4 py-3 text-sm font-bold text-[#b61522]">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-6 space-y-3 md:hidden">
            @forelse($ranking as $user)
                @php
                    $puedeVerDetalle = auth()->user()->quiniela_finalizada && $user->quiniela_finalizada;
                    $isCurrentUser = (int) $user->id === (int) $currentUserId;
                    $positionClass = match($loop->iteration) {
                        1 => 'bg-[#ffc400] text-[#080f2f]',
                        2 => 'bg-slate-300 text-[#080f2f]',
                        3 => 'bg-[#b7793f] text-white',
                        default => 'bg-[#edf1ff] text-[#1238ff]',
                    };
                @endphp

                <div class="overflow-hidden rounded-3xl border {{ $isCurrentUser ? 'border-[#159447] ring-2 ring-[#159447]/20' : 'border-slate-200' }} bg-white shadow-sm">
                    @if($puedeVerDetalle)
                        <a href="{{ route('ranking.detail', $user->id) }}" class="block p-4">
                    @else
                        <div class="p-4">
                    @endif
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-base font-black {{ $positionClass }}">
                                {{ $loop->iteration }}
                            </div>

                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" class="h-12 w-12 shrink-0 rounded-full border border-slate-200 object-cover" alt="{{ $user->name }}">
                            @else
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#080f2f] font-black text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-base font-black text-[#080f2f]">{{ $user->name }}</p>
                                    @if($isCurrentUser)
                                        <span class="rounded-full bg-[#159447]/10 px-2 py-1 text-[9px] font-black uppercase text-[#159447]">Tú</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs font-bold text-[#080f2f]/45">
                                    {{ $user->exact_results }} exactos · {{ $user->predictions_count }} pronósticos
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-3xl font-black text-[#159447]">{{ $user->points }}</p>
                                <p class="text-[10px] font-black uppercase tracking-[0.12em] text-[#080f2f]/40">pts</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="rounded-2xl bg-[#edf1ff] px-3 py-3 text-center">
                                <p class="text-lg font-black text-[#1238ff]">{{ $user->group_points }}</p>
                                <p class="mt-1 text-[9px] font-black uppercase tracking-[0.12em] text-[#080f2f]/50">Fase de grupos</p>
                            </div>
                            <div class="rounded-2xl bg-[#fff7d6] px-3 py-3 text-center">
                                <p class="text-lg font-black text-[#9a6b00]">{{ $user->bracket_points }}</p>
                                <p class="mt-1 text-[9px] font-black uppercase tracking-[0.12em] text-[#080f2f]/50">
                                    Llaves {{ $user->bracket_available ? '(' . $user->bracket_hits . ')' : '' }}
                                </p>
                            </div>
                        </div>
                    @if($puedeVerDetalle)
                        </a>
                    @else
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl bg-white p-10 text-center font-black text-[#080f2f]/40 shadow-sm">
                    No hay participantes todavía.
                </div>
            @endforelse
        </div>

        <div class="mt-8 hidden overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-black/5 md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#080f2f] text-white">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase">#</th>
                            <th class="px-5 py-4 text-left text-xs font-black uppercase">Participante</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Grupos</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Llaves</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Exactos</th>
                            <th class="px-5 py-4 text-center text-xs font-black uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ranking as $user)
                            @php
                                $puedeVerDetalle = auth()->user()->quiniela_finalizada && $user->quiniela_finalizada;
                                $isCurrentUser = (int) $user->id === (int) $currentUserId;
                            @endphp
                            <tr class="border-b border-slate-100 {{ $isCurrentUser ? 'bg-[#159447]/5' : 'hover:bg-[#f8f9ff]' }}">
                                <td class="px-5 py-4 text-lg font-black text-[#1238ff]">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4">
                                    @if($puedeVerDetalle)<a href="{{ route('ranking.detail', $user->id) }}" class="flex items-center gap-3">@else<div class="flex items-center gap-3">@endif
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar }}" class="h-11 w-11 rounded-full border border-slate-200 object-cover" alt="{{ $user->name }}">
                                        @else
                                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#080f2f] font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                        @endif
                                        <div>
                                            <p class="font-black text-[#080f2f]">{{ $user->name }}</p>
                                            <p class="mt-1 text-xs font-bold text-[#080f2f]/40">{{ $user->predictions_count }} pronósticos</p>
                                        </div>
                                    @if($puedeVerDetalle)</a>@else</div>@endif
                                </td>
                                <td class="px-5 py-4 text-center text-xl font-black text-[#1238ff]">{{ $user->group_points }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-xl bg-[#fff7d6] px-4 py-2 text-lg font-black text-[#9a6b00]">{{ $user->bracket_points }}</span>
                                </td>
                                <td class="px-5 py-4 text-center text-xl font-black text-[#159447]">{{ $user->exact_results }}</td>
                                <td class="px-5 py-4 text-center text-3xl font-black text-[#159447]">{{ $user->points }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center font-black text-[#080f2f]/40">No hay participantes todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
