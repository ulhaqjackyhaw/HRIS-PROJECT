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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();

            // 1. Identitas Perusahaan
            $table->string('nik', 50)->unique();
            $table->string('employment_status', 30)->default('PKWT'); // PKWT, PKWTT, MAGANG
            $table->date('join_date'); // TMT Karyawan
            $table->date('end_date')->nullable(); // Tgl berakhir kontrak jika PKWT
            $table->string('current_contract_no', 100)->nullable(); // NO KONTRAK
            $table->string('work_location', 100)->nullable(); // LOKASI KERJA (HO, Cabang, Site)
            $table->boolean('is_active')->default(true);

            // 2. Identitas Personal & KTP
            $table->string('ktp_number', 20)->unique(); // NO KTP
            $table->string('full_name', 150); // NAMA
            $table->enum('gender', ['MALE', 'FEMALE']); // JENIS KELAMIN
            $table->date('birth_date'); // TANGGAL LAHIR (Usia dihitung dinamis)
            $table->string('religion', 30)->nullable(); // AGAMA
            $table->string('marital_status', 30)->default('SINGLE'); // STATUS PERNIKAHAN
            $table->text('ktp_address')->nullable(); // ALAMAT KTP
            $table->text('current_address')->nullable(); // Alamat Domisili jika beda dengan KTP

            // 3. Kontak
            $table->string('email')->unique();
            $table->string('phone_number', 25)->nullable();

            // 4. Pajak & Penggajian (Persiapan Payroll)
            $table->string('npwp', 30)->nullable();
            $table->string('ptkp_status', 10)->default('TK/0'); // TK/0, K/1, dst.
            $table->string('bpjs_ketenagakerjaan_no', 50)->nullable();
            $table->string('bpjs_kesehatan_no', 50)->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 150)->nullable();

            // 5. Fleksibilitas Atribut Tambahan (JSONB PostgreSQL)
            $table->jsonb('custom_fields')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
