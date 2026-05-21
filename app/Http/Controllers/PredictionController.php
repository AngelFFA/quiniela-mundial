<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    public function index()
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get()
            ->groupBy('group_name');

        $predictions = Prediction::where('user_id', Auth::id())
            ->get()
            ->keyBy('match_game_id');

        return view('predictions.index', compact('matches', 'predictions'));
    }

    public function store(Request $request)
    {
        if (!$request->predictions) {
            return back()->with('error', 'No hay pronósticos para guardar.');
        }

        foreach ($request->predictions as $matchId => $prediction) {
            Prediction::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'match_game_id' => $matchId,
                ],
                [
                    'predicted_home_score' => $prediction['home'] ?? 0,
                    'predicted_away_score' => $prediction['away'] ?? 0,
                ]
            );
        }

        return back()->with('success', 'Pronósticos guardados correctamente.');
    }
}