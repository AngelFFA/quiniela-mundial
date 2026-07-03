<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'octavos_finalizados')) {
                $table->boolean('octavos_finalizados')->default(false)->after('dieciseisavos_finalizados_at');
            }
            if (!Schema::hasColumn('users', 'octavos_finalizados_at')) {
                $table->timestamp('octavos_finalizados_at')->nullable()->after('octavos_finalizados');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'octavos_finalizados_at')) $columns[] = 'octavos_finalizados_at';
            if (Schema::hasColumn('users', 'octavos_finalizados')) $columns[] = 'octavos_finalizados';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
