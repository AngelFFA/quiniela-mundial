<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\PredictionScore;
use App\Models\Team;
use App\Models\UserBracketMatch;
use App\Models\UserGroupStanding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldCupTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        UserBracketMatch::truncate();
        UserGroupStanding::truncate();
        PredictionScore::truncate();
        Prediction::truncate();
        MatchGame::truncate();
        Team::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $groups = [
            'A' => [
                ['name' => 'México', 'code' => 'MEX', 'flag' => 'mx'],
                ['name' => 'Sudáfrica', 'code' => 'RSA', 'flag' => 'za'],
                ['name' => 'Corea del Sur', 'code' => 'KOR', 'flag' => 'kr'],
                ['name' => 'Czechia', 'code' => 'CZE', 'flag' => 'cz'],
            ],
            'B' => [
                ['name' => 'Canadá', 'code' => 'CAN', 'flag' => 'ca'],
                ['name' => 'Bosnia y Herzegovina', 'code' => 'BIH', 'flag' => 'ba'],
                ['name' => 'Qatar', 'code' => 'QAT', 'flag' => 'qa'],
                ['name' => 'Suiza', 'code' => 'SUI', 'flag' => 'ch'],
            ],
            'C' => [
                ['name' => 'Brasil', 'code' => 'BRA', 'flag' => 'br'],
                ['name' => 'Marruecos', 'code' => 'MAR', 'flag' => 'ma'],
                ['name' => 'Haití', 'code' => 'HAI', 'flag' => 'ht'],
                ['name' => 'Escocia', 'code' => 'SCO', 'flag' => 'gb-sct'],
            ],
            'D' => [
                ['name' => 'Estados Unidos', 'code' => 'USA', 'flag' => 'us'],
                ['name' => 'Paraguay', 'code' => 'PAR', 'flag' => 'py'],
                ['name' => 'Australia', 'code' => 'AUS', 'flag' => 'au'],
                ['name' => 'Türkiye', 'code' => 'TUR', 'flag' => 'tr'],
            ],
            'E' => [
                ['name' => 'Alemania', 'code' => 'GER', 'flag' => 'de'],
                ['name' => 'Curazao', 'code' => 'CUW', 'flag' => 'cw'],
                ['name' => 'Costa de Marfil', 'code' => 'CIV', 'flag' => 'ci'],
                ['name' => 'Ecuador', 'code' => 'ECU', 'flag' => 'ec'],
            ],
            'F' => [
                ['name' => 'Países Bajos', 'code' => 'NED', 'flag' => 'nl'],
                ['name' => 'Japón', 'code' => 'JPN', 'flag' => 'jp'],
                ['name' => 'Túnez', 'code' => 'TUN', 'flag' => 'tn'],
                ['name' => 'Suecia', 'code' => 'SWE', 'flag' => 'se'],
            ],
            'G' => [
                ['name' => 'Bélgica', 'code' => 'BEL', 'flag' => 'be'],
                ['name' => 'Egipto', 'code' => 'EGY', 'flag' => 'eg'],
                ['name' => 'Irán', 'code' => 'IRN', 'flag' => 'ir'],
                ['name' => 'Nueva Zelanda', 'code' => 'NZL', 'flag' => 'nz'],
            ],
            'H' => [
                ['name' => 'España', 'code' => 'ESP', 'flag' => 'es'],
                ['name' => 'Cabo Verde', 'code' => 'CPV', 'flag' => 'cv'],
                ['name' => 'Arabia Saudita', 'code' => 'KSA', 'flag' => 'sa'],
                ['name' => 'Uruguay', 'code' => 'URU', 'flag' => 'uy'],
            ],
            'I' => [
                ['name' => 'Francia', 'code' => 'FRA', 'flag' => 'fr'],
                ['name' => 'Senegal', 'code' => 'SEN', 'flag' => 'sn'],
                ['name' => 'Noruega', 'code' => 'NOR', 'flag' => 'no'],
                ['name' => 'Irak', 'code' => 'IRQ', 'flag' => 'iq'],
            ],
            'J' => [
                ['name' => 'Argentina', 'code' => 'ARG', 'flag' => 'ar'],
                ['name' => 'Argelia', 'code' => 'ALG', 'flag' => 'dz'],
                ['name' => 'Austria', 'code' => 'AUT', 'flag' => 'at'],
                ['name' => 'Jordania', 'code' => 'JOR', 'flag' => 'jo'],
            ],
            'K' => [
                ['name' => 'Portugal', 'code' => 'POR', 'flag' => 'pt'],
                ['name' => 'Uzbekistán', 'code' => 'UZB', 'flag' => 'uz'],
                ['name' => 'Colombia', 'code' => 'COL', 'flag' => 'co'],
                ['name' => 'RD Congo', 'code' => 'COD', 'flag' => 'cd'],
            ],
            'L' => [
                ['name' => 'Inglaterra', 'code' => 'ENG', 'flag' => 'gb-eng'],
                ['name' => 'Croacia', 'code' => 'CRO', 'flag' => 'hr'],
                ['name' => 'Ghana', 'code' => 'GHA', 'flag' => 'gh'],
                ['name' => 'Panamá', 'code' => 'PAN', 'flag' => 'pa'],
            ],
        ];

        foreach ($groups as $groupName => $teams) {
            foreach ($teams as $team) {
                Team::create([
                    'name' => $team['name'],
                    'short_name' => $team['code'],
                    'code' => $team['code'],
                    'flag' => $team['flag'],
                    'group_name' => $groupName,
                ]);
            }
        }

        $fixtures = [
            ['A', 'MEX', 'RSA', '2026-06-11 19:00:00'],
            ['A', 'KOR', 'CZE', '2026-06-11 22:00:00'],
            ['A', 'CZE', 'RSA', '2026-06-18 16:00:00'],
            ['A', 'MEX', 'KOR', '2026-06-18 19:00:00'],
            ['A', 'CZE', 'MEX', '2026-06-24 20:00:00'],
            ['A', 'RSA', 'KOR', '2026-06-24 20:00:00'],

            ['B', 'CAN', 'BIH', '2026-06-12 15:00:00'],
            ['B', 'QAT', 'SUI', '2026-06-13 15:00:00'],
            ['B', 'SUI', 'BIH', '2026-06-18 15:00:00'],
            ['B', 'CAN', 'QAT', '2026-06-18 18:00:00'],
            ['B', 'BIH', 'QAT', '2026-06-24 18:00:00'],
            ['B', 'SUI', 'CAN', '2026-06-24 18:00:00'],

            ['C', 'BRA', 'MAR', '2026-06-13 18:00:00'],
            ['C', 'HAI', 'SCO', '2026-06-14 15:00:00'],
            ['C', 'BRA', 'HAI', '2026-06-19 18:00:00'],
            ['C', 'SCO', 'MAR', '2026-06-20 15:00:00'],
            ['C', 'SCO', 'BRA', '2026-06-26 18:00:00'],
            ['C', 'MAR', 'HAI', '2026-06-26 18:00:00'],

            ['D', 'USA', 'PAR', '2026-06-12 18:00:00'],
            ['D', 'AUS', 'TUR', '2026-06-13 18:00:00'],
            ['D', 'TUR', 'PAR', '2026-06-19 18:00:00'],
            ['D', 'USA', 'AUS', '2026-06-19 21:00:00'],
            ['D', 'TUR', 'USA', '2026-06-25 20:00:00'],
            ['D', 'PAR', 'AUS', '2026-06-25 20:00:00'],

            ['E', 'GER', 'CUW', '2026-06-14 18:00:00'],
            ['E', 'CIV', 'ECU', '2026-06-15 15:00:00'],
            ['E', 'GER', 'CIV', '2026-06-20 18:00:00'],
            ['E', 'ECU', 'CUW', '2026-06-21 15:00:00'],
            ['E', 'ECU', 'GER', '2026-06-27 18:00:00'],
            ['E', 'CUW', 'CIV', '2026-06-27 18:00:00'],

            ['F', 'NED', 'JPN', '2026-06-14 21:00:00'],
            ['F', 'SWE', 'TUN', '2026-06-14 18:00:00'],
            ['F', 'NED', 'SWE', '2026-06-20 18:00:00'],
            ['F', 'TUN', 'JPN', '2026-06-20 21:00:00'],
            ['F', 'JPN', 'SWE', '2026-06-25 18:00:00'],
            ['F', 'TUN', 'NED', '2026-06-25 18:00:00'],

            ['G', 'BEL', 'EGY', '2026-06-15 18:00:00'],
            ['G', 'IRN', 'NZL', '2026-06-16 15:00:00'],
            ['G', 'BEL', 'IRN', '2026-06-21 18:00:00'],
            ['G', 'NZL', 'EGY', '2026-06-22 15:00:00'],
            ['G', 'NZL', 'BEL', '2026-06-28 18:00:00'],
            ['G', 'EGY', 'IRN', '2026-06-28 18:00:00'],

            ['H', 'ESP', 'CPV', '2026-06-15 21:00:00'],
            ['H', 'KSA', 'URU', '2026-06-16 18:00:00'],
            ['H', 'ESP', 'KSA', '2026-06-21 21:00:00'],
            ['H', 'URU', 'CPV', '2026-06-22 18:00:00'],
            ['H', 'URU', 'ESP', '2026-06-28 21:00:00'],
            ['H', 'CPV', 'KSA', '2026-06-28 21:00:00'],

            ['I', 'FRA', 'SEN', '2026-06-16 21:00:00'],
            ['I', 'IRQ', 'NOR', '2026-06-16 18:00:00'],
            ['I', 'FRA', 'IRQ', '2026-06-22 18:00:00'],
            ['I', 'NOR', 'SEN', '2026-06-22 21:00:00'],
            ['I', 'SEN', 'IRQ', '2026-06-26 18:00:00'],
            ['I', 'NOR', 'FRA', '2026-06-26 18:00:00'],

            ['J', 'ARG', 'ALG', '2026-06-17 15:00:00'],
            ['J', 'AUT', 'JOR', '2026-06-17 18:00:00'],
            ['J', 'ARG', 'AUT', '2026-06-23 15:00:00'],
            ['J', 'JOR', 'ALG', '2026-06-23 18:00:00'],
            ['J', 'JOR', 'ARG', '2026-06-27 21:00:00'],
            ['J', 'ALG', 'AUT', '2026-06-27 21:00:00'],

            ['K', 'POR', 'COD', '2026-06-17 18:00:00'],
            ['K', 'UZB', 'COL', '2026-06-17 21:00:00'],
            ['K', 'COL', 'COD', '2026-06-23 18:00:00'],
            ['K', 'POR', 'UZB', '2026-06-23 21:00:00'],
            ['K', 'COD', 'UZB', '2026-06-27 18:00:00'],
            ['K', 'COL', 'POR', '2026-06-27 18:00:00'],

            ['L', 'ENG', 'CRO', '2026-06-17 20:00:00'],
            ['L', 'GHA', 'PAN', '2026-06-17 23:00:00'],
            ['L', 'ENG', 'GHA', '2026-06-23 20:00:00'],
            ['L', 'PAN', 'CRO', '2026-06-23 23:00:00'],
            ['L', 'PAN', 'ENG', '2026-06-27 20:00:00'],
            ['L', 'CRO', 'GHA', '2026-06-27 20:00:00'],
        ];

        foreach ($fixtures as $fixture) {
            [$groupName, $homeCode, $awayCode, $matchDate] = $fixture;

            $homeTeam = Team::where('code', $homeCode)->firstOrFail();
            $awayTeam = Team::where('code', $awayCode)->firstOrFail();

            MatchGame::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'match_date' => $matchDate,
                'stage' => 'Grupos',
                'group_name' => $groupName,
            ]);
        }
    }
}