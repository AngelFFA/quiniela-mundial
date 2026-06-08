<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function ranking()
    {
        $ranking = User::leftJoin('predictions', 'users.id', '=', 'predictions.user_id')
            ->leftJoin('prediction_scores', 'predictions.id', '=', 'prediction_scores.prediction_id')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                'users.quiniela_finalizada',
                'users.quiniela_finalizada_at',
                DB::raw('COUNT(DISTINCT predictions.id) as predictions_count'),
                DB::raw('COALESCE(SUM(prediction_scores.points), 0) as points'),
                DB::raw("SUM(CASE WHEN prediction_scores.reason = 'Marcador exacto' THEN 1 ELSE 0 END) as exact_results")
            )
            ->groupBy('users.id', 'users.name', 'users.avatar', 'users.quiniela_finalizada', 'users.quiniela_finalizada_at')
            ->orderByDesc('points')
            ->orderByDesc('exact_results')
            ->orderByDesc('predictions_count')
            ->get();

        return view('ranking', compact('ranking'));
    }

    public function rankingDetail(User $user)
    {
        $details = DB::table('predictions')
            ->join('match_games', 'predictions.match_game_id', '=', 'match_games.id')
            ->join('teams as home_team', 'match_games.home_team_id', '=', 'home_team.id')
            ->join('teams as away_team', 'match_games.away_team_id', '=', 'away_team.id')
            ->leftJoin('prediction_scores', 'predictions.id', '=', 'prediction_scores.prediction_id')
            ->where('predictions.user_id', $user->id)
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

        $totalPoints = $details->sum('points');
        $exactResults = $details->where('reason', 'Marcador exacto')->count();
        $playedMatches = $details->where('is_finished', true)->count();

        return view('ranking_detail', compact(
            'user',
            'details',
            'totalPoints',
            'exactResults',
            'playedMatches'
        ));
    }
}
