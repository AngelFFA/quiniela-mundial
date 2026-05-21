@extends('layouts.app', ['title' => 'Ranking - Quiniela Mundial'])

@section('content')
<section class="relative px-6 py-12">
    <div class="absolute -left-32 top-20 h-[420px] w-[420px] rounded-full bg-[#1238ff]"></div>
    <div class="absolute -right-16 top-16 h-[430px] w-[430px] rounded-full bg-[#e51b2b]"></div>
    <div class="absolute right-[-100px] top-[360px] h-[420px] w-[420px] rounded-full bg-[#ffc400]"></div>

    <div class="relative mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div>
                <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                    Competencia
                </div>

                <h1 class="mt-6 text-6xl font-black leading-tight text-[#080f2f]">
                    Ranking General
                </h1>

                <p class="mt-4 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                    Tabla de posiciones de todos los participantes de la quiniela.
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

        <div class="mt-10 overflow-hidden rounded-[2rem] bg-white shadow-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#080f2f] text-white">
                        <tr>
                            <th class="px-6 py-5 text-left text-sm font-black uppercase">
                                #
                            </th>

                            <th class="px-6 py-5 text-left text-sm font-black uppercase">
                                Participante
                            </th>

                            <th class="px-6 py-5 text-center text-sm font-black uppercase">
                                Puntos
                            </th>

                            <th class="px-6 py-5 text-center text-sm font-black uppercase">
                                Exactos
                            </th>

                            <th class="px-6 py-5 text-center text-sm font-black uppercase">
                                Pronósticos
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ranking as $user)
                            <tr class="border-b border-slate-100 hover:bg-[#f8f9ff]">
                                <td class="px-6 py-5 text-lg font-black text-[#080f2f]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <img
                                            src="{{ $user->avatar }}"
                                            class="h-14 w-14 rounded-full border-4 border-white shadow-lg"
                                            alt="{{ $user->name }}"
                                        >

                                        <div>
                                            <p class="text-base font-black text-[#080f2f]">
                                                {{ $user->name }}
                                            </p>
                                        </div>
                                    </div>
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
                                    <span class="rounded-2xl bg-[#edf1ff] px-4 py-2 text-sm font-black text-[#080f2f]">
                                        {{ $user->predictions_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-lg font-black text-[#080f2f]/45">
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