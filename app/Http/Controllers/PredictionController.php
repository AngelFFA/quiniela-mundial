<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use App\Services\BracketSimulatorService;
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

    public function store(Request $request, BracketSimulatorService $simulator)
    {
        if (!$request->predictions) {
            return back()->with('error', 'No hay pronósticos para guardar.');
        }

        foreach ($request->predictions as $matchId => $prediction) {
            if (
                $prediction['home'] === null ||
                $prediction['home'] === '' ||
                $prediction['away'] === null ||
                $prediction['away'] === ''
            ) {
                continue;
            }

            Prediction::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'match_game_id' => $matchId,
                ],
                [
                    'predicted_home_score' => $prediction['home'],
                    'predicted_away_score' => $prediction['away'],
                ]
            );
        }

        $simulator->generateForUser(Auth::id());

        return redirect()
            ->route('bracket.simulator', ['user_id' => Auth::id()])
            ->with('success', 'Pronósticos guardados y simulación actualizada.');
    }

    public function publicList(Request $request)
    {
        $users = User::orderBy('name')->get();

        $selectedUserId = $request->get('user_id', Auth::id());

        $selectedUser = User::findOrFail($selectedUserId);

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get()
            ->groupBy('group_name');

        $predictions = Prediction::where('user_id', $selectedUser->id)
            ->get()
            ->keyBy('match_game_id');

        return view('predictions.public', compact(
            'users',
            'selectedUser',
            'matches',
            'predictions'
        ));
    }
}