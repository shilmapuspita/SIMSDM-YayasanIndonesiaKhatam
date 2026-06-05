<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('jenis_pegawai', ['management', 'staff', 'guru', 'kepala_sekolah', 'kepala_divisi'])
                ->default('staff')
                ->after('employment_status')
                ->comment('Jenis/kategori pegawai untuk menentukan aturan jam kerja absensi');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('jenis_pegawai');
        });
    }
};
