<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ONLY add if they don't exist
            if (!Schema::hasColumn('users', 'wood')) {
                $table->integer('wood')->default(120)->after('population');
            }
            if (!Schema::hasColumn('users', 'stone')) {
                $table->integer('stone')->default(80)->after('wood');
            }
            if (!Schema::hasColumn('users', 'food')) {
                $table->integer('food')->default(50)->after('stone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'wood')) $columns[] = 'wood';
            if (Schema::hasColumn('users', 'stone')) $columns[] = 'stone';
            if (Schema::hasColumn('users', 'food')) $columns[] = 'food';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
