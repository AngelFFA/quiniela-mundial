<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupStanding extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'group_name',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points',
        'position',
        'qualified',
        'qualification_type',
    ];

    protected $casts = [
        'qualified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}