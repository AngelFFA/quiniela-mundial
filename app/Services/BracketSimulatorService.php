<?php

namespace App\Services;

use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Illuminate\Support\Facades\DB;

class BracketSimulatorService
{
    public function generateForUser(int $userId): void
    {
        DB::transaction(function () use ($userId) {
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

            $bestThirds = $standings
                ->where('position', 3)
                ->sortByDesc(function ($row) {
                    return str_pad($row->points, 3, '0', STR_PAD_LEFT)
                        . str_pad($row->goal_difference + 100, 3, '0', STR_PAD_LEFT)
                        . str_pad($row->goals_for, 3, '0', STR_PAD_LEFT);
                })
                ->values()
                ->take(8);

            $thirdByGroup = [];

            foreach ($bestThirds as $third) {
                $thirdByGroup[$third->group_name] = $third;
            }

            $thirdGroups = array_keys($thirdByGroup);

            $thirdMap = $this->resolveThirdPlaceMap($thirdGroups);

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
                $homeTeamId = $this->resolveTeamId($teams[0], $positions, $thirdByGroup);
                $awayTeamId = $this->resolveTeamId($teams[1], $positions, $thirdByGroup);

                UserBracketMatch::create([
                    'user_id' => $userId,
                    'round' => 'Dieciseisavos',
                    'slot' => $slot,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
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
                }

                if ($awayScore > $homeScore) {
                    $winnerId = $match->away_team_id;
                }

                if ($homeScore === $awayScore) {
                    $winnerId = $winnerId ? (int) $winnerId : null;
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
                $prediction['predicted_home_score'] === null ||
                $prediction['predicted_away_score'] === null
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

            $match->update([
                'predicted_home_score' => $prediction['predicted_home_score'],
                'predicted_away_score' => $prediction['predicted_away_score'],
                'predicted_winner_team_id' => $winnerId,
            ]);
        }
    }

    private function winnerTeamId(?UserBracketMatch $match): ?int
    {
        return $match?->predicted_winner_team_id;
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

    private function resolveTeamId(string $code, array $positions, array $thirdByGroup): ?int
    {
        $position = (int) substr($code, 0, 1);
        $group = substr($code, 1, 1);

        if ($position === 3) {
            return $thirdByGroup[$group]->team_id ?? null;
        }

        return $positions[$group][$position]->team_id ?? null;
    }

    private function resolveThirdPlaceMap(array $thirdGroups): array
    {
        $available = collect($thirdGroups)->values();

        return [
            '1A' => $available->contains('C') ? 'C' : ($available->contains('D') ? 'D' : ($available->contains('E') ? 'E' : $available->first())),
            '1B' => $available->contains('E') ? 'E' : ($available->contains('F') ? 'F' : ($available->contains('G') ? 'G' : $available->first())),
            '1D' => $available->contains('A') ? 'A' : ($available->contains('B') ? 'B' : ($available->contains('C') ? 'C' : $available->first())),
            '1E' => $available->contains('D') ? 'D' : ($available->contains('C') ? 'C' : ($available->contains('B') ? 'B' : $available->first())),
            '1F' => $available->contains('C') ? 'C' : ($available->contains('E') ? 'E' : ($available->contains('H') ? 'H' : $available->first())),
            '1G' => $available->contains('E') ? 'E' : ($available->contains('F') ? 'F' : ($available->contains('I') ? 'I' : $available->first())),
            '1I' => $available->contains('F') ? 'F' : ($available->contains('G') ? 'G' : ($available->contains('H') ? 'H' : $available->first())),
            '1L' => $available->contains('I') ? 'I' : ($available->contains('J') ? 'J' : ($available->contains('K') ? 'K' : $available->first())),
        ];
    }

    private function deleteBracket(int $userId): void
    {
        UserBracketMatch::where('user_id', $userId)->delete();
    }
}