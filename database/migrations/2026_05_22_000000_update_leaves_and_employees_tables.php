<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (!Schema::hasColumn('leaves', 'attachment')) {
                    $table->string('attachment')->nullable();
                }

                if (!Schema::hasColumn('leaves', 'jumlah_hari')) {
                    $table->integer('jumlah_hari')->nullable();
                }
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'annual_leave_balance')) {
                    $table->integer('annual_leave_balance')->default(12);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn('leaves', 'attachment')) {
                    $table->dropColumn('attachment');
                }

                if (Schema::hasColumn('leaves', 'jumlah_hari')) {
                    $table->dropColumn('jumlah_hari');
                }
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'annual_leave_balance')) {
                    $table->dropColumn('annual_leave_balance');
                }
            });
        }
    }
};
