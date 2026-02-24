<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set default values for existing columns
        DB::statement('ALTER TABLE users MODIFY level INT DEFAULT 1');
        DB::statement('ALTER TABLE users MODIFY xp INT DEFAULT 0');
        DB::statement('ALTER TABLE users MODIFY coins INT DEFAULT 0');
        DB::statement('ALTER TABLE users MODIFY town_level INT DEFAULT 1');
        DB::statement('ALTER TABLE users MODIFY population INT DEFAULT 100');
        
        // Update any existing null values to defaults
        DB::table('users')->whereNull('level')->update(['level' => 1]);
        DB::table('users')->whereNull('xp')->update(['xp' => 0]);
        DB::table('users')->whereNull('coins')->update(['coins' => 0]);
        DB::table('users')->whereNull('town_level')->update(['town_level' => 1]);
        DB::table('users')->whereNull('population')->update(['population' => 100]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove defaults (optional)
        DB::statement('ALTER TABLE users MODIFY level INT');
        DB::statement('ALTER TABLE users MODIFY xp INT');
        DB::statement('ALTER TABLE users MODIFY coins INT');
        DB::statement('ALTER TABLE users MODIFY town_level INT');
        DB::statement('ALTER TABLE users MODIFY population INT');
    }
};