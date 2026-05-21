<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prediction_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('points')->default(0);

            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_scores');
    }
};