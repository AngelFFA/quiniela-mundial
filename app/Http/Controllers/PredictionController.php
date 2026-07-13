<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use App\Services\BracketSimulatorService;
use App\Services\RoundOf32Service;
use App\Services\RoundOf16Service;
use App\Services\RoundOf8Service;
use App\Services\RoundOf4Service;
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

        $yaTieneLlaves = UserBracketMatch::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->whereNotNull('predicted_home_score')
                    ->orWhereNotNull('predicted_away_score')
                    ->orWhereNotNull('predicted_winner_team_id');
            })
            ->exists();

        $activeTab = $request->get('tab', session('active_tab', 'groups'));

        return view('predictions.index', compact(
            'matches',
            'predictions',
            'standings',
            'bestThirds',
            'bracketMatches',
            'activeTab',
            'yaTieneLlaves'
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
        $resetBracket = $request->input('reset_bracket') == '1';

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

            if ($resetBracket) {
                UserBracketMatch::where('user_id', Auth::id())->delete();
            }

            $simulator->generateForUser(Auth::id());

            return redirect()
                ->route('predictions.index', ['tab' => $activeTab])
                ->with('active_tab', $activeTab)
                ->with('success', 'Fase de grupos guardada correctamente. Las tablas y la llave fueron actualizadas.');
        }

        if ($request->has('bracket')) {
            $simulator->saveBracketPredictions(
                Auth::id(),
                $request->input('bracket', [])
            );

            return redirect()
                ->route('predictions.index', ['tab' => 'bracket'])
                ->with('active_tab', 'bracket')
                ->with('success', 'Llave guardada correctamente.');
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

        $roundOf32Ready = UserBracketMatch::where('user_id', Auth::id())
            ->whereBetween('slot', [73, 88])
            ->whereNotNull('home_team_id')
            ->whereNotNull('away_team_id')
            ->count();

        if ($roundOf32Ready < 16) {
            return redirect()
                ->route('predictions.index', ['tab' => 'bracket'])
                ->with('active_tab', 'bracket')
                ->with('error', 'Debe guardar la fase de grupos para generar la llave de dieciseisavos antes de finalizar su quiniela.');
        }

        $user->forceFill([
            'quiniela_finalizada' => true,
            'quiniela_finalizada_at' => now(),
        ])->save();

        return redirect()
            ->route('predictions.index')
            ->with('success', 'Su quiniela fue finalizada correctamente. A partir de este momento ya no podrá realizar modificaciones.');
    }

    public function publicList(Request $request, RoundOf32Service $round32Service, RoundOf16Service $round16Service, RoundOf8Service $round8Service, RoundOf4Service $round4Service)
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->quiniela_finalizada) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Debe finalizar su quiniela antes de ver las quinielas de otros participantes.');
        }

        $users = User::where('quiniela_finalizada', true)
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Todavía no hay quinielas finalizadas disponibles.');
        }

        $selectedUserId = $request->get('user_id', $currentUser->id);

        $selectedUser = User::where('quiniela_finalizada', true)
            ->where('id', $selectedUserId)
            ->first();

        if (!$selectedUser) {
            $selectedUser = $users->first();
        }

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

        $canSeeRound32 = (bool) $currentUser->dieciseisavos_finalizados
            && (bool) $selectedUser->dieciseisavos_finalizados;
        $round32Slots = collect();
        $round32Predictions = collect();

        if ($canSeeRound32) {
            $round32Slots = $round32Service->slots();
            $round32Predictions = Prediction::with('predictedWinner')
                ->where('user_id', $selectedUser->id)
                ->whereIn('match_game_id', $round32Slots->pluck('match.id')->filter())
                ->get()
                ->keyBy('match_game_id');
        }

        $canSeeRound16 = (bool) $currentUser->octavos_finalizados
            && (bool) $selectedUser->octavos_finalizados;
        $round16Slots = collect();
        $round16Predictions = collect();

        if ($canSeeRound16) {
            $round16Slots = $round16Service->slots();
            $round16Predictions = Prediction::with('predictedWinner')
                ->where('user_id', $selectedUser->id)
                ->whereIn('match_game_id', $round16Slots->pluck('match.id')->filter())
                ->get()
                ->keyBy('match_game_id');
        }


        $canSeeRound8 = (bool) $currentUser->cuartos_finalizados
            && (bool) $selectedUser->cuartos_finalizados;
        $round8Slots = collect();
        $round8Predictions = collect();

        if ($canSeeRound8) {
            $round8Slots = $round8Service->slots();
            $round8Predictions = Prediction::with('predictedWinner')
                ->where('user_id', $selectedUser->id)
                ->whereIn('match_game_id', $round8Slots->pluck('match.id')->filter())
                ->get()
                ->keyBy('match_game_id');
        }

        $canSeeRound4 = (bool) $currentUser->semifinales_finalizados
            && (bool) $selectedUser->semifinales_finalizados;
        $round4Slots = collect();
        $round4Predictions = collect();

        if ($canSeeRound4) {
            $round4Slots = $round4Service->slots();
            $round4Predictions = Prediction::with('predictedWinner')
                ->where('user_id', $selectedUser->id)
                ->whereIn('match_game_id', $round4Slots->pluck('match.id')->filter())
                ->get()
                ->keyBy('match_game_id');
        }

        return view('predictions.public', compact(
            'users',
            'selectedUser',
            'matches',
            'predictions',
            'standings',
            'bestThirds',
            'bracketMatches',
            'canSeeRound32',
            'round32Slots',
            'round32Predictions',
            'canSeeRound16',
            'round16Slots',
            'round16Predictions',
            'canSeeRound8',
            'round8Slots',
            'round8Predictions',
            'canSeeRound4',
            'round4Slots',
            'round4Predictions'
        ));
    }

    public function printFinalized()
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->quiniela_finalizada) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Debe finalizar su quiniela antes de imprimir las quinielas de otros participantes.');
        }

        $users = User::where('quiniela_finalizada', true)
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            return redirect()
                ->route('predictions.public')
                ->with('error', 'Todavía no hay quinielas finalizadas disponibles para imprimir.');
        }

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get();

        $predictions = Prediction::whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $standings = UserGroupStanding::with('team')
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('group_name')
            ->orderBy('position')
            ->get()
            ->groupBy('user_id');

        $bestThirds = UserGroupStanding::with('team')
            ->whereIn('user_id', $users->pluck('id'))
            ->where('position', 3)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get()
            ->groupBy('user_id');

        $bracketMatches = UserBracketMatch::with([
            'homeTeam',
            'awayTeam',
            'predictedWinnerTeam',
        ])
            ->whereIn('user_id', $users->pluck('id'))
            ->orderBy('slot')
            ->get()
            ->groupBy('user_id');

        return view('predictions.print', compact(
            'users',
            'matches',
            'predictions',
            'standings',
            'bestThirds',
            'bracketMatches'
        ));
    }

    public function byMatch()
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->quiniela_finalizada) {
            return redirect()
                ->route('predictions.index')
                ->with('error', 'Debe finalizar su quiniela antes de ver los pronósticos de todos por partido.');
        }

        $users = User::where('quiniela_finalizada', true)
            ->orderBy('name')
            ->get();

        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get()
            ->groupBy('group_name');

        $predictions = Prediction::whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('match_game_id');

        return view('predictions.by_match', compact(
            'users',
            'matches',
            'predictions'
        ));
    }

}
