<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Illuminate\Support\Collection;

class BracketSimulatorService
{
    public function generateForUser(int $userId): void
    {
        UserGroupStanding::where('user_id', $userId)->delete();
        UserBracketMatch::where('user_id', $userId)->delete();

        $this->generateGroupStandings($userId);
        $this->generateRoundOf32($userId);
    }

    private function generateGroupStandings(int $userId): void
    {
        $groupMatches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->get();

        $table = [];

        foreach ($groupMatches as $match) {
            $prediction = Prediction::where('user_id', $userId)
                ->where('match_game_id', $match->id)
                ->first();

            if (!$prediction) {
                continue;
            }

            $homeTeam = $match->homeTeam;
            $awayTeam = $match->awayTeam;

            $this->ensureTeamInTable($table, $homeTeam);
            $this->ensureTeamInTable($table, $awayTeam);

            $homeGoals = $prediction->predicted_home_score;
            $awayGoals = $prediction->predicted_away_score;

            $table[$homeTeam->id]['played']++;
            $table[$awayTeam->id]['played']++;

            $table[$homeTeam->id]['goals_for'] += $homeGoals;
            $table[$homeTeam->id]['goals_against'] += $awayGoals;

            $table[$awayTeam->id]['goals_for'] += $awayGoals;
            $table[$awayTeam->id]['goals_against'] += $homeGoals;

            if ($homeGoals > $awayGoals) {
                $table[$homeTeam->id]['won']++;
                $table[$awayTeam->id]['lost']++;
                $table[$homeTeam->id]['points'] += 3;
            } elseif ($homeGoals < $awayGoals) {
                $table[$awayTeam->id]['won']++;
                $table[$homeTeam->id]['lost']++;
                $table[$awayTeam->id]['points'] += 3;
            } else {
                $table[$homeTeam->id]['drawn']++;
                $table[$awayTeam->id]['drawn']++;
                $table[$homeTeam->id]['points'] += 1;
                $table[$awayTeam->id]['points'] += 1;
            }
        }

        foreach ($table as $teamId => $row) {
            $table[$teamId]['goal_difference'] = $row['goals_for'] - $row['goals_against'];
        }

        $groups = collect($table)->groupBy('group_name');

        foreach ($groups as $groupName => $teams) {
            $sorted = $teams->sortByDesc('points')
                ->sortByDesc('goal_difference')
                ->sortByDesc('goals_for')
                ->values();

            foreach ($sorted as $index => $row) {
                UserGroupStanding::create([
                    'user_id' => $userId,
                    'team_id' => $row['team_id'],
                    'group_name' => $groupName,
                    'played' => $row['played'],
                    'won' => $row['won'],
                    'drawn' => $row['drawn'],
                    'lost' => $row['lost'],
                    'goals_for' => $row['goals_for'],
                    'goals_against' => $row['goals_against'],
                    'goal_difference' => $row['goal_difference'],
                    'points' => $row['points'],
                    'position' => $index + 1,
                    'qualified' => $index <= 1,
                    'qualification_type' => $index <= 1 ? 'directo' : null,
                ]);
            }
        }

        $this->markBestThirdPlaces($userId);
    }

    private function ensureTeamInTable(array &$table, Team $team): void
    {
        if (isset($table[$team->id])) {
            return;
        }

        $table[$team->id] = [
            'team_id' => $team->id,
            'group_name' => $team->group_name,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
        ];
    }

    private function markBestThirdPlaces(int $userId): void
    {
        $thirdPlaces = UserGroupStanding::where('user_id', $userId)
            ->where('position', 3)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->limit(8)
            ->get();

        foreach ($thirdPlaces as $standing) {
            $standing->update([
                'qualified' => true,
                'qualification_type' => 'mejor tercero',
            ]);
        }
    }

    private function generateRoundOf32(int $userId): void
    {
        $qualified = UserGroupStanding::with('team')
            ->where('user_id', $userId)
            ->where('qualified', true)
            ->orderBy('group_name')
            ->orderBy('position')
            ->get();

        if ($qualified->count() < 32) {
            return;
        }

        $teams = $qualified->pluck('team')->values();

        for ($i = 0; $i < 16; $i++) {
            UserBracketMatch::create([
                'user_id' => $userId,
                'round' => 'Dieciseisavos',
                'slot' => $i + 1,
                'home_team_id' => $teams[$i]->id,
                'away_team_id' => $teams[31 - $i]->id,
            ]);
        }
    }
}