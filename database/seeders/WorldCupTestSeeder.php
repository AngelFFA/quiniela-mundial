<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\PredictionScore;
use App\Models\Team;
use App\Models\User;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldCupTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        UserBracketMatch::truncate();
        UserGroupStanding::truncate();
        PredictionScore::truncate();
        Prediction::truncate();
        MatchGame::truncate();
        Team::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $groups = [
            'A' => ['Mexico', 'South Africa', 'Korea Republic', 'Czechia'],
            'B' => ['Canada', 'Switzerland', 'Qatar', 'Bosnia and Herzegovina'],
            'C' => ['Brazil', 'Morocco', 'Haiti', 'Scotland'],
            'D' => ['United States', 'Paraguay', 'Australia', 'Türkiye'],
            'E' => ['Germany', 'Curaçao', 'Côte d’Ivoire', 'Ecuador'],
            'F' => ['Netherlands', 'Japan', 'Tunisia', 'Sweden'],
            'G' => ['Belgium', 'Egypt', 'Iran', 'New Zealand'],
            'H' => ['Spain', 'Cabo Verde', 'Saudi Arabia', 'Uruguay'],
            'I' => ['France', 'Senegal', 'Norway', 'Iraq'],
            'J' => ['Argentina', 'Algeria', 'Austria', 'Jordan'],
            'K' => ['Portugal', 'Uzbekistan', 'Colombia', 'Congo DR'],
            'L' => ['England', 'Croatia', 'Ghana', 'Panama'],
        ];

        foreach ($groups as $groupName => $teams) {
            foreach ($teams as $teamName) {
                Team::create([
                    'name' => $teamName,
                    'short_name' => strtoupper(substr($teamName, 0, 3)),
                    'code' => strtoupper(substr($teamName, 0, 3)),
                    'group_name' => $groupName,
                ]);
            }
        }

        foreach ($groups as $groupName => $teamNames) {
            $teams = Team::where('group_name', $groupName)->get()->values();

            $pairings = [
                [0, 1],
                [2, 3],
                [0, 2],
                [3, 1],
                [3, 0],
                [1, 2],
            ];

            foreach ($pairings as $pairing) {
                MatchGame::create([
                    'home_team_id' => $teams[$pairing[0]]->id,
                    'away_team_id' => $teams[$pairing[1]]->id,
                    'match_date' => now()->addDays(rand(1, 20)),
                    'stage' => 'Grupos',
                    'group_name' => $groupName,
                ]);
            }
        }

        $user = User::first();

        if (!$user) {
            return;
        }

        foreach (MatchGame::all() as $match) {
            Prediction::create([
                'user_id' => $user->id,
                'match_game_id' => $match->id,
                'predicted_home_score' => rand(0, 4),
                'predicted_away_score' => rand(0, 4),
            ]);
        }
    }
}