<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('quiniela_finalizada')->default(false)->after('remember_token');
            $table->timestamp('quiniela_finalizada_at')->nullable()->after('quiniela_finalizada');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'quiniela_finalizada',
                'quiniela_finalizada_at',
            ]);
        });
    }
};