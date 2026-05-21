<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'match_date',
        'stage',
        'group_name',
        'home_score',
        'away_score',
        'winner_team_id',
        'is_finished'
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'is_finished' => 'boolean'
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function winnerTeam()
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }
}