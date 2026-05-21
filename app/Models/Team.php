<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'code',
        'flag',
        'group_name'
    ];

    public function homeMatches()
    {
        return $this->hasMany(MatchGame::class, 'home_team_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(MatchGame::class, 'away_team_id');
    }
}