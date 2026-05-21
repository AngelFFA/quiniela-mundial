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

        $selectedUserId = $request->get('user_id', Auth::id());

        $selectedUser = User::findOrFail($selectedUserId);

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

        $bracketMatches = UserBracketMatch::with(['homeTeam', 'awayTeam'])
            ->where('user_id', $selectedUser->id)
            ->orderBy('round')
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

    public function generate(BracketSimulatorService $simulator)
    {
        $simulator->generateForUser(Auth::id());

        return redirect()
            ->route('bracket.simulator', ['user_id' => Auth::id()])
            ->with('success', 'Simulación actualizada correctamente.');
    }
}