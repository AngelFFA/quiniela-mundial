<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'quiniela_finalizada' => 'boolean',
            'quiniela_finalizada_at' => 'datetime',
            'dieciseisavos_finalizados' => 'boolean',
            'dieciseisavos_finalizados_at' => 'datetime',
            'octavos_finalizados' => 'boolean',
            'octavos_finalizados_at' => 'datetime',
            'cuartos_finalizados' => 'boolean',
            'cuartos_finalizados_at' => 'datetime',
            'semifinales_finalizados' => 'boolean',
            'semifinales_finalizados_at' => 'datetime',
        ];
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }
}