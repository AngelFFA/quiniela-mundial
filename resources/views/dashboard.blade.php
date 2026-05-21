@extends('layouts.app', ['title' => 'Panel - Quiniela Mundial'])

@section('content')
<section class="mx-auto max-w-7xl px-6 py-12">
    <h1 class="text-4xl font-black">Panel principal</h1>
    <p class="mt-3 text-white/60">
        Aquí aparecerán los partidos, tus pronósticos, los resultados reales y la tabla de posiciones.
    </p>

    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <p class="text-sm text-white/50">Tus puntos</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <p class="text-sm text-white/50">Marcadores exactos</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/10 p-6">
            <p class="text-sm text-white/50">Partidos pronosticados</p>
            <p class="mt-3 text-5xl font-black">0</p>
        </div>
    </div>
</section>
@endsection