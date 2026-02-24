<?php
// database/migrations/2024_xx_xx_xxxxxx_create_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('category');
            $table->date('date');
            $table->text('note')->nullable();
            $table->string('photo_path')->nullable();
            $table->integer('xp_earned')->default(0);
            $table->integer('coins_earned')->default(0);
            $table->integer('daily_count')->default(0);
            $table->boolean('full_reward')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};