// database/migrations/2024_01_01_000001_create_quizzes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('level_required')->default(1); // User level required
            $table->integer('order')->default(0); // 1,2,3 for Beginner, Intermediate, Advanced
            $table->integer('passing_score')->default(70); // 70%
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
};