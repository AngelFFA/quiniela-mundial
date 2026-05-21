<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
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
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                DB::raw('COUNT(predictions.id) as predictions_count'),
                DB::raw('0 as points'),
                DB::raw('0 as exact_results')
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.avatar'
            )
            ->orderByDesc('predictions_count')
            ->get();

        return view('ranking', compact('ranking'));
    }
}