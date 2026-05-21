<?php

namespace App\Http\Controllers;

use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use App\Services\BracketSimulatorService;
use Illuminate\Support\Facades\Auth;

class BracketController extends Controller
{
    public function simulator()
    {
        $standings = UserGroupStanding::with('team')
            ->where('user_id', Auth::id())
            ->orderBy('group_name')
            ->orderBy('position')
            ->get()
            ->groupBy('group_name');

        $bracketMatches = UserBracketMatch::with(['homeTeam', 'awayTeam'])
            ->where('user_id', Auth::id())
            ->orderBy('round')
            ->orderBy('slot')
            ->get();

        return view('bracket.simulator', compact('standings', 'bracketMatches'));
    }

    public function generate(BracketSimulatorService $simulator)
    {
        $simulator->generateForUser(Auth::id());

        return redirect()
            ->route('bracket.simulator')
            ->with('success', 'Simulador actualizado correctamente.');
    }
}