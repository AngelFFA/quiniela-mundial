<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bracket_matches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('round');
            $table->integer('slot');

            $table->foreignId('home_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->nullOnDelete();

            $table->integer('predicted_home_score')->nullable();
            $table->integer('predicted_away_score')->nullable();

            $table->foreignId('predicted_winner_team_id')->nullable()->constrained('teams')->nullOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'round', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bracket_matches');
    }
};