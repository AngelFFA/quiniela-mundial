<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Team;
use App\Models\UserBracketMatch;
use Illuminate\Support\Collection;

class BracketScoringService
{
    public const POINTS_PER_CORRECT_MATCHUP = 2;

    /**
     * Returns the official Round of 32 generated from completed group results.
     * An empty collection means the group stage is not complete yet.
     */
    public function actualRoundOf32(): Collection
    {
        $matches = MatchGame::with(['homeTeam', 'awayTeam'])
            ->where('stage', 'Grupos')
            ->orderBy('group_name')
            ->orderBy('match_date')
            ->get();

        if ($matches->isEmpty() || $matches->contains(fn ($match) => !$match->is_finished || $match->home_score === null || $match->away_score === null)) {
            return collect();
        }

        $positions = [];
        $thirdRows = [];

        foreach ($matches->groupBy('group_name') as $groupName => $groupMatches) {
            $teamIds = $groupMatches
                ->flatMap(fn ($match) => [(int) $match->home_team_id, (int) $match->away_team_id])
                ->unique()
                ->values()
                ->all();

            $stats = $this->emptyStats($teamIds);

            foreach ($groupMatches as $match) {
                $homeId = (int) $match->home_team_id;
                $awayId = (int) $match->away_team_id;
                $homeScore = (int) $match->home_score;
                $awayScore = (int) $match->away_score;

                $stats[$homeId]['played']++;
                $stats[$awayId]['played']++;
                $stats[$homeId]['goals_for'] += $homeScore;
                $stats[$homeId]['goals_against'] += $awayScore;
                $stats[$awayId]['goals_for'] += $awayScore;
                $stats[$awayId]['goals_against'] += $homeScore;

                if ($homeScore > $awayScore) {
                    $stats[$homeId]['points'] += 3;
                } elseif ($awayScore > $homeScore) {
                    $stats[$awayId]['points'] += 3;
                } else {
                    $stats[$homeId]['points']++;
                    $stats[$awayId]['points']++;
                }
            }

            foreach ($stats as $teamId => $row) {
                $stats[$teamId]['goal_difference'] = $row['goals_for'] - $row['goals_against'];
            }

            $ordered = $this->rankGroupTeams($teamIds, $stats, $groupMatches);

            foreach ($ordered as $index => $teamId) {
                $position = $index + 1;
                $row = (object) array_merge($stats[$teamId], [
                    'team_id' => $teamId,
                    'group_name' => (string) $groupName,
                    'position' => $position,
                ]);

                $positions[$groupName][$position] = $row;

                if ($position === 3) {
                    $thirdRows[] = $row;
                }
            }
        }

        $bestThirds = array_slice($this->rankBestThirds($thirdRows), 0, 8);
        $thirdByGroup = [];

        foreach ($bestThirds as $third) {
            $thirdByGroup[$third->group_name] = $third;
        }

        $thirdMap = $this->resolveThirdPlaceMap(array_keys($thirdByGroup));

        if (count($thirdMap) !== 8) {
            return collect();
        }

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

        return collect($roundOf32)->map(function (array $codes, int $slot) use ($positions, $thirdByGroup) {
            return [
                'slot' => $slot,
                'home_team_id' => $this->resolveTeamId($codes[0], $positions, $thirdByGroup),
                'away_team_id' => $this->resolveTeamId($codes[1], $positions, $thirdByGroup),
            ];
        });
    }

    public function scoreForUser(int $userId): array
    {
        $actual = $this->actualRoundOf32();

        if ($actual->isEmpty()) {
            return ['points' => 0, 'hits' => 0, 'available' => false, 'details' => collect()];
        }

        $predicted = UserBracketMatch::with(['homeTeam', 'awayTeam'])
            ->where('user_id', $userId)
            ->whereBetween('slot', [73, 88])
            ->get()
            ->keyBy('slot');

        $teamIds = $actual
            ->flatMap(fn ($row) => [$row['home_team_id'], $row['away_team_id']])
            ->filter()
            ->unique();

        $teams = Team::whereIn('id', $teamIds)->get()->keyBy('id');

        $details = $actual->map(function (array $official, int $slot) use ($predicted, $teams) {
            $prediction = $predicted->get($slot);
            $officialPair = [(int) $official['home_team_id'], (int) $official['away_team_id']];
            $predictedPair = $prediction
                ? [(int) $prediction->home_team_id, (int) $prediction->away_team_id]
                : [0, 0];

            sort($officialPair);
            sort($predictedPair);

            $correct = $officialPair === $predictedPair;

            return [
                'slot' => $slot,
                'correct' => $correct,
                'points' => $correct ? self::POINTS_PER_CORRECT_MATCHUP : 0,
                'official_home' => $teams->get($official['home_team_id']),
                'official_away' => $teams->get($official['away_team_id']),
                'predicted_home' => $prediction?->homeTeam,
                'predicted_away' => $prediction?->awayTeam,
            ];
        })->values();

        $hits = $details->where('correct', true)->count();

        return [
            'points' => $hits * self::POINTS_PER_CORRECT_MATCHUP,
            'hits' => $hits,
            'available' => true,
            'details' => $details,
        ];
    }

    public function scoresForUsers(Collection $userIds): Collection
    {
        return $userIds->mapWithKeys(fn ($userId) => [(int) $userId => $this->scoreForUser((int) $userId)]);
    }

    private function emptyStats(array $teamIds): array
    {
        $stats = [];

        foreach ($teamIds as $teamId) {
            $stats[(int) $teamId] = [
                'played' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            ];
        }

        return $stats;
    }

    private function rankGroupTeams(array $teamIds, array $stats, Collection $groupMatches): array
    {
        $pointBuckets = [];

        foreach ($teamIds as $teamId) {
            $pointBuckets[$stats[$teamId]['points']][] = $teamId;
        }

        krsort($pointBuckets, SORT_NUMERIC);
        $ordered = [];

        foreach ($pointBuckets as $bucket) {
            $ordered = array_merge(
                $ordered,
                count($bucket) === 1 ? $bucket : $this->resolveSamePointTie($bucket, $stats, $groupMatches)
            );
        }

        return $ordered;
    }

    private function resolveSamePointTie(array $teamIds, array $stats, Collection $groupMatches): array
    {
        $groups = [$teamIds];

        foreach (['points', 'goal_difference', 'goals_for'] as $metric) {
            $nextGroups = [];

            foreach ($groups as $group) {
                if (count($group) <= 1) {
                    $nextGroups[] = $group;
                    continue;
                }

                $h2hStats = $this->headToHeadStats($group, $groupMatches);
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
            $ordered = array_merge(
                $ordered,
                count($group) <= 1 ? $group : $this->resolveOverallTie($group, $stats)
            );
        }

        return $ordered;
    }

    private function headToHeadStats(array $teamIds, Collection $groupMatches): array
    {
        $allowed = array_flip(array_map('intval', $teamIds));
        $stats = $this->emptyStats($teamIds);

        foreach ($groupMatches as $match) {
            $homeId = (int) $match->home_team_id;
            $awayId = (int) $match->away_team_id;

            if (!isset($allowed[$homeId]) || !isset($allowed[$awayId])) {
                continue;
            }

            $homeScore = (int) $match->home_score;
            $awayScore = (int) $match->away_score;

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

        return (int) ($team?->team_conduct_score ?? $team?->fair_play_score ?? $team?->fair_play_points ?? 0);
    }

    private function teamFifaRanking(int $teamId): int
    {
        $team = Team::find($teamId);

        return (int) ($team?->fifa_ranking ?? $team?->ranking ?? (999000 + $teamId));
    }

    private function thirdCode(array $thirdMap, string $winnerGroupCode): ?string
    {
        return isset($thirdMap[$winnerGroupCode]) ? '3' . $thirdMap[$winnerGroupCode] : null;
    }

    private function resolveTeamId(?string $code, array $positions, array $thirdByGroup): ?int
    {
        if (!$code || strlen($code) < 2) {
            return null;
        }

        $position = (int) substr($code, 0, 1);
        $group = substr($code, 1, 1);

        return $position === 3
            ? ($thirdByGroup[$group]->team_id ?? null)
            : ($positions[$group][$position]->team_id ?? null);
    }

    private function resolveThirdPlaceMap(array $thirdGroups): array
    {
        $thirdGroups = array_values(array_unique(array_filter($thirdGroups)));
        sort($thirdGroups);

        if (count($thirdGroups) < 8) {
            return [];
        }

        $key = implode('', $thirdGroups);
        $officialMaps = config('worldcup_third_place', []);

        if (isset($officialMaps[$key]) && count($officialMaps[$key]) === 8) {
            return $officialMaps[$key];
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
            return count(array_intersect($rules[$slotA], $thirdGroups)) <=> count(array_intersect($rules[$slotB], $thirdGroups));
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

            $result = $this->assignThirds(
                $slots,
                $rules,
                $thirdGroups,
                array_merge($assigned, [$slot => $group]),
                array_merge($used, [$group])
            );

            if (count($result) === 8) {
                return $result;
            }
        }

        return [];
    }
}
