<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\PredictionScore;
use App\Services\RoundOf32Service;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(RoundOf32Service $service)
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get()
            ->groupBy('group_name');

        $round32Slots = $service->slots();

        return view('results', compact('matches', 'round32Slots'));
    }

    public function store(Request $request)
    {
        foreach ($request->input('results', []) as $matchId => $result) {
            $home = $result['home'] ?? null;
            $away = $result['away'] ?? null;
            if ($home === null || $home === '' || $away === null || $away === '') continue;

            $match = MatchGame::find($matchId);
            if (!$match) continue;

            $home = (int) $home;
            $away = (int) $away;
            $winnerId = null;

            if ($home > $away) {
                $winnerId = $match->home_team_id;
            } elseif ($away > $home) {
                $winnerId = $match->away_team_id;
            } elseif ($match->stage === 'Dieciseisavos') {
                $winnerId = (int) ($result['winner'] ?? 0);
                if (!in_array($winnerId, [$match->home_team_id, $match->away_team_id], true)) {
                    return back()->withInput()->with('error', 'Debe seleccionar quién clasificó en cada partido empatado de dieciseisavos.');
                }
            }

            $match->update([
                'home_score' => $home,
                'away_score' => $away,
                'winner_team_id' => $winnerId,
                'is_finished' => true,
            ]);

            $this->calculateScores($match);
        }

        return redirect()->route('results.index')->with('success', 'Resultados guardados y puntos recalculados.');
    }

    private function calculateScores(MatchGame $match): void
    {
        $predictions = Prediction::where('match_game_id', $match->id)->get();

        foreach ($predictions as $prediction) {
            $realHome = (int) $match->home_score;
            $realAway = (int) $match->away_score;
            $predHome = (int) $prediction->predicted_home_score;
            $predAway = (int) $prediction->predicted_away_score;
            $points = 0;
            $reason = 'Fallo';

            if ($match->stage === 'Dieciseisavos' && $realHome === $realAway) {
                $predictedDraw = $predHome === $predAway;
                $exact = $predHome === $realHome && $predAway === $realAway;
                $correctQualifier = (int) $prediction->predicted_winner_team_id === (int) $match->winner_team_id;

                if ($predictedDraw && $exact && $correctQualifier) {
                    $points = 5;
                    $reason = 'Empate exacto y clasificado correcto';
                } elseif ($predictedDraw && !$exact && $correctQualifier) {
                    $points = 3;
                    $reason = 'Empate y clasificado correctos';
                } elseif ($predictedDraw && $exact && !$correctQualifier) {
                    $points = 2;
                    $reason = 'Empate exacto, clasificado incorrecto';
                } elseif ($predictedDraw) {
                    $points = 1;
                    $reason = 'Empate correcto, clasificado incorrecto';
                }
            } elseif ($predHome === $realHome && $predAway === $realAway) {
                $points = 5;
                $reason = 'Marcador exacto';
            } elseif ($this->sameResult($realHome, $realAway, $predHome, $predAway)) {
                $points = 3;
                $reason = 'Resultado correcto';
            }

            PredictionScore::updateOrCreate(
                ['prediction_id' => $prediction->id],
                ['points' => $points, 'reason' => $reason]
            );
        }
    }

    private function sameResult(int $realHome, int $realAway, int $predHome, int $predAway): bool
    {
        return ($realHome > $realAway && $predHome > $predAway)
            || ($realHome < $realAway && $predHome < $predAway)
            || ($realHome === $realAway && $predHome === $predAway);
    }
}
