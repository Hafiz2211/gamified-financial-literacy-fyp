<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false);
            $table->timestamp('premium_until')->nullable(); // For subscription expiry
            $table->string('subscription_type')->nullable(); // 'monthly' or 'yearly'
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'premium_until', 'subscription_type']);
        });
    }
};