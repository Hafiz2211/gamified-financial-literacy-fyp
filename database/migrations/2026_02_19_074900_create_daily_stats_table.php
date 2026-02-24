<?php
// database/migrations/2024_xx_xx_xxxxxx_create_daily_stats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('transaction_count')->default(0);
            $table->integer('xp_earned_today')->default(0);
            $table->integer('coins_earned_today')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_stats');
    }
};