<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BuildWorldCupThirdPlaceTable extends Command
{
    protected $signature = 'worldcup:third-table';

    protected $description = 'Construye la tabla completa de combinaciones de terceros del Mundial 2026';

    public function handle(): int
    {
        $this->info('Descargando tabla de combinaciones...');

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->timeout(60)->get(
            'https://en.wikipedia.org/wiki/Template:2026_FIFA_World_Cup_third-place_table'
        );

        if (!$response->successful()) {
            $this->error('No se pudo descargar la tabla.');
            return self::FAILURE;
        }

        $html = $response->body();

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//tr');

        $table = [];

        foreach ($rows as $row) {
            $text = trim(preg_replace('/\s+/', ' ', $row->textContent));

            if (!preg_match('/^\d+\s+([A-L](?:\s+[A-L]){7})\s+(3[A-L](?:\s+3[A-L]){7})$/', $text, $matches)) {
                continue;
            }

            $groups = explode(' ', $matches[1]);
            $assignments = explode(' ', $matches[2]);

            sort($groups);

            $key = implode('', $groups);

            $table[$key] = [
                '1A' => substr($assignments[0], 1),
                '1B' => substr($assignments[1], 1),
                '1D' => substr($assignments[2], 1),
                '1E' => substr($assignments[3], 1),
                '1G' => substr($assignments[4], 1),
                '1I' => substr($assignments[5], 1),
                '1K' => substr($assignments[6], 1),
                '1L' => substr($assignments[7], 1),
            ];
        }

        if (count($table) < 400) {
            $this->error('La tabla no se procesó completa. Filas detectadas: ' . count($table));
            return self::FAILURE;
        }

        ksort($table);

        $content = "<?php\n\nreturn " . var_export($table, true) . ";\n";

        file_put_contents(config_path('worldcup_third_place.php'), $content);

        $this->info('Tabla creada correctamente.');
        $this->info('Combinaciones cargadas: ' . count($table));

        return self::SUCCESS;
    }
}