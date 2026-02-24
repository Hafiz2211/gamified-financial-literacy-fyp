// database/migrations/2024_01_01_000003_create_user_quiz_attempts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->integer('score')->nullable(); // Percentage score
            $table->boolean('passed')->default(false);
            $table->boolean('reward_claimed')->default(false);
            $table->integer('xp_earned')->default(0);
            $table->integer('coins_earned')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of user, quiz, and attempt
            $table->unique(['user_id', 'quiz_id', 'attempt_number'], 'user_quiz_attempt_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_quiz_attempts');
    }
};