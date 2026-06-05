<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->string('last_login_user_agent')->nullable()->after('last_login_ip');
            $table->boolean('is_online')->default(false)->after('last_login_user_agent');
            $table->timestamp('last_activity_at')->nullable()->after('is_online');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip',
                'last_login_user_agent',
                'is_online',
                'last_activity_at',
            ]);
        });
    }
};
