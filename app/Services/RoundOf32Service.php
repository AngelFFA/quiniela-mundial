<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoundOf32Service
{
    public function syncConfirmedMatches(): void
    {
        foreach (config('worldcup_round32', []) as $slot => $row) {
            $home = $this->resolveTeam($row['home'] ?? null);
            $away = $this->resolveTeam($row['away'] ?? null);

            if (!$home || !$away) {
                continue;
            }

            MatchGame::updateOrCreate(
                ['stage' => 'Dieciseisavos', 'bracket_slot' => $slot],
                [
                    'home_team_id' => $home->id,
                    'away_team_id' => $away->id,
                    'match_date' => $row['date'],
                ]
            );
        }
    }

    public function slots(): Collection
    {
        $this->syncConfirmedMatches();

        $matches = MatchGame::with(['homeTeam', 'awayTeam', 'winnerTeam'])
            ->where('stage', 'Dieciseisavos')
            ->whereBetween('bracket_slot', [73, 88])
            ->get()
            ->keyBy('bracket_slot');

        return collect(config('worldcup_round32', []))->map(function (array $row, int $slot) use ($matches) {
            return (object) [
                'slot' => $slot,
                'date' => $row['date'] ?? null,
                'home_label' => $row['home_label'] ?? 'Por definir',
                'away_label' => $row['away_label'] ?? 'Por definir',
                'match' => $matches->get($slot),
            ];
        })->sortBy('date')->values();
    }

    private function resolveTeam(?array $aliases): ?Team
    {
        if (!$aliases) {
            return null;
        }

        foreach ($aliases as $alias) {
            $normalized = trim($alias);
            $team = Team::query()
                ->whereRaw('LOWER(code) = ?', [mb_strtolower($normalized)])
                ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($normalized)])
                ->orWhereRaw('LOWER(short_name) = ?', [mb_strtolower($normalized)])
                ->first();

            if ($team) {
                return $team;
            }
        }

        return null;
    }
}
