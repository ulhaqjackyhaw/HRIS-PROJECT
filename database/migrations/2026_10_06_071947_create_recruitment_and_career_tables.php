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
        // 1. Job Postings / Lowongan Kerja
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('employment_type', 30)->default('FULL_TIME'); // FULL_TIME, CONTRACT, INTERNSHIP, PART_TIME
            $table->string('work_model', 30)->default('ON_SITE'); // ON_SITE, HYBRID, REMOTE
            $table->string('location')->default('Jakarta HQ');
            $table->string('experience_level')->default('Mid Level'); // Entry Level, Mid Level, Senior / Lead, Managerial
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->string('salary_currency', 10)->default('IDR');
            $table->boolean('is_salary_visible')->default(false);
            $table->text('description');
            $table->text('requirements');
            $table->text('benefits')->nullable();
            $table->integer('quota')->default(1);
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('PUBLISHED'); // DRAFT, PUBLISHED, CLOSED
            $table->unsignedInteger('views_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Job Applications / Lamaran Kerja (8-Stage Pipeline)
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone', 30);
            $table->string('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('current_stage', 30)->default('APPLIED');
            // APPLIED, SHORTLISTED, PSYCHOTEST_PASSED, INTERVIEW_HR, INTERVIEW_USER, INTERVIEW_BOD, MCU, OFFERING, HIRED, REJECTED
            $table->text('stage_notes')->nullable();
            $table->dateTime('interview_scheduled_at')->nullable();
            $table->string('interview_location')->nullable();
            $table->string('mcu_document_path')->nullable();
            $table->string('offering_letter_path')->nullable();
            $table->decimal('offering_salary', 15, 2)->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });

        // 3. Modul Psikotes Online
        Schema::create('psychotests', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: "Tes Logika Penalaran & Kepribadian DISC"
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('passing_score')->default(70);
            $table->jsonb('questions_data'); // Daftar soal, opsi jawaban, dan kunci (JSONB PostgreSQL)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Hasil Psikotes Pelamar
        Schema::create('candidate_psychotest_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('psychotest_id')->constrained('psychotests')->cascadeOnDelete();
            $table->jsonb('answers_submitted')->nullable(); // Jawaban pelamar
            $table->integer('total_score')->default(0);      // Nilai akhir (misal: 85)
            $table->string('result_status', 20)->default('PENDING'); // PENDING, PASSED, FAILED
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Log Riwayat Komunikasi (WhatsApp & Email)
        Schema::create('application_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('employees')->nullOnDelete(); // HR yang kirim
            $table->string('channel', 20); // WHATSAPP, EMAIL
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('recipient'); // Nomor HP / Alamat Email
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_communications');
        Schema::dropIfExists('candidate_psychotest_results');
        Schema::dropIfExists('psychotests');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
