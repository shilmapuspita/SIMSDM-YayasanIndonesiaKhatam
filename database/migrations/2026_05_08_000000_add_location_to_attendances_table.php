<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('check_in_latitude', 10, 8)->nullable()->after('check_in')->comment('Latitude saat check-in');
            $table->decimal('check_in_longitude', 11, 8)->nullable()->after('check_in_latitude')->comment('Longitude saat check-in');
            $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_in_longitude')->comment('Jarak dari kantor saat check-in (meter)');

            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('check_out')->comment('Latitude saat check-out');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude')->comment('Longitude saat check-out');
            $table->decimal('check_out_distance', 8, 2)->nullable()->after('check_out_longitude')->comment('Jarak dari kantor saat check-out (meter)');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude',
                'check_in_longitude',
                'check_in_distance',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_distance',
            ]);
        });
    }
};
