<?php

namespace App\Services;

use App\Models\MatchGame;
use Illuminate\Support\Collection;

class RoundOf16Service
{
    private const MAPPING = [
        89 => [73, 75, '2026-07-04 11:00:00'],
        90 => [74, 77, '2026-07-04 15:00:00'],
        91 => [76, 78, '2026-07-05 14:00:00'],
        92 => [79, 80, '2026-07-05 18:00:00'],
        93 => [83, 84, '2026-07-06 13:00:00'],
        94 => [81, 82, '2026-07-06 18:00:00'],
        95 => [86, 88, '2026-07-07 10:00:00'],
        96 => [85, 87, '2026-07-07 14:00:00'],
    ];

    public function syncConfirmedMatches(): void
    {
        $round32 = MatchGame::query()
            ->where('stage', 'Dieciseisavos')
            ->whereBetween('bracket_slot', [73, 88])
            ->get()
            ->keyBy('bracket_slot');

        foreach (self::MAPPING as $slot => [$sourceHome, $sourceAway, $date]) {
            $homeSource = $round32->get($sourceHome);
            $awaySource = $round32->get($sourceAway);

            if (!$homeSource?->is_finished || !$awaySource?->is_finished) {
                continue;
            }

            if (!$homeSource->winner_team_id || !$awaySource->winner_team_id) {
                continue;
            }

            MatchGame::updateOrCreate(
                ['stage' => 'Octavos', 'bracket_slot' => $slot],
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
            ->where('stage', 'Octavos')
            ->whereBetween('bracket_slot', [89, 96])
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
