@extends('layouts.app', ['title' => 'Quinielas - Mundial 2026'])

@section('content')
<section class="px-6 py-12">
    <div class="mx-auto max-w-5xl">
        <div class="rounded-[2rem] bg-white p-8 shadow-xl ring-1 ring-black/5 md:p-12">
            <div class="inline-flex rounded-full bg-[#edf1ff] px-5 py-2 text-xs font-black uppercase tracking-[0.25em] text-[#1238ff]">
                Quinielas
            </div>

            <h1 class="mt-6 text-4xl font-black leading-tight text-[#080f2f] md:text-6xl">
                Vista no disponible por el momento
            </h1>

            <p class="mt-5 max-w-3xl text-lg font-medium leading-8 text-[#080f2f]/65">
                Este apartado estará disponible cuando todos los participantes hayan completado su quiniela.
            </p>

            <div class="mt-8 rounded-[1.5rem] bg-[#080f2f] p-6 text-white">
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-white/50">
                    Información
                </p>

                <p class="mt-3 text-base font-medium leading-7 text-white/75">
                    Por ahora, cada participante debe ingresar a la opción “Mi Quiniela” para completar y guardar sus pronósticos.
                </p>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('predictions.index') }}" class="rounded-2xl bg-[#1238ff] px-6 py-4 text-center text-sm font-black text-white shadow-lg">
                    Ir a Mi Quiniela
                </a>

                <a href="{{ route('ranking') }}" class="rounded-2xl border border-[#080f2f]/10 bg-white px-6 py-4 text-center text-sm font-black text-[#080f2f] shadow-sm">
                    Ver Ranking
                </a>
            </div>
        </div>
    </div>
</section>
@endsection