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
                ['name' => 'Turquía', 'code' => 'TUR', 'flag' => 'tr'],
            ],
            'B' => [
                ['name' => 'Canadá', 'code' => 'CAN', 'flag' => 'ca'],
                ['name' => 'Qatar', 'code' => 'QAT', 'flag' => 'qa'],
                ['name' => 'Bosnia y Herzegovina', 'code' => 'BIH', 'flag' => 'ba'],
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
                ['name' => 'Italia', 'code' => 'ITA', 'flag' => 'it'],
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

        foreach ($groups as $groupName => $teamsGroup) {
            $teams = Team::where('group_name', $groupName)->get()->values();

            $pairings = [
                [0, 1],
                [2, 3],
                [0, 2],
                [3, 1],
                [3, 0],
                [1, 2],
            ];

            foreach ($pairings as $index => $pairing) {
                MatchGame::create([
                    'home_team_id' => $teams[$pairing[0]]->id,
                    'away_team_id' => $teams[$pairing[1]]->id,
                    'match_date' => now()->addDays((ord($groupName) - 64) * 3 + $index),
                    'stage' => 'Grupos',
                    'group_name' => $groupName,
                ]);
            }
        }
    }
}