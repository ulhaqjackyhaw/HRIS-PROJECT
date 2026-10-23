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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_holiday_work')->default(false)->after('status');
            $table->foreignId('holiday_id')->nullable()->after('is_holiday_work')->constrained('holidays')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['holiday_id']);
            $table->dropColumn(['is_holiday_work', 'holiday_id']);
        });
    }
};
