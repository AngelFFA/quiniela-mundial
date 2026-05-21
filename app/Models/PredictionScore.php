<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionScore extends Model
{
    protected $fillable = [
        'prediction_id',
        'points',
        'reason'
    ];

    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}