@extends('layouts.app', ['title' => 'Cuartos - Mundial 2026'])

@section('content')
@php
    $flagUrl = function ($team) {
        if (!$team || !$team->flag) return null;
        return str_starts_with($team->flag, 'http') ? $team->flag : 'https://flagcdn.com/w80/' . strtolower($team->flag) . '.png';
    };
@endphp
<section class="px-4 py-8 md:px-6">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#1238ff]">Mi Quiniela</p>
                <h1 class="mt-2 text-3xl font-black text-[#080f2f] md:text-5xl">Cuartos</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('round32.index') }}" class="rounded-xl bg-white px-4 py-3 text-sm font-black text-[#080f2f] shadow ring-1 ring-black/5">Dieciseisavos</a>
                @if($user->cuartos_finalizados)
                    <a href="{{ route('round8.by_match') }}" class="rounded-xl bg-[#1238ff] px-4 py-3 text-sm font-black text-white">Ver pronósticos de todos</a>
                @endif
            </div>
        </div>

        @if(session('success'))<div class="mt-5 rounded-2xl bg-green-50 px-5 py-4 font-bold text-green-800">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mt-5 rounded-2xl bg-red-50 px-5 py-4 font-bold text-red-800">{{ session('error') }}</div>@endif

        @if($user->cuartos_finalizados)
            <div class="mt-5 rounded-2xl bg-green-50 px-5 py-4 font-bold text-green-800">Cuartos finalizados.</div>
        @endif

        <form id="round8-predictions-form" method="POST" action="{{ route('round8.store') }}" class="mt-7">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                @foreach($slots as $slot)
                    @php
                        $match = $slot->match;
                        $prediction = $match ? $predictions->get($match->id) : null;
                        $homeFlag = $match ? $flagUrl($match->homeTeam) : null;
                        $awayFlag = $match ? $flagUrl($match->awayTeam) : null;
                    @endphp
                    <article class="rounded-3xl bg-white p-5 shadow-lg ring-1 ring-black/5 {{ $match ? '' : 'opacity-70' }}">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-[#080f2f]/45">
                            {{ $slot->date ? \Illuminate\Support\Carbon::parse($slot->date)->format('d/m/Y H:i') : 'Pendiente' }}
                        </p>
                        <div class="mt-4 grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                            <div class="text-center">
                                @if($homeFlag)<img src="{{ $homeFlag }}" class="mx-auto h-9 w-14 rounded-md object-cover" alt="">@endif
                                <p class="mt-2 text-sm font-black text-[#080f2f]">{{ $match?->homeTeam?->name ?? $slot->home_label }}</p>
                            </div>
                            <div class="text-sm font-black text-[#080f2f]/40">VS</div>
                            <div class="text-center">
                                @if($awayFlag)<img src="{{ $awayFlag }}" class="mx-auto h-9 w-14 rounded-md object-cover" alt="">@endif
                                <p class="mt-2 text-sm font-black text-[#080f2f]">{{ $match?->awayTeam?->name ?? $slot->away_label }}</p>
                            </div>
                        </div>

                        @if($match)
                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <input type="number" min="0" inputmode="numeric" name="predictions[{{ $match->id }}][home]" value="{{ old("predictions.{$match->id}.home", $prediction?->predicted_home_score) }}" class="round8-score rounded-xl border border-[#dbe2f1] px-3 py-3 text-center text-lg font-black" data-match="{{ $match->id }}" @disabled($user->cuartos_finalizados)>
                                <input type="number" min="0" inputmode="numeric" name="predictions[{{ $match->id }}][away]" value="{{ old("predictions.{$match->id}.away", $prediction?->predicted_away_score) }}" class="round8-score rounded-xl border border-[#dbe2f1] px-3 py-3 text-center text-lg font-black" data-match="{{ $match->id }}" @disabled($user->cuartos_finalizados)>
                            </div>
                            <div id="winner-wrap-{{ $match->id }}" class="mt-3 hidden">
                                <select name="predictions[{{ $match->id }}][winner]" class="w-full rounded-xl border border-[#dbe2f1] px-3 py-3 font-bold" @disabled($user->cuartos_finalizados)>
                                    <option value="">¿Quién clasifica?</option>
                                    <option value="{{ $match->home_team_id }}" @selected((int) old("predictions.{$match->id}.winner", $prediction?->predicted_winner_team_id) === (int) $match->home_team_id)>{{ $match->homeTeam->name }}</option>
                                    <option value="{{ $match->away_team_id }}" @selected((int) old("predictions.{$match->id}.winner", $prediction?->predicted_winner_team_id) === (int) $match->away_team_id)>{{ $match->awayTeam->name }}</option>
                                </select>
                            </div>
                        @else
                            <div class="mt-5 rounded-xl bg-[#f2f5fb] px-4 py-3 text-center text-sm font-black text-[#080f2f]/45">Pendiente</div>
                        @endif
                    </article>
                @endforeach
            </div>

        </form>

        @unless($user->cuartos_finalizados)
            <div class="mt-7 flex items-center justify-end gap-3">
                <button
                    type="submit"
                    form="round8-predictions-form"
                    class="min-w-[112px] rounded-2xl bg-[#1238ff] px-5 py-4 text-sm font-black text-white"
                >
                    Guardar
                </button>

                <form method="POST" action="{{ route('round8.finalize') }}" onsubmit="return confirm('Al finalizar ya no podrá modificar sus pronósticos de cuartos. ¿Desea continuar?')">
                    @csrf
                    <button type="submit" class="min-w-[190px] rounded-2xl bg-[#159447] px-5 py-4 text-sm font-black text-white">
                        Finalizar cuartos
                    </button>
                </form>
            </div>
        @endunless
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const update = (matchId) => {
        const inputs = [...document.querySelectorAll(`.round8-score[data-match="${matchId}"]`)];
        const wrap = document.getElementById(`winner-wrap-${matchId}`);
        if (!wrap || inputs.length !== 2) return;
        const both = inputs.every(i => i.value !== '');
        wrap.classList.toggle('hidden', !(both && Number(inputs[0].value) === Number(inputs[1].value)));
    };
    document.querySelectorAll('.round8-score').forEach(input => {
        update(input.dataset.match);
        input.addEventListener('input', () => update(input.dataset.match));
    });
});
</script>
@endsection
