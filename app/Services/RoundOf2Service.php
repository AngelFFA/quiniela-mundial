<?php

namespace App\Services;

use App\Models\MatchGame;
use Illuminate\Support\Collection;

class RoundOf2Service
{
    private const MAPPING = [
        103 => ['type' => 'third_place', 'label' => 'Tercer lugar', 'date' => '2026-07-18 15:00:00'],
        104 => ['type' => 'final', 'label' => 'Final', 'date' => '2026-07-19 13:00:00'],
    ];

    public function syncConfirmedMatches(): void
    {
        $semifinals = MatchGame::query()
            ->where('stage', 'Semifinales')
            ->whereBetween('bracket_slot', [101, 102])
            ->get()
            ->keyBy('bracket_slot');

        $semiOne = $semifinals->get(101);
        $semiTwo = $semifinals->get(102);

        if (!$semiOne?->is_finished || !$semiTwo?->is_finished) {
            return;
        }

        if (!$semiOne->winner_team_id || !$semiTwo->winner_team_id) {
            return;
        }

        $loserOne = $this->loserTeamId($semiOne);
        $loserTwo = $this->loserTeamId($semiTwo);

        if (!$loserOne || !$loserTwo) {
            return;
        }

        MatchGame::updateOrCreate(
            ['stage' => 'Final', 'bracket_slot' => 103],
            [
                'home_team_id' => $loserOne,
                'away_team_id' => $loserTwo,
                'match_date' => self::MAPPING[103]['date'],
            ]
        );

        MatchGame::updateOrCreate(
            ['stage' => 'Final', 'bracket_slot' => 104],
            [
                'home_team_id' => $semiOne->winner_team_id,
                'away_team_id' => $semiTwo->winner_team_id,
                'match_date' => self::MAPPING[104]['date'],
            ]
        );
    }

    public function slots(): Collection
    {
        $this->syncConfirmedMatches();

        $matches = MatchGame::with(['homeTeam', 'awayTeam', 'winnerTeam'])
            ->where('stage', 'Final')
            ->whereIn('bracket_slot', [103, 104])
            ->get()
            ->keyBy('bracket_slot');

        return collect(self::MAPPING)->map(function (array $row, int $slot) use ($matches) {
            return (object) [
                'slot' => $slot,
                'title' => $row['label'],
                'date' => $row['date'],
                'home_label' => 'Por definir',
                'away_label' => 'Por definir',
                'match' => $matches->get($slot),
            ];
        })->sortBy('date')->values();
    }

    private function loserTeamId(MatchGame $match): ?int
    {
        if ((int) $match->winner_team_id === (int) $match->home_team_id) {
            return (int) $match->away_team_id;
        }

        if ((int) $match->winner_team_id === (int) $match->away_team_id) {
            return (int) $match->home_team_id;
        }

        return null;
    }
}
