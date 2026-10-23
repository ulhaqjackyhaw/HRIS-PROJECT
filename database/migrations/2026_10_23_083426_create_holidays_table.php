<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->date('date')->index();
            $table->string('type', 30)->default('NATIONAL'); // NATIONAL, MASS_LEAVE, COMPANY, REGIONAL
            $table->foreignId('office_location_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('reduces_annual_leave')->default(false);
            $table->boolean('is_workday_override')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
