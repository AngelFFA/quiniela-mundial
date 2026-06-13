<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Quinielas finalizadas - Mundial 2026</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fff; color: #111827; font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
        .top-actions { position: sticky; top: 0; z-index: 10; display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 20px; background: #0f172a; color: #fff; }
        .top-actions button { border: 0; border-radius: 10px; background: #2563eb; color: #fff; padding: 10px 16px; font-weight: 800; cursor: pointer; }
        .document { width: 100%; max-width: 980px; margin: 0 auto; padding: 24px; }
        .participant-page { page-break-after: always; padding-bottom: 24px; }
        .participant-page:last-child { page-break-after: auto; }
        .header { border-bottom: 3px solid #111827; padding-bottom: 12px; margin-bottom: 18px; }
        .title { margin: 0; font-size: 25px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
        .participant-name { margin: 8px 0 0; font-size: 20px; font-weight: 900; }
        .group-block { break-inside: avoid; page-break-inside: avoid; margin-bottom: 16px; }
        .group-title { margin: 0 0 7px; padding: 8px 10px; background: #111827; color: #fff; font-size: 14px; font-weight: 900; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; color: #111827; padding: 7px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { border: 1px solid #d1d5db; padding: 7px; vertical-align: middle; }
        .score { width: 110px; text-align: center; font-size: 14px; font-weight: 900; white-space: nowrap; }
        .footer-note { margin-top: 14px; color: #6b7280; font-size: 10px; font-weight: 700; }
        @media print {
            .top-actions { display: none; }
            .document { max-width: none; padding: 0; }
            @page { size: letter; margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="top-actions">
        <div><strong>Quinielas finalizadas</strong><span> — Se imprimen todos los participantes finalizados.</span></div>
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <main class="document">
        @foreach($users as $user)
            @php
                $userPredictions = $predictions->get($user->id, collect())->keyBy('match_game_id');
            @endphp

            <section class="participant-page">
                <div class="header">
                    <h1 class="title">Quiniela Mundial 2026</h1>
                    <p class="participant-name">{{ $user->name }}</p>
                </div>

                @foreach($matches->groupBy('group_name') as $groupName => $groupMatches)
                    <div class="group-block">
                        <h2 class="group-title">Grupo {{ $groupName }}</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Partido</th>
                                    <th style="width: 110px; text-align: center;">Pronóstico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupMatches as $match)
                                    @php $prediction = $userPredictions->get($match->id); @endphp
                                    <tr>
                                        <td>{{ $match->homeTeam?->name ?? 'Equipo 1' }} vs {{ $match->awayTeam?->name ?? 'Equipo 2' }}</td>
                                        <td class="score">
                                            @if($prediction && $prediction->predicted_home_score !== null && $prediction->predicted_away_score !== null)
                                                {{ $prediction->predicted_home_score }} - {{ $prediction->predicted_away_score }}
                                            @else
                                                Sin pronóstico
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

                <p class="footer-note">Documento de respaldo de los pronósticos registrados por el participante.</p>
            </section>
        @endforeach
    </main>
</body>
</html>
