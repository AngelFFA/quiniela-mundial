<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use App\Services\BracketSimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    public function index(Request $request)
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

        $standings = UserGroupStanding::with('team')
            ->where('user_id', Auth::id())
            ->orderBy('group_name')
            ->orderBy('position')
            ->get()
            ->groupBy('group_name');

        $bestThirds = UserGroupStanding::with('team')
            ->where('user_id', Auth::id())
            ->where('position', 3)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        $bracketMatches = UserBracketMatch::with([
            'homeTeam',
            'awayTeam',
            'predictedWinnerTeam',
        ])
            ->where('user_id', Auth::id())
            ->orderBy('slot')
            ->get();

        $activeTab = $request->get('tab', session('active_tab', 'groups'));

        return view('predictions.index', compact(
            'matches',
            'predictions',
            'standings',
            'bestThirds',
            'bracketMatches',
            'activeTab'
        ));
    }

    public function store(Request $request, BracketSimulatorService $simulator)
    {
        $activeTab = $request->input('active_tab', 'tables');

        if ($request->has('predictions')) {
            foreach ($request->input('predictions', []) as $matchId => $prediction) {
                if (
                    !isset($prediction['home']) ||
                    !isset($prediction['away']) ||
                    $prediction['home'] === '' ||
                    $prediction['away'] === '' ||
                    $prediction['home'] === null ||
                    $prediction['away'] === null
                ) {
                    continue;
                }

                Prediction::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'match_game_id' => $matchId,
                    ],
                    [
                        'predicted_home_score' => (int) $prediction['home'],
                        'predicted_away_score' => (int) $prediction['away'],
                    ]
                );
            }

            $simulator->generateForUser(Auth::id());

            return redirect()
                ->route('predictions.index', ['tab' => $activeTab])
                ->with('active_tab', $activeTab)
                ->with('success', 'Pronósticos guardados y simulación actualizada.');
        }

        if ($request->has('bracket')) {
            $simulator->saveBracketPredictions(
                Auth::id(),
                $request->input('bracket', [])
            );

            return redirect()
                ->route('predictions.index', ['tab' => 'bracket'])
                ->with('active_tab', 'bracket')
                ->with('success', 'Llave guardada y cruces actualizados.');
        }

        return redirect()
            ->route('predictions.index')
            ->with('error', 'No hay datos para guardar.');
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

        $standings = UserGroupStanding::with('team')
            ->where('user_id', $selectedUser->id)
            ->orderBy('group_name')
            ->orderBy('position')
            ->get()
            ->groupBy('group_name');

        $bestThirds = UserGroupStanding::with('team')
            ->where('user_id', $selectedUser->id)
            ->where('position', 3)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        $bracketMatches = UserBracketMatch::with([
            'homeTeam',
            'awayTeam',
            'predictedWinnerTeam',
        ])
            ->where('user_id', $selectedUser->id)
            ->orderBy('slot')
            ->get();

        return view('predictions.public', compact(
            'users',
            'selectedUser',
            'matches',
            'predictions',
            'standings',
            'bestThirds',
            'bracketMatches'
        ));
    }
}
