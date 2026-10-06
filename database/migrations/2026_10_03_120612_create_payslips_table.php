<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // Rekap Presensi Periode Ini (Terekam permanen untuk bukti audit)
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('unpaid_leave_days')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);

            // Hasil Finansial
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('total_reimbursements', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);

            $table->enum('status', ['DRAFT', 'PUBLISHED'])->default('DRAFT');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
