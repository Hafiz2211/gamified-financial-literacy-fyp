<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the missing fields
            $table->integer('level')->default(1)->after('remember_token');
            $table->integer('xp')->default(0)->after('level');
            $table->integer('coins')->default(0)->after('xp');
            $table->integer('town_level')->default(1)->after('coins');
            $table->integer('population')->default(100)->after('town_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level', 'xp', 'coins', 'town_level', 'population']);
        });
    }
};