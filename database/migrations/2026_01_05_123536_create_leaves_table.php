<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();

            // 1. KUNCI UTAMA: Connect ke 'users' tapi nama kolomnya tetap 'employee_id'
            $table->foreignId('employee_id')
                ->constrained('employees') // <--- INI KUNCINYA (Arahkan ke tabel users)
                ->onDelete('cascade');

            // 2. Jenis Cuti pakai STRING saja biar tidak error "Data truncated" kalau ada jenis baru
            $table->string('leave_type');

            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');

            // Status pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Siapa yang approve (User juga/Admin)
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
