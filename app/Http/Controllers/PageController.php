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
                DB::raw('COUNT(predictions.id) as predictions_count'),
                DB::raw('COALESCE(SUM(prediction_scores.points), 0) as points'),
                DB::raw("SUM(CASE WHEN prediction_scores.reason = 'Marcador exacto' THEN 1 ELSE 0 END) as exact_results")
            )
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('points')
            ->orderByDesc('exact_results')
            ->orderByDesc('predictions_count')
            ->get();

        return view('ranking', compact('ranking'));
    }
}