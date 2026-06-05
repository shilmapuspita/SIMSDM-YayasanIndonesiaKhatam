<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // 1. Ubah 'kepala_sekolah' menjadi 'kepsek' di database
        DB::table('employees')
            ->where('jenis_pegawai', 'kepala_sekolah')
            ->update(['jenis_pegawai' => 'kepsek']);

        // 2. Change column enum dari kepala_sekolah ke kepsek
        Schema::table('employees', function (Blueprint $table) {
            // MySQL cara change enum
            $table->enum('jenis_pegawai', ['management', 'staff', 'guru', 'kepsek', 'kepala_divisi'])
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // Ubah kembali 'kepsek' menjadi 'kepala_sekolah'
        DB::table('employees')
            ->where('jenis_pegawai', 'kepsek')
            ->update(['jenis_pegawai' => 'kepala_sekolah']);

        // Change column enum kembali ke kepala_sekolah
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('jenis_pegawai', ['management', 'staff', 'guru', 'kepala_sekolah', 'kepala_divisi'])
                ->change();
        });
    }
};
