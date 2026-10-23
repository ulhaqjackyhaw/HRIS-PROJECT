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
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // I. Data Diri
            $table->string('full_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('gender', 20)->nullable(); // Laki-laki, Perempuan
            $table->string('marital_status', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('nationality', 50)->default('Indonesia');
            $table->string('ethnicity', 50)->nullable();
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->string('clothing_size', 10)->nullable();
            $table->string('shoe_size', 10)->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('blood_rhesus', 20)->nullable();
            $table->text('hobbies')->nullable();
            $table->text('medical_history')->nullable();

            // Upload Dokumen
            $table->string('photo_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('certificate_path')->nullable(); // Ijazah
            $table->string('transcript_path')->nullable();  // Transkrip

            // II. Identitas Diri
            $table->string('ktp_number', 30)->nullable();
            $table->date('ktp_expiry')->nullable();
            $table->string('npwp_number', 30)->nullable();
            $table->date('npwp_expiry')->nullable();
            $table->string('passport_number', 30)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('bpjs_tk_number', 30)->nullable();
            $table->date('bpjs_tk_expiry')->nullable();
            $table->string('marriage_cert_number', 50)->nullable();
            $table->date('marriage_cert_expiry')->nullable();
            $table->string('family_card_number', 30)->nullable(); // No KK
            $table->string('sim_a_number', 30)->nullable();
            $table->date('sim_a_expiry')->nullable();
            $table->string('sim_c_number', 30)->nullable();
            $table->date('sim_c_expiry')->nullable();
            $table->jsonb('vehicles_data')->nullable(); // Mobil & Motor

            // III. Kontak & Alamat
            $table->string('phone_wa', 30)->nullable();
            $table->string('email')->nullable();
            // Alamat KTP
            $table->text('ktp_address')->nullable();
            $table->string('ktp_province')->nullable();
            $table->string('ktp_city')->nullable();
            $table->string('ktp_home_phone', 30)->nullable();
            $table->string('ktp_housing_status', 50)->nullable();
            // Alamat Domisili
            $table->text('domicile_address')->nullable();
            $table->string('domicile_province')->nullable();
            $table->string('domicile_city')->nullable();
            $table->string('domicile_phone', 30)->nullable();
            $table->string('domicile_housing_status', 50)->nullable();
            // Kontak Darurat
            $table->jsonb('emergency_contact')->nullable();

            // IV. Data Keluarga (JSONB)
            $table->jsonb('family_father')->nullable();
            $table->jsonb('family_mother')->nullable();
            $table->jsonb('family_siblings')->nullable(); // Saudara kandung
            $table->jsonb('family_core')->nullable();     // Pasangan & Anak

            // V. Pendidikan & Keterampilan (JSONB)
            $table->jsonb('education_formal')->nullable();     // SD, SMP, SMA, D3, S1, S2, S3
            $table->jsonb('education_non_formal')->nullable(); // Kursus / Pelatihan
            $table->jsonb('organizations')->nullable();
            $table->jsonb('languages')->nullable();            // Kemampuan Bahasa

            // VI. Pengalaman Kerja (JSONB)
            $table->jsonb('work_experiences')->nullable();

            // VII. Referensi (JSONB)
            $table->jsonb('references_data')->nullable();

            // VIII. Esai & Proses Rekrutmen
            $table->text('strengths_weaknesses')->nullable();
            $table->text('proudest_achievement')->nullable();
            $table->boolean('applied_before')->default(false);
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->boolean('willing_to_relocate')->default(false);
            $table->string('estimated_start_date')->nullable();
            $table->text('preparation_notes')->nullable();
            $table->string('recruitment_location')->nullable();
            $table->boolean('agreement_signed')->default(false);

            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
    }
};
