@extends('layouts.app', ['title' => 'Tabla General - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-2xl bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Competencia
                </div>

                <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] md:text-6xl">
                    Tabla General
                </h1>

                <p class="mt-4 max-w-3xl text-base font-medium leading-8 text-[#080f2f]/65 md:text-lg">
                    Posiciones generales de todos los participantes de la quiniela. Podés presionar el nombre de un participante para ver el detalle de sus puntos.
                </p>
            </div>

            <div class="rounded-[2rem] bg-[#080f2f] p-7 text-white shadow-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.25em] text-white/45">
                    Participantes
                </p>

                <h2 class="mt-3 text-5xl font-black">
                    {{ $ranking->count() }}
                </h2>

                <p class="mt-3 text-sm leading-6 text-white/65">
                    Usuarios registrados en la quiniela.
                </p>
            </div>
        </div>

        <div class="mt-10 overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#080f2f] text-white">
                        <tr>
                            <th class="px-6 py-5 text-left text-sm font-black uppercase">#</th>
                            <th class="px-6 py-5 text-left text-sm font-black uppercase">Participante</th>
                            <th class="px-6 py-5 text-center text-sm font-black uppercase">Estado</th>
                            <th class="px-6 py-5 text-center text-sm font-black uppercase">Puntos</th>
                            <th class="px-6 py-5 text-center text-sm font-black uppercase">Exactos</th>
                            <th class="px-6 py-5 text-center text-sm font-black uppercase">Pronósticos</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ranking as $user)
                            <tr class="border-b border-slate-100 hover:bg-[#f8f9ff]">
                                <td class="px-6 py-5">
                                    <span class="inline-flex h-11 min-w-11 items-center justify-center rounded-xl bg-[#edf1ff] px-3 text-lg font-black text-[#1238ff]">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    <a href="{{ route('ranking.detail', $user->id) }}" class="flex items-center gap-4">
                                        @if($user->avatar)
                                            <img
                                                src="{{ $user->avatar }}"
                                                class="h-14 w-14 rounded-xl border border-[#dfe5f3] object-cover shadow"
                                                alt="{{ $user->name }}"
                                            >
                                        @else
                                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-[#080f2f] text-lg font-black text-white shadow">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="text-base font-black text-[#080f2f] hover:text-[#1238ff]">
                                                {{ $user->name }}
                                            </p>
                                            <p class="mt-1 text-xs font-bold text-[#080f2f]/45">
                                                Ver detalle de puntos
                                            </p>
                                        </div>
                                    </a>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    @if($user->quiniela_finalizada)
                                        <span class="rounded-xl bg-[#159447]/10 px-4 py-2 text-xs font-black text-[#159447]">
                                            Finalizada
                                        </span>
                                    @else
                                        <span class="rounded-xl bg-[#ffc400]/20 px-4 py-2 text-xs font-black text-[#080f2f]">
                                            En progreso
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="text-2xl font-black text-[#1238ff]">
                                        {{ $user->points }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="text-2xl font-black text-[#159447]">
                                        {{ $user->exact_results }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="rounded-xl bg-[#edf1ff] px-4 py-2 text-sm font-black text-[#080f2f]">
                                        {{ $user->predictions_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-lg font-black text-[#080f2f]/45">
                                    No hay participantes todavía.
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
