<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use App\Services\BracketSimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BracketController extends Controller
{
    public function simulator(Request $request)
    {
        $users = User::orderBy('name')->get();

        $selectedUser = User::find(
            $request->get('user_id', Auth::id())
        ) ?? Auth::user();

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

        return view('bracket.simulator', compact(
            'users',
            'selectedUser',
            'standings',
            'bestThirds',
            'bracketMatches'
        ));
    }

    public function generate(Request $request, BracketSimulatorService $simulator)
    {
        if ($request->has('bracket')) {
            $simulator->saveBracketPredictions(
                Auth::id(),
                $request->input('bracket', [])
            );

            return redirect()
                ->route('bracket.simulator', ['user_id' => Auth::id()])
                ->with('success', 'Marcadores de eliminatorias guardados.');
        }

        $simulator->generateForUser(Auth::id());

        return redirect()
            ->route('bracket.simulator', ['user_id' => Auth::id()])
            ->with('success', 'Simulación generada correctamente.');
    }
}