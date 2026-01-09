<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('employee_code')->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('position');
            $table->enum('employment_status', ['permanent', 'contract']);
            $table->date('join_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};