<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'semifinales_finalizados')) {
                $table->boolean('semifinales_finalizados')->default(false)->after('cuartos_finalizados_at');
            }
            if (!Schema::hasColumn('users', 'semifinales_finalizados_at')) {
                $table->timestamp('semifinales_finalizados_at')->nullable()->after('semifinales_finalizados');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'semifinales_finalizados_at')) $columns[] = 'semifinales_finalizados_at';
            if (Schema::hasColumn('users', 'semifinales_finalizados')) $columns[] = 'semifinales_finalizados';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
