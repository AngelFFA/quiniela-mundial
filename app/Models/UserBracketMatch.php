<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBracketMatch extends Model
{
    protected $fillable = [
        'user_id',
        'round',
        'slot',
        'home_team_id',
        'away_team_id',
        'predicted_home_score',
        'predicted_away_score',
        'predicted_winner_team_id',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function predictedWinnerTeam()
    {
        return $this->belongsTo(Team::class, 'predicted_winner_team_id');
    }
}