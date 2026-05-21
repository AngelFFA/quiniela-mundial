<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\PredictionScore;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get()
            ->groupBy('group_name');

        return view('results', compact('matches'));
    }

    public function store(Request $request)
    {
        foreach ($request->results as $matchId => $result) {
            if (
                $result['home'] === null ||
                $result['home'] === '' ||
                $result['away'] === null ||
                $result['away'] === ''
            ) {
                continue;
            }

            $match = MatchGame::find($matchId);

            if (!$match) {
                continue;
            }

            $match->update([
                'home_score' => $result['home'],
                'away_score' => $result['away'],
                'is_finished' => true,
            ]);

            $this->calculateScores($match);
        }

        return redirect()
            ->route('ranking')
            ->with('success', 'Resultados guardados y puntos recalculados.');
    }

    private function calculateScores(MatchGame $match): void
    {
        $predictions = Prediction::where('match_game_id', $match->id)->get();

        foreach ($predictions as $prediction) {
            $points = 0;
            $reason = 'Fallo';

            $realHome = (int) $match->home_score;
            $realAway = (int) $match->away_score;

            $predHome = (int) $prediction->predicted_home_score;
            $predAway = (int) $prediction->predicted_away_score;

            if ($predHome === $realHome && $predAway === $realAway) {
                $points = 5;
                $reason = 'Marcador exacto';
            } elseif ($this->sameResult($realHome, $realAway, $predHome, $predAway)) {
                $points = 3;
                $reason = 'Resultado correcto';
            }

            PredictionScore::updateOrCreate(
                [
                    'prediction_id' => $prediction->id,
                ],
                [
                    'points' => $points,
                    'reason' => $reason,
                ]
            );
        }
    }

    private function sameResult(int $realHome, int $realAway, int $predHome, int $predAway): bool
    {
        if ($realHome > $realAway && $predHome > $predAway) {
            return true;
        }

        if ($realHome < $realAway && $predHome < $predAway) {
            return true;
        }

        if ($realHome === $realAway && $predHome === $predAway) {
            return true;
        }

        return false;
    }
}