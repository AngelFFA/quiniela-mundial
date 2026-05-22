<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;

class BracketSimulatorService
{
    public function generateForUser(int $userId): void
    {
        UserGroupStanding::where('user_id', $userId)->delete();
        UserBracketMatch::where('user_id', $userId)->delete();

        $this->generateGroupStandings($userId);
        $this->markBestThirdPlaces($userId);
        $this->generateRoundOf32($userId);
    }

    private function generateGroupStandings(int $userId): void
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get();

        $table = [];

        foreach ($matches as $match) {
            $prediction = Prediction::where('user_id', $userId)
                ->where('match_game_id', $match->id)
                ->first();

            if (!$prediction) {
                continue;
            }

            $homeTeam = $match->homeTeam;
            $awayTeam = $match->awayTeam;

            $this->addTeamToTable($table, $homeTeam, $match->group_name);
            $this->addTeamToTable($table, $awayTeam, $match->group_name);

            $homeScore = (int) $prediction->predicted_home_score;
            $awayScore = (int) $prediction->predicted_away_score;

            $table[$homeTeam->id]['played']++;
            $table[$awayTeam->id]['played']++;

            $table[$homeTeam->id]['goals_for'] += $homeScore;
            $table[$homeTeam->id]['goals_against'] += $awayScore;

            $table[$awayTeam->id]['goals_for'] += $awayScore;
            $table[$awayTeam->id]['goals_against'] += $homeScore;

            if ($homeScore > $awayScore) {
                $table[$homeTeam->id]['won']++;
                $table[$awayTeam->id]['lost']++;
                $table[$homeTeam->id]['points'] += 3;
            } elseif ($awayScore > $homeScore) {
                $table[$awayTeam->id]['won']++;
                $table[$homeTeam->id]['lost']++;
                $table[$awayTeam->id]['points'] += 3;
            } else {
                $table[$homeTeam->id]['drawn']++;
                $table[$awayTeam->id]['drawn']++;
                $table[$homeTeam->id]['points']++;
                $table[$awayTeam->id]['points']++;
            }
        }

        foreach ($table as $teamId => $row) {
            $table[$teamId]['goal_difference'] = $row['goals_for'] - $row['goals_against'];
        }

        $groups = collect($table)->groupBy('group_name');

        foreach ($groups as $groupName => $teams) {
            $sorted = $teams->sort(function ($a, $b) {
                if ($a['points'] !== $b['points']) {
                    return $b['points'] <=> $a['points'];
                }

                if ($a['goal_difference'] !== $b['goal_difference']) {
                    return $b['goal_difference'] <=> $a['goal_difference'];
                }

                if ($a['goals_for'] !== $b['goals_for']) {
                    return $b['goals_for'] <=> $a['goals_for'];
                }

                return strcmp($a['team_name'], $b['team_name']);
            })->values();

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
    }

    private function addTeamToTable(array &$table, Team $team, ?string $groupName): void
    {
        if (isset($table[$team->id])) {
            return;
        }

        $table[$team->id] = [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'group_name' => $groupName,
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
            ->get();

        foreach ($thirdPlaces as $index => $third) {
            $third->update([
                'qualified' => $index < 8,
                'qualification_type' => $index < 8 ? 'mejor tercero' : null,
            ]);
        }
    }

    private function generateRoundOf32(int $userId): void
    {
        $thirdGroups = UserGroupStanding::where('user_id', $userId)
            ->where('position', 3)
            ->where('qualified', true)
            ->pluck('group_name')
            ->sort()
            ->values()
            ->implode('');

        $thirdMap = $this->getThirdPlaceMap($thirdGroups);

        $roundOf32 = [
            73 => ['2A', '2B'],
            74 => ['1E', '3' . $thirdMap['1E']],
            75 => ['1I', '3' . $thirdMap['1I']],
            76 => ['1F', '2C'],
            77 => ['2K', '2L'],
            78 => ['1H', '2J'],
            79 => ['1D', '3' . $thirdMap['1D']],
            80 => ['1G', '3' . $thirdMap['1G']],
            81 => ['1C', '2F'],
            82 => ['2E', '2I'],
            83 => ['1A', '3' . $thirdMap['1A']],
            84 => ['1L', '3' . $thirdMap['1L']],
            85 => ['1J', '2H'],
            86 => ['2D', '2G'],
            87 => ['1B', '3' . $thirdMap['1B']],
            88 => ['1K', '3' . $thirdMap['1K']],
        ];

        foreach ($roundOf32 as $slot => $teams) {
            $home = $this->resolveSlot($userId, $teams[0]);
            $away = $this->resolveSlot($userId, $teams[1]);

            UserBracketMatch::create([
                'user_id' => $userId,
                'round' => 'Dieciseisavos',
                'slot' => $slot,
                'home_team_id' => $home?->team_id,
                'away_team_id' => $away?->team_id,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]);
        }
    }

    private function getThirdPlaceMap(string $thirdGroups): array
    {
        $table = config('worldcup_third_place');

        if (!isset($table[$thirdGroups])) {
            throw new \Exception('No existe combinación FIFA para terceros: ' . $thirdGroups);
        }

        return $table[$thirdGroups];
    }

    private function resolveSlot(int $userId, string $slot): ?UserGroupStanding
    {
        $position = (int) substr($slot, 0, 1);
        $groupName = substr($slot, 1, 1);

        return UserGroupStanding::where('user_id', $userId)
            ->where('group_name', $groupName)
            ->where('position', $position)
            ->first();
    }
}