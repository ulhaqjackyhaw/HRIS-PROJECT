<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('annual_leave_quota')->default(12)->after('is_active');
            $table->integer('annual_leave_used')->default(0)->after('annual_leave_quota');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['annual_leave_quota', 'annual_leave_used']);
        });
    }
};
