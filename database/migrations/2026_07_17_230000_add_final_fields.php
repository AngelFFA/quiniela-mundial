<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'final_finalizada')) {
                $table->boolean('final_finalizada')->default(false)->after('semifinales_finalizados_at');
            }
            if (!Schema::hasColumn('users', 'final_finalizada_at')) {
                $table->timestamp('final_finalizada_at')->nullable()->after('final_finalizada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'final_finalizada_at')) $columns[] = 'final_finalizada_at';
            if (Schema::hasColumn('users', 'final_finalizada')) $columns[] = 'final_finalizada';
            if ($columns) $table->dropColumn($columns);
        });
    }
};
