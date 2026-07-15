<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BracketScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function rules()
    {
        return view('rules');
    }

    public function ranking(BracketScoringService $bracketScoring)
    {
        $currentUser = Auth::user();
        $viewerFinishedRound32 = (bool) ($currentUser?->dieciseisavos_finalizados ?? false);
        $viewerFinishedRound16 = (bool) ($currentUser?->octavos_finalizados ?? false);
        $viewerFinishedRound8 = (bool) ($currentUser?->cuartos_finalizados ?? false);
        $viewerFinishedRound4 = (bool) ($currentUser?->semifinales_finalizados ?? false);
        $viewerFinishedRound2 = (bool) ($currentUser?->final_finalizada ?? false);

        $ranking = User::leftJoin('predictions', 'users.id', '=', 'predictions.user_id')
            ->leftJoin('match_games', 'predictions.match_game_id', '=', 'match_games.id')
            ->leftJoin('prediction_scores', 'predictions.id', '=', 'prediction_scores.prediction_id')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                'users.quiniela_finalizada',
                'users.quiniela_finalizada_at',
                'users.dieciseisavos_finalizados',
                'users.dieciseisavos_finalizados_at',
                'users.octavos_finalizados',
                'users.octavos_finalizados_at',
                'users.cuartos_finalizados',
                'users.cuartos_finalizados_at',
                'users.semifinales_finalizados',
                'users.semifinales_finalizados_at',
                'users.final_finalizada',
                'users.final_finalizada_at',
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Grupos' THEN predictions.id END) as predictions_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Dieciseisavos' THEN predictions.id END) as round32_predictions_count_real"),
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Octavos' THEN predictions.id END) as round16_predictions_count_real"),
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Cuartos' THEN predictions.id END) as round8_predictions_count_real"),
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Semifinales' THEN predictions.id END) as round4_predictions_count_real"),
                DB::raw("COUNT(DISTINCT CASE WHEN match_games.stage = 'Final' THEN predictions.id END) as round2_predictions_count_real"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Grupos' THEN prediction_scores.points ELSE 0 END), 0) as group_points"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Dieciseisavos' THEN prediction_scores.points ELSE 0 END), 0) as round32_points_real"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Octavos' THEN prediction_scores.points ELSE 0 END), 0) as round16_points_real"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Cuartos' THEN prediction_scores.points ELSE 0 END), 0) as round8_points_real"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Semifinales' THEN prediction_scores.points ELSE 0 END), 0) as round4_points_real"),
                DB::raw("COALESCE(SUM(CASE WHEN match_games.stage = 'Final' THEN prediction_scores.points ELSE 0 END), 0) as round2_points_real"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Grupos' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as group_exact_results"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Dieciseisavos' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as round32_exact_results_real"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Octavos' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as round16_exact_results_real"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Cuartos' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as round8_exact_results_real"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Semifinales' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as round4_exact_results_real"),
                DB::raw("SUM(CASE WHEN match_games.stage = 'Final' AND match_games.is_finished = 1 AND predictions.predicted_home_score = match_games.home_score AND predictions.predicted_away_score = match_games.away_score THEN 1 ELSE 0 END) as round2_exact_results_real")
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.avatar',
                'users.quiniela_finalizada',
                'users.quiniela_finalizada_at',
                'users.dieciseisavos_finalizados',
                'users.dieciseisavos_finalizados_at',
                'users.octavos_finalizados',
                'users.octavos_finalizados_at',
                'users.cuartos_finalizados',
                'users.cuartos_finalizados_at',
                'users.semifinales_finalizados',
                'users.semifinales_finalizados_at',
                'users.final_finalizada',
                'users.final_finalizada_at'
            )
            ->get();

        $bracketScores = $bracketScoring->scoresForUsers($ranking->pluck('id'));

        $ranking = $ranking
            ->map(function ($user) use ($bracketScores, $viewerFinishedRound32, $viewerFinishedRound16, $viewerFinishedRound8, $viewerFinishedRound4, $viewerFinishedRound2) {
                $score = $bracketScores->get((int) $user->id, [
                    'points' => 0,
                    'hits' => 0,
                    'available' => false,
                ]);

                $user->group_points = (int) $user->group_points;
                $user->bracket_points = (int) $score['points'];
                $user->bracket_hits = (int) $score['hits'];
                $user->bracket_available = (bool) $score['available'];
                $user->can_view_round32 = $viewerFinishedRound32 && (bool) $user->dieciseisavos_finalizados;
                $user->can_view_round16 = $viewerFinishedRound16 && (bool) $user->octavos_finalizados;
                $user->can_view_round8 = $viewerFinishedRound8 && (bool) $user->cuartos_finalizados;
                $user->can_view_round4 = $viewerFinishedRound4 && (bool) $user->semifinales_finalizados;
                $user->can_view_round2 = $viewerFinishedRound2 && (bool) $user->final_finalizada;
                $user->can_view_eliminations = $user->can_view_round32 || $user->can_view_round16 || $user->can_view_round8 || $user->can_view_round4 || $user->can_view_round2;
                $user->visible_predictions_count = (int) $user->predictions_count
                    + ($user->can_view_round32 ? (int) $user->round32_predictions_count_real : 0)
                    + ($user->can_view_round16 ? (int) $user->round16_predictions_count_real : 0)
                    + ($user->can_view_round8 ? (int) $user->round8_predictions_count_real : 0)
                    + ($user->can_view_round4 ? (int) $user->round4_predictions_count_real : 0)
                    + ($user->can_view_round2 ? (int) $user->round2_predictions_count_real : 0);
                $user->elimination_points = ($user->can_view_round32 ? (int) $user->round32_points_real : 0)
                    + ($user->can_view_round16 ? (int) $user->round16_points_real : 0)
                    + ($user->can_view_round8 ? (int) $user->round8_points_real : 0)
                    + ($user->can_view_round4 ? (int) $user->round4_points_real : 0)
                    + ($user->can_view_round2 ? (int) $user->round2_points_real : 0);
                $user->exact_results = (int) $user->group_exact_results
                    + ($user->can_view_round32 ? (int) $user->round32_exact_results_real : 0)
                    + ($user->can_view_round16 ? (int) $user->round16_exact_results_real : 0)
                    + ($user->can_view_round8 ? (int) $user->round8_exact_results_real : 0)
                    + ($user->can_view_round4 ? (int) $user->round4_exact_results_real : 0)
                    + ($user->can_view_round2 ? (int) $user->round2_exact_results_real : 0);
                $user->points = $user->group_points + $user->bracket_points + $user->elimination_points;

                return $user;
            })
            ->sort(function ($userA, $userB) {
                return [
                    -(int) $userA->points,
                    -(int) $userA->exact_results,
                    -(int) $userA->predictions_count,
                    (int) $userA->id,
                ] <=> [
                    -(int) $userB->points,
                    -(int) $userB->exact_results,
                    -(int) $userB->predictions_count,
                    (int) $userB->id,
                ];
            })
            ->values();

        $finalOficialTerminada = DB::table('match_games')
            ->where('stage', 'Final')
            ->where('bracket_slot', 104)
            ->where('is_finished', true)
            ->exists();

        $ganadorQuiniela = $finalOficialTerminada ? $ranking->first() : null;

        return view('ranking', compact('ranking', 'viewerFinishedRound32', 'viewerFinishedRound16', 'viewerFinishedRound8', 'viewerFinishedRound4', 'viewerFinishedRound2', 'finalOficialTerminada', 'ganadorQuiniela'));
    }

    public function rankingDetail(User $user, BracketScoringService $bracketScoring)
    {
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->quiniela_finalizada) {
            return redirect()
                ->route('ranking')
                ->with('error', 'Debe finalizar su quiniela antes de ver el detalle de otros participantes.');
        }

        if (!$user->quiniela_finalizada) {
            return redirect()
                ->route('ranking')
                ->with('error', 'Este participante aún no ha finalizado su quiniela.');
        }

        $details = DB::table('predictions')
            ->join('match_games', 'predictions.match_game_id', '=', 'match_games.id')
            ->join('teams as home_team', 'match_games.home_team_id', '=', 'home_team.id')
            ->join('teams as away_team', 'match_games.away_team_id', '=', 'away_team.id')
            ->leftJoin('prediction_scores', 'predictions.id', '=', 'prediction_scores.prediction_id')
            ->where('predictions.user_id', $user->id)
            ->where('match_games.stage', 'Grupos')
            ->select(
                'match_games.id as match_id',
                'match_games.match_date',
                'match_games.stage',
                'match_games.group_name',
                'match_games.home_score',
                'match_games.away_score',
                'match_games.is_finished',
                'home_team.name as home_team_name',
                'home_team.code as home_team_code',
                'home_team.flag as home_team_flag',
                'away_team.name as away_team_name',
                'away_team.code as away_team_code',
                'away_team.flag as away_team_flag',
                'predictions.predicted_home_score',
                'predictions.predicted_away_score',
                DB::raw('COALESCE(prediction_scores.points, 0) as points'),
                'prediction_scores.reason'
            )
            ->orderBy('match_games.match_date')
            ->orderBy('match_games.group_name')
            ->get();

        $canSeeRound32 = (bool) $currentUser->dieciseisavos_finalizados
            && (bool) $user->dieciseisavos_finalizados;
        $canSeeRound16 = (bool) $currentUser->octavos_finalizados
            && (bool) $user->octavos_finalizados;
        $canSeeRound8 = (bool) $currentUser->cuartos_finalizados
            && (bool) $user->cuartos_finalizados;
        $canSeeRound4 = (bool) $currentUser->semifinales_finalizados
            && (bool) $user->semifinales_finalizados;
        $canSeeRound2 = (bool) $currentUser->final_finalizada
            && (bool) $user->final_finalizada;
        $visibleEliminationStages = collect([
            $canSeeRound32 ? 'Dieciseisavos' : null,
            $canSeeRound16 ? 'Octavos' : null,
            $canSeeRound8 ? 'Cuartos' : null,
            $canSeeRound4 ? 'Semifinales' : null,
            $canSeeRound2 ? 'Final' : null,
        ])->filter()->values();
        $canSeeEliminations = $visibleEliminationStages->isNotEmpty();

        $eliminationDetails = collect();
        if ($canSeeEliminations) {
            $eliminationDetails = DB::table('predictions')
                ->join('match_games', 'predictions.match_game_id', '=', 'match_games.id')
                ->join('teams as home_team', 'match_games.home_team_id', '=', 'home_team.id')
                ->join('teams as away_team', 'match_games.away_team_id', '=', 'away_team.id')
                ->leftJoin('teams as predicted_winner', 'predictions.predicted_winner_team_id', '=', 'predicted_winner.id')
                ->leftJoin('teams as official_winner', 'match_games.winner_team_id', '=', 'official_winner.id')
                ->leftJoin('prediction_scores', 'predictions.id', '=', 'prediction_scores.prediction_id')
                ->where('predictions.user_id', $user->id)
                ->whereIn('match_games.stage', $visibleEliminationStages)
                ->select(
                    'match_games.id as match_id',
                    'match_games.match_date',
                    'match_games.stage',
                    'match_games.home_score',
                    'match_games.away_score',
                    'match_games.is_finished',
                    'home_team.name as home_team_name',
                    'home_team.flag as home_team_flag',
                    'away_team.name as away_team_name',
                    'away_team.flag as away_team_flag',
                    'predictions.predicted_home_score',
                    'predictions.predicted_away_score',
                    'predicted_winner.name as predicted_winner_name',
                    'official_winner.name as official_winner_name',
                    DB::raw('COALESCE(prediction_scores.points, 0) as points'),
                    'prediction_scores.reason'
                )
                ->orderBy('match_games.match_date')
                ->get();
        }

        $groupPoints = (int) $details->sum('points');
        $eliminationPoints = $canSeeEliminations ? (int) $eliminationDetails->sum('points') : 0;
        $bracketScore = $bracketScoring->scoreForUser((int) $user->id);
        $bracketPoints = (int) $bracketScore['points'];
        $totalPoints = $groupPoints + $bracketPoints + $eliminationPoints;
        $groupExactResults = $details->filter(function ($detail) {
            return (bool) $detail->is_finished
                && (int) $detail->predicted_home_score === (int) $detail->home_score
                && (int) $detail->predicted_away_score === (int) $detail->away_score;
        })->count();

        $eliminationExactResults = $canSeeEliminations
            ? $eliminationDetails->filter(function ($detail) {
                return (bool) $detail->is_finished
                    && (int) $detail->predicted_home_score === (int) $detail->home_score
                    && (int) $detail->predicted_away_score === (int) $detail->away_score;
            })->count()
            : 0;

        $exactResults = $groupExactResults + $eliminationExactResults;
        $playedMatches = $details->where('is_finished', true)->count();
        $bracketDetails = $bracketScore['details'];
        $bracketAvailable = (bool) $bracketScore['available'];

        return view('ranking_detail', compact(
            'user',
            'details',
            'groupPoints',
            'bracketPoints',
            'eliminationPoints',
            'totalPoints',
            'exactResults',
            'playedMatches',
            'bracketDetails',
            'bracketAvailable',
            'canSeeEliminations',
            'canSeeRound32',
            'canSeeRound16',
            'canSeeRound8',
            'canSeeRound4',
            'canSeeRound2',
            'eliminationDetails'
        ));
    }

}
