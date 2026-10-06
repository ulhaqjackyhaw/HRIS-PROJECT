<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('fixed_allowance', 15, 2)->default(0);
            $table->decimal('daily_transport_allowance', 15, 2)->default(0);
            $table->decimal('daily_meal_allowance', 15, 2)->default(0);
            $table->boolean('use_bpjs_tk')->default(true);
            $table->boolean('use_bpjs_kes')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
