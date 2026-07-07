<?php

namespace App\Services;

use App\Models\MatchGame;
use Illuminate\Support\Collection;

class RoundOf8Service
{
    private const MAPPING = [
        97 => [89, 90, '2026-07-09 14:00:00'],
        98 => [93, 94, '2026-07-10 13:00:00'],
        99 => [91, 92, '2026-07-11 15:00:00'],
        100 => [95, 96, '2026-07-11 19:00:00'],
    ];

    public function syncConfirmedMatches(): void
    {
        $round16 = MatchGame::query()
            ->where('stage', 'Octavos')
            ->whereBetween('bracket_slot', [89, 96])
            ->get()
            ->keyBy('bracket_slot');

        foreach (self::MAPPING as $slot => [$sourceHome, $sourceAway, $date]) {
            $homeSource = $round16->get($sourceHome);
            $awaySource = $round16->get($sourceAway);

            if (!$homeSource?->is_finished || !$awaySource?->is_finished) {
                continue;
            }

            if (!$homeSource->winner_team_id || !$awaySource->winner_team_id) {
                continue;
            }

            MatchGame::updateOrCreate(
                ['stage' => 'Cuartos', 'bracket_slot' => $slot],
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
            ->where('stage', 'Cuartos')
            ->whereBetween('bracket_slot', [97, 100])
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
