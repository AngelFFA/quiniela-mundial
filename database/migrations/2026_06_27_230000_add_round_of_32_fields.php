<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'dieciseisavos_finalizados')) {
                $table->boolean('dieciseisavos_finalizados')->default(false)->after('quiniela_finalizada_at');
            }
            if (!Schema::hasColumn('users', 'dieciseisavos_finalizados_at')) {
                $table->timestamp('dieciseisavos_finalizados_at')->nullable()->after('dieciseisavos_finalizados');
            }
        });

        Schema::table('match_games', function (Blueprint $table) {
            if (!Schema::hasColumn('match_games', 'bracket_slot')) {
                $table->unsignedSmallInteger('bracket_slot')->nullable()->after('group_name');
                $table->index(['stage', 'bracket_slot'], 'match_games_stage_slot_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            if (Schema::hasColumn('match_games', 'bracket_slot')) {
                $table->dropIndex('match_games_stage_slot_index');
                $table->dropColumn('bracket_slot');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'dieciseisavos_finalizados_at')) $columns[] = 'dieciseisavos_finalizados_at';
            if (Schema::hasColumn('users', 'dieciseisavos_finalizados')) $columns[] = 'dieciseisavos_finalizados';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
