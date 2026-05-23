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

    public function saveBracketPredictions(int $userId, array $bracketPredictions): void
    {
        foreach ($bracketPredictions as $matchId => $prediction) {
            $match = UserBracketMatch::where('user_id', $userId)
                ->where('id', $matchId)
                ->first();

            if (!$match) {
                continue;
            }

            $homeScore = $prediction['home'] ?? null;
            $awayScore = $prediction['away'] ?? null;
            $winnerId = $prediction['winner'] ?? null;

            if ($homeScore === null || $homeScore === '' || $awayScore === null || $awayScore === '') {
                continue;
            }

            $homeScore = (int) $homeScore;
            $awayScore = (int) $awayScore;

            if ($homeScore > $awayScore) {
                $winnerId = $match->home_team_id;
            } elseif ($awayScore > $homeScore) {
                $winnerId = $match->away_team_id;
            } else {
                $winnerId = $winnerId ? (int) $winnerId : null;
            }

            $match->update([
                'predicted_home_score' => $homeScore,
                'predicted_away_score' => $awayScore,
                'predicted_winner_team_id' => $winnerId,
            ]);
        }

        $this->rebuildNextRounds($userId);
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

            $teamA = $match->homeTeam;
            $teamB = $match->awayTeam;

            $this->addTeamToTable($table, $teamA, $match->group_name);
            $this->addTeamToTable($table, $teamB, $match->group_name);

            $scoreA = (int) $prediction->predicted_home_score;
            $scoreB = (int) $prediction->predicted_away_score;

            $table[$teamA->id]['played']++;
            $table[$teamB->id]['played']++;

            $table[$teamA->id]['goals_for'] += $scoreA;
            $table[$teamA->id]['goals_against'] += $scoreB;

            $table[$teamB->id]['goals_for'] += $scoreB;
            $table[$teamB->id]['goals_against'] += $scoreA;

            if ($scoreA > $scoreB) {
                $table[$teamA->id]['won']++;
                $table[$teamB->id]['lost']++;
                $table[$teamA->id]['points'] += 3;
            } elseif ($scoreB > $scoreA) {
                $table[$teamB->id]['won']++;
                $table[$teamA->id]['lost']++;
                $table[$teamB->id]['points'] += 3;
            } else {
                $table[$teamA->id]['drawn']++;
                $table[$teamB->id]['drawn']++;
                $table[$teamA->id]['points']++;
                $table[$teamB->id]['points']++;
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
            $teamA = $this->resolveGroupPosition($userId, $teams[0]);
            $teamB = $this->resolveGroupPosition($userId, $teams[1]);

            UserBracketMatch::create([
                'user_id' => $userId,
                'round' => 'Dieciseisavos',
                'slot' => $slot,
                'home_team_id' => $teamA?->team_id,
                'away_team_id' => $teamB?->team_id,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]);
        }
    }

    private function rebuildNextRounds(int $userId): void
    {
        UserBracketMatch::where('user_id', $userId)
            ->whereIn('round', [
                'Octavos',
                'Cuartos',
                'Semifinales',
                'Tercer lugar',
                'Final',
            ])
            ->delete();

        $this->generateRoundFromPrevious($userId, 'Octavos', [
            89 => [73, 74],
            90 => [75, 76],
            91 => [77, 78],
            92 => [79, 80],
            93 => [81, 82],
            94 => [83, 84],
            95 => [85, 86],
            96 => [87, 88],
        ]);

        $this->generateRoundFromPrevious($userId, 'Cuartos', [
            97 => [89, 90],
            98 => [91, 92],
            99 => [93, 94],
            100 => [95, 96],
        ]);

        $this->generateRoundFromPrevious($userId, 'Semifinales', [
            101 => [97, 98],
            102 => [99, 100],
        ]);

        $this->generateFinalAndThirdPlace($userId);
    }

    private function generateRoundFromPrevious(int $userId, string $round, array $matches): void
    {
        foreach ($matches as $slot => $sourceSlots) {
            $teamA = $this->getWinnerTeamId($userId, $sourceSlots[0]);
            $teamB = $this->getWinnerTeamId($userId, $sourceSlots[1]);

            if (!$teamA || !$teamB) {
                continue;
            }

            UserBracketMatch::create([
                'user_id' => $userId,
                'round' => $round,
                'slot' => $slot,
                'home_team_id' => $teamA,
                'away_team_id' => $teamB,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]);
        }
    }

    private function generateFinalAndThirdPlace(int $userId): void
    {
        $semiA = UserBracketMatch::where('user_id', $userId)->where('slot', 101)->first();
        $semiB = UserBracketMatch::where('user_id', $userId)->where('slot', 102)->first();

        if (!$semiA || !$semiB) {
            return;
        }

        if (!$semiA->predicted_winner_team_id || !$semiB->predicted_winner_team_id) {
            return;
        }

        $finalTeamA = $semiA->predicted_winner_team_id;
        $finalTeamB = $semiB->predicted_winner_team_id;

        $thirdTeamA = $this->getLoserTeamIdFromMatch($semiA);
        $thirdTeamB = $this->getLoserTeamIdFromMatch($semiB);

        if ($thirdTeamA && $thirdTeamB) {
            UserBracketMatch::create([
                'user_id' => $userId,
                'round' => 'Tercer lugar',
                'slot' => 103,
                'home_team_id' => $thirdTeamA,
                'away_team_id' => $thirdTeamB,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]);
        }

        UserBracketMatch::create([
            'user_id' => $userId,
            'round' => 'Final',
            'slot' => 104,
            'home_team_id' => $finalTeamA,
            'away_team_id' => $finalTeamB,
            'predicted_home_score' => null,
            'predicted_away_score' => null,
            'predicted_winner_team_id' => null,
        ]);
    }

    private function getWinnerTeamId(int $userId, int $slot): ?int
    {
        return UserBracketMatch::where('user_id', $userId)
            ->where('slot', $slot)
            ->value('predicted_winner_team_id');
    }

    private function getLoserTeamIdFromMatch(UserBracketMatch $match): ?int
    {
        if (!$match->predicted_winner_team_id) {
            return null;
        }

        if ((int) $match->predicted_winner_team_id === (int) $match->home_team_id) {
            return $match->away_team_id;
        }

        return $match->home_team_id;
    }

    private function getThirdPlaceMap(string $thirdGroups): array
    {
        $table = config('worldcup_third_place');

        if (!isset($table[$thirdGroups])) {
            throw new \Exception('No existe combinación FIFA para terceros: ' . $thirdGroups);
        }

        return $table[$thirdGroups];
    }

    private function resolveGroupPosition(int $userId, string $slot): ?UserGroupStanding
    {
        $position = (int) substr($slot, 0, 1);
        $groupName = substr($slot, 1, 1);

        return UserGroupStanding::where('user_id', $userId)
            ->where('group_name', $groupName)
            ->where('position', $position)
            ->first();
    }
}