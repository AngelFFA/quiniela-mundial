<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('match_game_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('predicted_home_score');
            $table->integer('predicted_away_score');

            $table->foreignId('predicted_winner_team_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'match_game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};