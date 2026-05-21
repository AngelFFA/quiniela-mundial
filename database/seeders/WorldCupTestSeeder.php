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

        $teams = [
            ['name' => 'México', 'group_name' => 'A'],
            ['name' => 'Estados Unidos', 'group_name' => 'A'],
            ['name' => 'Canadá', 'group_name' => 'A'],
            ['name' => 'Japón', 'group_name' => 'A'],

            ['name' => 'Argentina', 'group_name' => 'B'],
            ['name' => 'Brasil', 'group_name' => 'B'],
            ['name' => 'España', 'group_name' => 'B'],
            ['name' => 'Francia', 'group_name' => 'B'],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }

        $groups = Team::all()->groupBy('group_name');

        foreach ($groups as $teamsGroup) {
            $teamsArray = $teamsGroup->values();

            for ($i = 0; $i < $teamsArray->count(); $i++) {
                for ($j = $i + 1; $j < $teamsArray->count(); $j++) {
                    MatchGame::create([
                        'home_team_id' => $teamsArray[$i]->id,
                        'away_team_id' => $teamsArray[$j]->id,
                        'match_date' => now(),
                        'stage' => 'Grupos',
                        'group_name' => $teamsArray[$i]->group_name,
                    ]);
                }
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