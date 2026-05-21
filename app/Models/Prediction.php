<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'user_id',
        'match_game_id',
        'predicted_home_score',
        'predicted_away_score',
        'predicted_winner_team_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matchGame()
    {
        return $this->belongsTo(MatchGame::class);
    }

    public function predictedWinner()
    {
        return $this->belongsTo(Team::class, 'predicted_winner_team_id');
    }

    public function score()
    {
        return $this->hasOne(PredictionScore::class);
    }
}