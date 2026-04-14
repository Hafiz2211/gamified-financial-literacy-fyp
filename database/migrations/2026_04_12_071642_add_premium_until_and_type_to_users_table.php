<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add the columns that don't exist yet
            if (!Schema::hasColumn('users', 'premium_until')) {
                $table->timestamp('premium_until')->nullable()->after('is_premium');
            }
            if (!Schema::hasColumn('users', 'subscription_type')) {
                $table->string('subscription_type')->nullable()->after('premium_until');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['premium_until', 'subscription_type']);
        });
    }
};