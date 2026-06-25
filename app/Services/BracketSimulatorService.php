<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Illuminate\Support\Facades\DB;

class BracketSimulatorService
{
    public function generateForUser(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $this->rebuildGroupStandings($userId);
            $this->deleteBracket($userId);

            $standings = UserGroupStanding::with('team')
                ->where('user_id', $userId)
                ->get();

            if ($standings->isEmpty()) {
                return;
            }

            $positions = [];

            foreach ($standings as $row) {
                $positions[$row->group_name][$row->position] = $row;
            }

            $thirdRows = $standings
                ->where('position', 3)
                ->values()
                ->all();

            $bestThirds = $this->rankBestThirds($thirdRows);
            $bestThirds = array_slice($bestThirds, 0, 8);

            $thirdByGroup = [];

            foreach ($bestThirds as $third) {
                $thirdByGroup[$third->group_name] = $third;
            }

            UserGroupStanding::where('user_id', $userId)->update([
                'qualified' => false,
                'qualification_type' => null,
            ]);

            UserGroupStanding::where('user_id', $userId)
                ->whereIn('position', [1, 2])
                ->update([
                    'qualified' => true,
                    'qualification_type' => 'directo',
                ]);

            UserGroupStanding::where('user_id', $userId)
                ->whereIn('id', collect($bestThirds)->pluck('id')->filter()->values()->all())
                ->update([
                    'qualified' => true,
                    'qualification_type' => 'mejor_tercero',
                ]);

            $thirdMap = $this->resolveThirdPlaceMap(array_keys($thirdByGroup));

            $roundOf32 = [
                73 => ['2A', '2B'],
                74 => ['1E', $this->thirdCode($thirdMap, '1E')],
                75 => ['1F', '2C'],
                76 => ['1C', '2F'],
                77 => ['1I', $this->thirdCode($thirdMap, '1I')],
                78 => ['2E', '2I'],
                79 => ['1A', $this->thirdCode($thirdMap, '1A')],
                80 => ['1L', $this->thirdCode($thirdMap, '1L')],
                81 => ['1D', $this->thirdCode($thirdMap, '1D')],
                82 => ['1G', $this->thirdCode($thirdMap, '1G')],
                83 => ['2K', '2L'],
                84 => ['1H', '2J'],
                85 => ['1B', $this->thirdCode($thirdMap, '1B')],
                86 => ['1J', '2H'],
                87 => ['1K', $this->thirdCode($thirdMap, '1K')],
                88 => ['2D', '2G'],
            ];

            foreach ($roundOf32 as $slot => $teams) {
                UserBracketMatch::create([
                    'user_id' => $userId,
                    'round' => 'Dieciseisavos',
                    'slot' => $slot,
                    'home_team_id' => $this->resolveTeamId($teams[0], $positions, $thirdByGroup),
                    'away_team_id' => $this->resolveTeamId($teams[1], $positions, $thirdByGroup),
                    'predicted_home_score' => null,
                    'predicted_away_score' => null,
                    'predicted_winner_team_id' => null,
                ]);
            }

            $this->rebuildAdvancedRounds($userId);
        });
    }

    public function saveBracketPredictions(int $userId, array $bracketPredictions): void
    {
        DB::transaction(function () use ($userId, $bracketPredictions) {
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

                if ($homeScore === '' || $awayScore === '' || $homeScore === null || $awayScore === null) {
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

                    if (
                        $winnerId !== null
                        && (int) $winnerId !== (int) $match->home_team_id
                        && (int) $winnerId !== (int) $match->away_team_id
                    ) {
                        $winnerId = null;
                    }
                }

                $match->update([
                    'predicted_home_score' => $homeScore,
                    'predicted_away_score' => $awayScore,
                    'predicted_winner_team_id' => $winnerId,
                ]);
            }

            $this->rebuildAdvancedRounds($userId);
        });
    }

    private function rebuildGroupStandings(int $userId): void
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get();

        $predictions = Prediction::where('user_id', $userId)
            ->get()
            ->keyBy('match_game_id');

        UserGroupStanding::where('user_id', $userId)->delete();

        foreach ($matches->groupBy('group_name') as $groupName => $groupMatches) {
            $teamIds = [];

            foreach ($groupMatches as $match) {
                if ($match->home_team_id) {
                    $teamIds[$match->home_team_id] = $match->home_team_id;
                }

                if ($match->away_team_id) {
                    $teamIds[$match->away_team_id] = $match->away_team_id;
                }
            }

            $stats = [];

            foreach ($teamIds as $teamId) {
                $stats[$teamId] = [
                    'team_id' => $teamId,
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

            foreach ($groupMatches as $match) {
                $prediction = $predictions->get($match->id);

                if (
                    !$prediction
                    || $prediction->predicted_home_score === null
                    || $prediction->predicted_away_score === null
                    || !$match->home_team_id
                    || !$match->away_team_id
                ) {
                    continue;
                }

                $homeId = (int) $match->home_team_id;
                $awayId = (int) $match->away_team_id;
                $homeScore = (int) $prediction->predicted_home_score;
                $awayScore = (int) $prediction->predicted_away_score;

                $stats[$homeId]['played']++;
                $stats[$awayId]['played']++;

                $stats[$homeId]['goals_for'] += $homeScore;
                $stats[$homeId]['goals_against'] += $awayScore;

                $stats[$awayId]['goals_for'] += $awayScore;
                $stats[$awayId]['goals_against'] += $homeScore;

                if ($homeScore > $awayScore) {
                    $stats[$homeId]['won']++;
                    $stats[$homeId]['points'] += 3;
                    $stats[$awayId]['lost']++;
                } elseif ($awayScore > $homeScore) {
                    $stats[$awayId]['won']++;
                    $stats[$awayId]['points'] += 3;
                    $stats[$homeId]['lost']++;
                } else {
                    $stats[$homeId]['drawn']++;
                    $stats[$awayId]['drawn']++;
                    $stats[$homeId]['points']++;
                    $stats[$awayId]['points']++;
                }
            }

            foreach ($stats as $teamId => $row) {
                $stats[$teamId]['goal_difference'] = $row['goals_for'] - $row['goals_against'];
            }

            $orderedTeamIds = $this->rankGroupTeams(array_values($teamIds), $stats, $groupMatches, $predictions);

            foreach ($orderedTeamIds as $index => $teamId) {
                $row = $stats[$teamId];

                UserGroupStanding::create([
                    'user_id' => $userId,
                    'team_id' => $teamId,
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
                    'qualified' => $index < 2,
                    'qualification_type' => $index === 0 ? 'directo' : ($index === 1 ? 'directo' : null),
                ]);
            }
        }
    }

    private function rankGroupTeams(array $teamIds, array $stats, $groupMatches, $predictions): array
    {
        $pointBuckets = [];

        foreach ($teamIds as $teamId) {
            $pointBuckets[$stats[$teamId]['points']][] = $teamId;
        }

        krsort($pointBuckets, SORT_NUMERIC);

        $ordered = [];

        foreach ($pointBuckets as $bucket) {
            if (count($bucket) === 1) {
                $ordered[] = $bucket[0];
                continue;
            }

            $ordered = array_merge(
                $ordered,
                $this->resolveSamePointTie($bucket, $stats, $groupMatches, $predictions)
            );
        }

        return $ordered;
    }

    private function resolveSamePointTie(array $teamIds, array $stats, $groupMatches, $predictions): array
    {
        $groups = [$teamIds];

        foreach (['points', 'goal_difference', 'goals_for'] as $metric) {
            $nextGroups = [];

            foreach ($groups as $group) {
                if (count($group) <= 1) {
                    $nextGroups[] = $group;
                    continue;
                }

                $h2hStats = $this->headToHeadStats($group, $groupMatches, $predictions);
                $metricGroups = [];

                foreach ($group as $teamId) {
                    $metricGroups[$h2hStats[$teamId][$metric]][] = $teamId;
                }

                krsort($metricGroups, SORT_NUMERIC);

                foreach ($metricGroups as $metricGroup) {
                    $nextGroups[] = array_values($metricGroup);
                }
            }

            $groups = $nextGroups;
        }

        $ordered = [];

        foreach ($groups as $group) {
            if (count($group) <= 1) {
                $ordered = array_merge($ordered, $group);
                continue;
            }

            $ordered = array_merge($ordered, $this->resolveOverallTie($group, $stats));
        }

        return $ordered;
    }

    private function headToHeadStats(array $teamIds, $groupMatches, $predictions): array
    {
        $teamIds = array_map('intval', $teamIds);
        $allowed = array_flip($teamIds);
        $stats = [];

        foreach ($teamIds as $teamId) {
            $stats[$teamId] = [
                'points' => 0,
                'goal_difference' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
            ];
        }

        foreach ($groupMatches as $match) {
            $homeId = (int) $match->home_team_id;
            $awayId = (int) $match->away_team_id;

            if (!isset($allowed[$homeId]) || !isset($allowed[$awayId])) {
                continue;
            }

            $prediction = $predictions->get($match->id);

            if (
                !$prediction
                || $prediction->predicted_home_score === null
                || $prediction->predicted_away_score === null
            ) {
                continue;
            }

            $homeScore = (int) $prediction->predicted_home_score;
            $awayScore = (int) $prediction->predicted_away_score;

            $stats[$homeId]['goals_for'] += $homeScore;
            $stats[$homeId]['goals_against'] += $awayScore;
            $stats[$homeId]['goal_difference'] += $homeScore - $awayScore;

            $stats[$awayId]['goals_for'] += $awayScore;
            $stats[$awayId]['goals_against'] += $homeScore;
            $stats[$awayId]['goal_difference'] += $awayScore - $homeScore;

            if ($homeScore > $awayScore) {
                $stats[$homeId]['points'] += 3;
            } elseif ($awayScore > $homeScore) {
                $stats[$awayId]['points'] += 3;
            } else {
                $stats[$homeId]['points']++;
                $stats[$awayId]['points']++;
            }
        }

        return $stats;
    }

    private function resolveOverallTie(array $teamIds, array $stats): array
    {
        usort($teamIds, function ($teamA, $teamB) use ($stats) {
            return [
                -$stats[$teamA]['goal_difference'],
                -$stats[$teamA]['goals_for'],
                -$this->teamConductScore($teamA),
                $this->teamFifaRanking($teamA),
                $teamA,
            ] <=> [
                -$stats[$teamB]['goal_difference'],
                -$stats[$teamB]['goals_for'],
                -$this->teamConductScore($teamB),
                $this->teamFifaRanking($teamB),
                $teamB,
            ];
        });

        return $teamIds;
    }

    private function rankBestThirds(array $thirdRows): array
    {
        usort($thirdRows, function ($rowA, $rowB) {
            return [
                -$rowA->points,
                -$rowA->goal_difference,
                -$rowA->goals_for,
                -$this->teamConductScore($rowA->team_id),
                $this->teamFifaRanking($rowA->team_id),
                $rowA->team_id,
            ] <=> [
                -$rowB->points,
                -$rowB->goal_difference,
                -$rowB->goals_for,
                -$this->teamConductScore($rowB->team_id),
                $this->teamFifaRanking($rowB->team_id),
                $rowB->team_id,
            ];
        });

        return $thirdRows;
    }

    private function teamConductScore(int $teamId): int
    {
        $team = Team::find($teamId);

        if (!$team) {
            return 0;
        }

        return (int) (
            $team->team_conduct_score
            ?? $team->fair_play_score
            ?? $team->fair_play_points
            ?? 0
        );
    }

    private function teamFifaRanking(int $teamId): int
    {
        $team = Team::find($teamId);

        if (!$team) {
            return 999999;
        }

        return (int) (
            $team->fifa_ranking
            ?? $team->ranking
            ?? (999000 + $teamId)
        );
    }

    private function rebuildAdvancedRounds(int $userId): void
    {
        $savedPredictions = UserBracketMatch::where('user_id', $userId)
            ->get()
            ->keyBy('slot')
            ->map(function ($match) {
                return [
                    'predicted_home_score' => $match->predicted_home_score,
                    'predicted_away_score' => $match->predicted_away_score,
                    'predicted_winner_team_id' => $match->predicted_winner_team_id,
                ];
            })
            ->toArray();

        UserBracketMatch::where('user_id', $userId)
            ->whereIn('round', [
                'Octavos',
                'Cuartos',
                'Semifinales',
                'Tercer Puesto',
                'Final',
            ])
            ->delete();

        $this->createNextMatch($userId, 'Octavos', 89, 74, 77);
        $this->createNextMatch($userId, 'Octavos', 90, 73, 75);
        $this->createNextMatch($userId, 'Octavos', 91, 76, 78);
        $this->createNextMatch($userId, 'Octavos', 92, 79, 80);
        $this->createNextMatch($userId, 'Octavos', 93, 83, 84);
        $this->createNextMatch($userId, 'Octavos', 94, 81, 82);
        $this->createNextMatch($userId, 'Octavos', 95, 86, 88);
        $this->createNextMatch($userId, 'Octavos', 96, 85, 87);

        $this->applySavedPredictions($userId, $savedPredictions);

        $this->createNextMatch($userId, 'Cuartos', 97, 89, 90);
        $this->createNextMatch($userId, 'Cuartos', 98, 93, 94);
        $this->createNextMatch($userId, 'Cuartos', 99, 91, 92);
        $this->createNextMatch($userId, 'Cuartos', 100, 95, 96);

        $this->applySavedPredictions($userId, $savedPredictions);

        $this->createNextMatch($userId, 'Semifinales', 101, 97, 98);
        $this->createNextMatch($userId, 'Semifinales', 102, 99, 100);

        $this->applySavedPredictions($userId, $savedPredictions);

        $this->createFinalMatch($userId, 104, 101, 102);
        $this->createThirdPlaceMatch($userId, 103, 101, 102);

        $this->applySavedPredictions($userId, $savedPredictions);
    }

    private function createNextMatch(int $userId, string $round, int $newSlot, int $sourceSlotA, int $sourceSlotB): void
    {
        $sourceA = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotA)
            ->first();

        $sourceB = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotB)
            ->first();

        UserBracketMatch::updateOrCreate(
            [
                'user_id' => $userId,
                'slot' => $newSlot,
            ],
            [
                'round' => $round,
                'home_team_id' => $sourceA?->predicted_winner_team_id,
                'away_team_id' => $sourceB?->predicted_winner_team_id,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]
        );
    }

    private function createFinalMatch(int $userId, int $newSlot, int $sourceSlotA, int $sourceSlotB): void
    {
        $sourceA = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotA)
            ->first();

        $sourceB = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotB)
            ->first();

        UserBracketMatch::updateOrCreate(
            [
                'user_id' => $userId,
                'slot' => $newSlot,
            ],
            [
                'round' => 'Final',
                'home_team_id' => $sourceA?->predicted_winner_team_id,
                'away_team_id' => $sourceB?->predicted_winner_team_id,
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]
        );
    }

    private function createThirdPlaceMatch(int $userId, int $newSlot, int $sourceSlotA, int $sourceSlotB): void
    {
        $sourceA = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotA)
            ->first();

        $sourceB = UserBracketMatch::where('user_id', $userId)
            ->where('slot', $sourceSlotB)
            ->first();

        UserBracketMatch::updateOrCreate(
            [
                'user_id' => $userId,
                'slot' => $newSlot,
            ],
            [
                'round' => 'Tercer Puesto',
                'home_team_id' => $this->loserTeamId($sourceA),
                'away_team_id' => $this->loserTeamId($sourceB),
                'predicted_home_score' => null,
                'predicted_away_score' => null,
                'predicted_winner_team_id' => null,
            ]
        );
    }

    private function applySavedPredictions(int $userId, array $savedPredictions): void
    {
        foreach ($savedPredictions as $slot => $prediction) {
            $match = UserBracketMatch::where('user_id', $userId)
                ->where('slot', $slot)
                ->first();

            if (!$match) {
                continue;
            }

            if (
                $prediction['predicted_home_score'] === null
                || $prediction['predicted_away_score'] === null
            ) {
                continue;
            }

            $winnerId = $prediction['predicted_winner_team_id'];

            if ((int) $prediction['predicted_home_score'] > (int) $prediction['predicted_away_score']) {
                $winnerId = $match->home_team_id;
            }

            if ((int) $prediction['predicted_away_score'] > (int) $prediction['predicted_home_score']) {
                $winnerId = $match->away_team_id;
            }

            if (
                $winnerId !== null
                && (int) $winnerId !== (int) $match->home_team_id
                && (int) $winnerId !== (int) $match->away_team_id
            ) {
                $winnerId = null;
            }

            $match->update([
                'predicted_home_score' => $prediction['predicted_home_score'],
                'predicted_away_score' => $prediction['predicted_away_score'],
                'predicted_winner_team_id' => $winnerId,
            ]);
        }
    }

    private function loserTeamId(?UserBracketMatch $match): ?int
    {
        if (!$match || !$match->predicted_winner_team_id) {
            return null;
        }

        if ((int) $match->predicted_winner_team_id === (int) $match->home_team_id) {
            return $match->away_team_id;
        }

        if ((int) $match->predicted_winner_team_id === (int) $match->away_team_id) {
            return $match->home_team_id;
        }

        return null;
    }

    private function thirdCode(array $thirdMap, string $winnerGroupCode): ?string
    {
        if (!isset($thirdMap[$winnerGroupCode])) {
            return null;
        }

        return '3' . $thirdMap[$winnerGroupCode];
    }

    private function resolveTeamId(?string $code, array $positions, array $thirdByGroup): ?int
    {
        if (!$code || strlen($code) < 2) {
            return null;
        }

        $position = (int) substr($code, 0, 1);
        $group = substr($code, 1, 1);

        if ($position === 3) {
            return $thirdByGroup[$group]->team_id ?? null;
        }

        return $positions[$group][$position]->team_id ?? null;
    }

    private function resolveThirdPlaceMap(array $thirdGroups): array
{
    $thirdGroups = array_values(array_unique(array_filter($thirdGroups)));
    sort($thirdGroups);

    $key = implode('', $thirdGroups);

    $officialMaps = config('worldcup_third_place', []);

    if (isset($officialMaps[$key]) && count($officialMaps[$key]) === 8) {
        return $officialMaps[$key];
    }

    if (count($thirdGroups) < 8) {
        return [];
    }

    $rules = [
        '1A' => ['C', 'E', 'F', 'H', 'I'],
        '1B' => ['E', 'F', 'G', 'I', 'J'],
        '1D' => ['B', 'E', 'F', 'I', 'J'],
        '1E' => ['A', 'B', 'C', 'D', 'F'],
        '1G' => ['A', 'E', 'H', 'I', 'J'],
        '1I' => ['C', 'D', 'F', 'G', 'H'],
        '1K' => ['D', 'E', 'I', 'J', 'L'],
        '1L' => ['E', 'H', 'I', 'J', 'K'],
    ];

    $slots = array_keys($rules);

    usort($slots, function ($slotA, $slotB) use ($rules, $thirdGroups) {
        $countA = count(array_values(array_intersect($rules[$slotA], $thirdGroups)));
        $countB = count(array_values(array_intersect($rules[$slotB], $thirdGroups)));

        return $countA <=> $countB;
    });

    return $this->assignThirds($slots, $rules, $thirdGroups, [], []);
}

    private function assignThirds(array $slots, array $rules, array $thirdGroups, array $assigned, array $used): array
    {
        if (empty($slots)) {
            return $assigned;
        }

        $slot = array_shift($slots);
        $candidates = array_values(array_intersect($rules[$slot], $thirdGroups));

        foreach ($candidates as $group) {
            if (in_array($group, $used, true)) {
                continue;
            }

            $nextAssigned = $assigned;
            $nextAssigned[$slot] = $group;

            $nextUsed = $used;
            $nextUsed[] = $group;

            $result = $this->assignThirds($slots, $rules, $thirdGroups, $nextAssigned, $nextUsed);

            if (count($result) === 8) {
                return $result;
            }
        }

        return [];
    }

    private function deleteBracket(int $userId): void
    {
        UserBracketMatch::where('user_id', $userId)->delete();
    }
}
