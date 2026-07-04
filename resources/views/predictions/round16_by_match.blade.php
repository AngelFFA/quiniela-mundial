@extends('layouts.app', ['title' => 'Octavos de todos - Mundial 2026'])
@section('content')
@php
$flagUrl = function ($team) {
    if (!$team || !$team->flag) return null;
    return str_starts_with($team->flag, 'http') ? $team->flag : 'https://flagcdn.com/w80/' . strtolower($team->flag) . '.png';
};
@endphp
<section class="px-4 py-8 md:px-6"><div class="mx-auto max-w-6xl">
<div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between"><div><p class="text-xs font-black uppercase tracking-[.2em] text-[#1238ff]">Comparación general</p><h1 class="mt-2 text-3xl font-black text-[#080f2f] md:text-5xl">Octavos de todos</h1></div><div class="flex flex-wrap gap-2"><a href="{{ route('predictions.by_match') }}" class="rounded-xl bg-white px-4 py-3 text-sm font-black shadow ring-1 ring-black/5">Fase de grupos</a><a href="{{ route('round32.by_match') }}" class="rounded-xl bg-white px-4 py-3 text-sm font-black shadow ring-1 ring-black/5">Dieciseisavos</a><a href="{{ route('round16.index') }}" class="rounded-xl bg-[#080f2f] px-4 py-3 text-sm font-black text-white">Volver</a></div></div>
<div class="mt-8 grid gap-6 md:grid-cols-2">
@foreach($slots as $slot)
@php $match=$slot->match; @endphp
@if($match)
<article class="overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-black/5">
<div class="bg-[#080f2f] p-5 text-white"><div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3"><div class="text-center">@if($flagUrl($match->homeTeam))<img src="{{ $flagUrl($match->homeTeam) }}" class="mx-auto h-8 w-12 rounded object-cover" alt="">@endif<p class="mt-2 text-sm font-black">{{ $match->homeTeam->name }}</p></div><span class="font-black text-white/50">VS</span><div class="text-center">@if($flagUrl($match->awayTeam))<img src="{{ $flagUrl($match->awayTeam) }}" class="mx-auto h-8 w-12 rounded object-cover" alt="">@endif<p class="mt-2 text-sm font-black">{{ $match->awayTeam->name }}</p></div></div></div>
<div class="divide-y divide-[#e8edf6] p-4">@php $rows=$predictions->get($match->id, collect())->keyBy('user_id'); @endphp
@foreach($users as $participant) @php $p=$rows->get($participant->id); @endphp
<div class="grid grid-cols-[1fr_auto] items-center gap-3 py-3"><div class="text-sm font-black text-[#080f2f]">{{ $participant->name }}</div><div class="text-right"><div class="rounded-xl bg-[#edf1ff] px-3 py-2 font-black text-[#080f2f]">{{ $p ? $p->predicted_home_score.' - '.$p->predicted_away_score : '-' }}</div>@if($p && $p->predicted_home_score === $p->predicted_away_score && $p->predictedWinner)<p class="mt-1 text-[11px] font-bold text-[#080f2f]/55">{{ $p->predictedWinner->name }}</p>@endif</div></div>
@endforeach</div></article>
@endif
@endforeach
</div></div></section>
@endsection
