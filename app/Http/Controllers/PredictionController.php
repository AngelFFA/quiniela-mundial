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
        $user = Auth::user();

        if ($user->quiniela_finalizada) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Su quiniela ya fue finalizada y no puede modificarse.');
        }

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
                ->with('success', 'Pronósticos guardados preliminarmente. Las tablas y la llave fueron actualizadas.');
        }

        if ($request->has('bracket')) {
            $simulator->saveBracketPredictions(
                Auth::id(),
                $request->input('bracket', [])
            );

            return redirect()
                ->route('predictions.index', ['tab' => 'bracket'])
                ->with('active_tab', 'bracket')
                ->with('success', 'Llave guardada preliminarmente.');
        }

        return redirect()
            ->route('predictions.index')
            ->with('error', 'No hay datos para guardar.');
    }

    public function finalize(Request $request, BracketSimulatorService $simulator)
    {
        $user = Auth::user();

        if ($user->quiniela_finalizada) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Su quiniela ya fue finalizada anteriormente.');
        }

        $totalGroupMatches = MatchGame::where('stage', 'Grupos')->count();

        $completedPredictions = Prediction::where('user_id', Auth::id())
            ->whereNotNull('predicted_home_score')
            ->whereNotNull('predicted_away_score')
            ->count();

        if ($completedPredictions < $totalGroupMatches) {
            return redirect()
                ->route('predictions.index', ['tab' => 'groups'])
                ->with('active_tab', 'groups')
                ->with('error', 'Debe completar todos los partidos de la fase de grupos antes de finalizar su quiniela.');
        }

        $simulator->generateForUser(Auth::id());

        $pendingBracket = UserBracketMatch::where('user_id', Auth::id())
            ->whereNotNull('home_team_id')
            ->whereNotNull('away_team_id')
            ->where(function ($query) {
                $query->whereNull('predicted_home_score')
                    ->orWhereNull('predicted_away_score')
                    ->orWhereNull('predicted_winner_team_id');
            })
            ->count();

        if ($pendingBracket > 0) {
            return redirect()
                ->route('predictions.index', ['tab' => 'bracket'])
                ->with('active_tab', 'bracket')
                ->with('error', 'Debe completar toda la llave de eliminación antes de finalizar su quiniela.');
        }

        $user->forceFill([
            'quiniela_finalizada' => true,
            'quiniela_finalizada_at' => now(),
        ])->save();

        return redirect()
            ->route('predictions.index')
            ->with('success', 'Su quiniela fue finalizada correctamente. A partir de este momento ya no podrá realizar modificaciones.');
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