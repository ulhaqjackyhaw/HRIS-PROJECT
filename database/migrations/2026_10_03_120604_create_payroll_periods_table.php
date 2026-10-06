<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payout_date');
            $table->enum('status', ['DRAFT', 'PROCESSING', 'APPROVED', 'PAID'])->default('DRAFT');
            $table->decimal('total_gross_amount', 18, 2)->default(0);
            $table->decimal('total_deduction_amount', 18, 2)->default(0);
            $table->decimal('total_net_amount', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
