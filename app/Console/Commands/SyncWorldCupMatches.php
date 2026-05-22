<?php

namespace App\Console\Commands;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\PredictionScore;
use App\Models\Team;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncWorldCupMatches extends Command
{
    protected $signature = 'worldcup:sync';
    protected $description = 'Sincroniza calendario completo del Mundial 2026';

    public function handle(): int
    {
        $this->info('Limpiando datos anteriores...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        PredictionScore::truncate();
        Prediction::truncate();
        UserBracketMatch::truncate();
        UserGroupStanding::truncate();
        MatchGame::truncate();
        Team::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Obteniendo calendario completo del Mundial 2026...');

        $response = Http::timeout(60)->get(
            'https://raw.githubusercontent.com/openfootball/worldcup.json/master/2026/worldcup.json'
        );

        if (!$response->successful()) {
            $this->error('No se pudo obtener el calendario.');
            return self::FAILURE;
        }

        $data = $response->json();

        if (!isset($data['matches']) || empty($data['matches'])) {
            $this->error('El JSON no devolvió partidos.');
            return self::FAILURE;
        }

        $flagMap = [
            'Mexico' => 'mx',
            'South Africa' => 'za',
            'South Korea' => 'kr',
            'Czech Republic' => 'cz',
            'Canada' => 'ca',
            'Bosnia and Herzegovina' => 'ba',
            'Bosnia-Herzegovina' => 'ba',
            'Qatar' => 'qa',
            'Switzerland' => 'ch',
            'Brazil' => 'br',
            'Morocco' => 'ma',
            'Haiti' => 'ht',
            'Scotland' => 'gb-sct',
            'USA' => 'us',
            'United States' => 'us',
            'Paraguay' => 'py',
            'Australia' => 'au',
            'Turkey' => 'tr',
            'Germany' => 'de',
            'Curaçao' => 'cw',
            'Curacao' => 'cw',
            'Ivory Coast' => 'ci',
            'Ecuador' => 'ec',
            'Netherlands' => 'nl',
            'Japan' => 'jp',
            'Sweden' => 'se',
            'Tunisia' => 'tn',
            'Belgium' => 'be',
            'Egypt' => 'eg',
            'Iran' => 'ir',
            'New Zealand' => 'nz',
            'Spain' => 'es',
            'Cape Verde' => 'cv',
            'Saudi Arabia' => 'sa',
            'Uruguay' => 'uy',
            'France' => 'fr',
            'Senegal' => 'sn',
            'Norway' => 'no',
            'Iraq' => 'iq',
            'Argentina' => 'ar',
            'Algeria' => 'dz',
            'Austria' => 'at',
            'Jordan' => 'jo',
            'Portugal' => 'pt',
            'Uzbekistan' => 'uz',
            'Colombia' => 'co',
            'DR Congo' => 'cd',
            'England' => 'gb-eng',
            'Croatia' => 'hr',
            'Ghana' => 'gh',
            'Panama' => 'pa',
        ];

        $teams = [];

        foreach ($data['matches'] as $index => $match) {
            $team1Name = trim($match['team1']);
            $team2Name = trim($match['team2']);

            $groupName = null;

            if (isset($match['group'])) {
                $groupName = str_replace('Group ', '', $match['group']);
            }

            if (!isset($teams[$team1Name])) {
                $teams[$team1Name] = Team::create([
                    'name' => $team1Name,
                    'short_name' => strtoupper(substr($team1Name, 0, 3)),
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $team1Name), 0, 5)),
                    'flag' => $flagMap[$team1Name] ?? null,
                    'group_name' => $groupName,
                ]);
            }

            if (!isset($teams[$team2Name])) {
                $teams[$team2Name] = Team::create([
                    'name' => $team2Name,
                    'short_name' => strtoupper(substr($team2Name, 0, 3)),
                    'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $team2Name), 0, 5)),
                    'flag' => $flagMap[$team2Name] ?? null,
                    'group_name' => $groupName,
                ]);
            }

            MatchGame::create([
                'api_fixture_id' => 'OF-' . ($index + 1),
                'home_team_id' => $teams[$team1Name]->id,
                'away_team_id' => $teams[$team2Name]->id,
                'match_date' => $this->parseMatchDate($match['date'], $match['time'] ?? '00:00'),
                'stage' => $this->stageName($match['round']),
                'group_name' => $groupName,
                'home_score' => null,
                'away_score' => null,
                'winner_team_id' => null,
                'is_finished' => false,
            ]);
        }

        $this->info('Sincronización completada correctamente.');
        $this->info('Partidos cargados: ' . MatchGame::count());
        $this->info('Equipos cargados: ' . Team::count());

        return self::SUCCESS;
    }

    private function parseMatchDate(string $date, string $time): Carbon
    {
        preg_match('/\d{2}:\d{2}/', $time, $matches);

        $cleanTime = $matches[0] ?? '00:00';

        return Carbon::parse($date . ' ' . $cleanTime . ':00');
    }

    private function stageName(string $round): string
    {
        $roundLower = strtolower($round);

        if (str_contains($roundLower, 'matchday')) {
            return 'Grupos';
        }

        if (str_contains($roundLower, 'round of 32')) {
            return 'Dieciseisavos';
        }

        if (str_contains($roundLower, 'round of 16')) {
            return 'Octavos';
        }

        if (str_contains($roundLower, 'quarter')) {
            return 'Cuartos';
        }

        if (str_contains($roundLower, 'semi')) {
            return 'Semifinal';
        }

        if (str_contains($roundLower, 'third')) {
            return 'Tercer lugar';
        }

        if (str_contains($roundLower, 'final')) {
            return 'Final';
        }

        return $round;
    }
}