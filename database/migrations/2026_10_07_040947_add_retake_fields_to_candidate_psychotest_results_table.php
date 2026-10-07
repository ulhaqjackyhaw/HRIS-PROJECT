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
        Schema::table('candidate_psychotest_results', function (Blueprint $table) {
            $table->boolean('can_retake')->default(false)->after('result_status');
            $table->text('retake_reason')->nullable()->after('can_retake');
            $table->timestamp('retake_granted_at')->nullable()->after('retake_reason');
            $table->foreignId('retake_granted_by')->nullable()->after('retake_granted_at')->constrained('users')->nullOnDelete();
            $table->integer('attempt_number')->default(1)->after('retake_granted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_psychotest_results', function (Blueprint $table) {
            $table->dropForeign(['retake_granted_by']);
            $table->dropColumn([
                'can_retake',
                'retake_reason',
                'retake_granted_at',
                'retake_granted_by',
                'attempt_number',
            ]);
        });
    }
};
