<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('activity');
        });

        if (Schema::hasTable('user_activity_logs')) {
            DB::table('activity_logs')->insertUsing(
                ['user_id', 'activity', 'description', 'metadata', 'ip_address', 'user_agent', 'created_at', 'updated_at'],
                DB::table('user_activity_logs')->select('user_id', 'action', 'description', 'metadata', 'ip_address', 'user_agent', 'created_at', 'updated_at')
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
