<?php

namespace App\Services;

use App\Models\MatchGame;
use Illuminate\Support\Collection;

class RoundOf4Service
{
    private const MAPPING = [
        101 => [97, 98, '2026-07-14 13:00:00'],
        102 => [99, 100, '2026-07-15 13:00:00'],
    ];

    public function syncConfirmedMatches(): void
    {
        $round8 = MatchGame::query()
            ->where('stage', 'Cuartos')
            ->whereBetween('bracket_slot', [97, 100])
            ->get()
            ->keyBy('bracket_slot');

        foreach (self::MAPPING as $slot => [$sourceHome, $sourceAway, $date]) {
            $homeSource = $round8->get($sourceHome);
            $awaySource = $round8->get($sourceAway);

            if (!$homeSource?->is_finished || !$awaySource?->is_finished) {
                continue;
            }

            if (!$homeSource->winner_team_id || !$awaySource->winner_team_id) {
                continue;
            }

            MatchGame::updateOrCreate(
                ['stage' => 'Semifinales', 'bracket_slot' => $slot],
                [
                    'home_team_id' => $homeSource->winner_team_id,
                    'away_team_id' => $awaySource->winner_team_id,
                    'match_date' => $date,
                ]
            );
        }
    }

    public function slots(): Collection
    {
        $this->syncConfirmedMatches();

        $matches = MatchGame::with(['homeTeam', 'awayTeam', 'winnerTeam'])
            ->where('stage', 'Semifinales')
            ->whereBetween('bracket_slot', [101, 102])
            ->get()
            ->keyBy('bracket_slot');

        return collect(self::MAPPING)->map(function (array $row, int $slot) use ($matches) {
            return (object) [
                'slot' => $slot,
                'date' => $row[2],
                'home_label' => 'Por definir',
                'away_label' => 'Por definir',
                'match' => $matches->get($slot),
            ];
        })->sortBy('date')->values();
    }
}
