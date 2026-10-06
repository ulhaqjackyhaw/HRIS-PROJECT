<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->nullable()->constrained('payroll_periods')->nullOnDelete();
            $table->string('title', 150);
            $table->decimal('amount', 15, 2);
            $table->date('claim_date');
            $table->string('receipt_photo_path')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'DISBURSED'])->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_claims');
    }
};
