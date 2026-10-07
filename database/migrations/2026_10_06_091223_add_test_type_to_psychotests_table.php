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
        Schema::table('psychotests', function (Blueprint $table) {
            $table->string('test_type', 30)->default('GENERAL')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psychotests', function (Blueprint $table) {
            $table->dropColumn('test_type');
        });
    }
};
