<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');

            // Clock In
            $table->dateTime('clock_in')->nullable();
            $table->decimal('clock_in_lat', 10, 8)->nullable();
            $table->decimal('clock_in_lng', 11, 8)->nullable();
            $table->decimal('clock_in_distance_meters', 8, 2)->nullable();
            $table->string('clock_in_photo_path')->nullable();

            // Clock Out
            $table->dateTime('clock_out')->nullable();
            $table->decimal('clock_out_lat', 10, 8)->nullable();
            $table->decimal('clock_out_lng', 11, 8)->nullable();
            $table->decimal('clock_out_distance_meters', 8, 2)->nullable();
            $table->string('clock_out_photo_path')->nullable();

            // Status & Kalkulasi
            $table->enum('status', ['PRESENT', 'LATE', 'EARLY_LEAVE', 'ABSENT', 'LEAVE'])->default('PRESENT');
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('total_work_minutes')->default(0);

            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
