<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cuartos_finalizados')) {
                $table->boolean('cuartos_finalizados')->default(false)->after('octavos_finalizados_at');
            }
            if (!Schema::hasColumn('users', 'cuartos_finalizados_at')) {
                $table->timestamp('cuartos_finalizados_at')->nullable()->after('cuartos_finalizados');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'cuartos_finalizados_at')) $columns[] = 'cuartos_finalizados_at';
            if (Schema::hasColumn('users', 'cuartos_finalizados')) $columns[] = 'cuartos_finalizados';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
