<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\User;
use App\Services\RoundOf16Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoundOf16Controller extends Controller
{
    public function index(RoundOf16Service $service)
    {
        $user = Auth::user();
        $slots = $service->slots();
        $matchIds = $slots->pluck('match.id')->filter();
        $predictions = Prediction::where('user_id', $user->id)
            ->whereIn('match_game_id', $matchIds)
            ->get()
            ->keyBy('match_game_id');

        return view('predictions.round16', compact('slots', 'predictions', 'user'));
    }

    public function store(Request $request, RoundOf16Service $service)
    {
        $user = Auth::user();
        if ($user->octavos_finalizados) {
            return back()->with('error', 'Los pronósticos de octavos ya fueron finalizados.');
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
                    return back()->withInput()->with('error', 'Debe seleccionar quién clasifica en cada partido empatado.');
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

        return redirect()->route('round16.index')->with('success', 'Pronósticos guardados correctamente.');
    }

    public function finalize(RoundOf16Service $service)
    {
        $user = Auth::user();
        if ($user->octavos_finalizados) {
            return back()->with('error', 'Los pronósticos de octavos ya fueron finalizados.');
        }

        $officialMatches = $service->slots()->pluck('match')->filter();
        if ($officialMatches->count() < 8) {
            return back()->with('error', 'Todavía faltan partidos de octavos por definirse.');
        }

        $completed = Prediction::where('user_id', $user->id)
            ->whereIn('match_game_id', $officialMatches->pluck('id'))
            ->whereNotNull('predicted_home_score')
            ->whereNotNull('predicted_away_score')
            ->whereNotNull('predicted_winner_team_id')
            ->count();

        if ($completed < 8) {
            return back()->with('error', 'Debe completar los ocho partidos antes de finalizar.');
        }

        $user->forceFill([
            'octavos_finalizados' => true,
            'octavos_finalizados_at' => now(),
        ])->save();

        return redirect()->route('round16.index')->with('success', 'Octavos finalizados correctamente.');
    }

    public function byMatch(RoundOf16Service $service)
    {
        $currentUser = Auth::user();
        if (!$currentUser->octavos_finalizados) {
            return redirect()->route('round16.index')
                ->with('error', 'Debe finalizar sus octavos antes de ver los pronósticos de los demás.');
        }

        $users = User::where('octavos_finalizados', true)->orderBy('name')->get();
        $slots = $service->slots();
        $matchIds = $slots->pluck('match.id')->filter();
        $predictions = Prediction::with('predictedWinner')
            ->whereIn('user_id', $users->pluck('id'))
            ->whereIn('match_game_id', $matchIds)
            ->get()
            ->groupBy('match_game_id');

        return view('predictions.round16_by_match', compact('users', 'slots', 'predictions'));
    }
}
