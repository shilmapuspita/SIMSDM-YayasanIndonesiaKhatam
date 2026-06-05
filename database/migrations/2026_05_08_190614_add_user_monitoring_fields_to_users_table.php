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
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable();
            }

            if (!Schema::hasColumn('users', 'last_login_user_agent')) {
                $table->text('last_login_user_agent')->nullable();
            }

            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false);
            }

            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('users', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }
            if (Schema::hasColumn('users', 'last_login_user_agent')) {
                $table->dropColumn('last_login_user_agent');
            }
            if (Schema::hasColumn('users', 'is_online')) {
                $table->dropColumn('is_online');
            }
            if (Schema::hasColumn('users', 'last_activity_at')) {
                $table->dropColumn('last_activity_at');
            }
        });
    }
};
