<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\User;
use App\Services\RoundOf2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoundOf2Controller extends Controller
{
    public function index(RoundOf2Service $service)
    {
        $user = Auth::user();
        $slots = $service->slots();
        $matchIds = $slots->pluck('match.id')->filter();
        $predictions = Prediction::where('user_id', $user->id)
            ->whereIn('match_game_id', $matchIds)
            ->get()
            ->keyBy('match_game_id');

        return view('predictions.round2', compact('slots', 'predictions', 'user'));
    }

    public function store(Request $request, RoundOf2Service $service)
    {
        $user = Auth::user();
        if ($user->final_finalizada) {
            return back()->with('error', 'Los pronósticos de final y tercer lugar ya fueron finalizados.');
        }

        $officialMatches = $service->slots()->pluck('match')->filter()->keyBy('id');

        foreach ($request->input('predictions', []) as $matchId => $data) {
            $match = $officialMatches->get((int) $matchId);
            if (!$match) {
                continue;
            }

            $home = $data['home'] ?? null;
            $away = $data['away'] ?? null;
            if ($home === '' || $away === '' || $home === null || $away === null) {
                continue;
            }

            $home = (int) $home;
            $away = (int) $away;
            $winnerId = null;

            if ($home > $away) {
                $winnerId = $match->home_team_id;
            } elseif ($away > $home) {
                $winnerId = $match->away_team_id;
            } else {
                $winnerId = (int) ($data['winner'] ?? 0);
                if (!in_array($winnerId, [$match->home_team_id, $match->away_team_id], true)) {
                    return back()->withInput()->with('error', 'Debe seleccionar quién gana en cada partido empatado.');
                }
            }

            Prediction::updateOrCreate(
                ['user_id' => $user->id, 'match_game_id' => $match->id],
                [
                    'predicted_home_score' => $home,
                    'predicted_away_score' => $away,
                    'predicted_winner_team_id' => $winnerId,
                ]
            );
        }

        return redirect()->route('round2.index')->with('success', 'Pronósticos guardados correctamente.');
    }

    public function finalize(RoundOf2Service $service)
    {
        $user = Auth::user();
        if ($user->final_finalizada) {
            return back()->with('error', 'Los pronósticos de final y tercer lugar ya fueron finalizados.');
        }

        $officialMatches = $service->slots()->pluck('match')->filter();
        if ($officialMatches->count() < 2) {
            return back()->with('error', 'Todavía falta definir la final o el partido por el tercer lugar.');
        }

        $completed = Prediction::where('user_id', $user->id)
            ->whereIn('match_game_id', $officialMatches->pluck('id'))
            ->whereNotNull('predicted_home_score')
            ->whereNotNull('predicted_away_score')
            ->whereNotNull('predicted_winner_team_id')
            ->count();

        if ($completed < 2) {
            return back()->with('error', 'Debe completar la final y el tercer lugar antes de finalizar.');
        }

        $user->forceFill([
            'final_finalizada' => true,
            'final_finalizada_at' => now(),
        ])->save();

        return redirect()->route('round2.index')->with('success', 'Final y tercer lugar finalizados correctamente.');
    }

    public function byMatch(RoundOf2Service $service)
    {
        $currentUser = Auth::user();
        if (!$currentUser->final_finalizada) {
            return redirect()->route('round2.index')
                ->with('error', 'Debe finalizar sus pronósticos de final y tercer lugar antes de ver los pronósticos de los demás.');
        }

        $users = User::where('final_finalizada', true)->orderBy('name')->get();
        $slots = $service->slots();
        $matchIds = $slots->pluck('match.id')->filter();
        $predictions = Prediction::with('predictedWinner')
            ->whereIn('user_id', $users->pluck('id'))
            ->whereIn('match_game_id', $matchIds)
            ->get()
            ->groupBy('match_game_id');

        return view('predictions.round2_by_match', compact('users', 'slots', 'predictions'));
    }
}
