@extends('layouts.app')

@section('header', 'Edit Profil Karyawan: ' . $employee->full_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Perbarui Profil Karyawan</h2>
            <p class="text-sm text-slate-500">NIK: {{ $employee->nik }} &bull; TMT: {{ $employee->join_date ? $employee->join_date->format('d/m/Y') : '-' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.show', $employee) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                Kembali ke Profil
            </a>
        </div>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. IDENTITAS PERUSAHAAN & STRUKTUR ORGANISASI -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">1</span>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Identitas Perusahaan & Organisasi</h3>
                    <p class="text-xs text-slate-400">Penetapan NIK, unit kerja, jabatan, dan status ikatan kerja</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Induk Karyawan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Status Hubungan Kerja -->
                <div>
                    <label for="employment_status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Status Ikatan Kerja <span class="text-rose-500">*</span>
                    </label>
                    <select id="employment_status" name="employment_status" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="PKWT" {{ old('employment_status', $employee->employment_status) === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                        <option value="PKWTT" {{ old('employment_status', $employee->employment_status) === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                        <option value="MAGANG" {{ old('employment_status', $employee->employment_status) === 'MAGANG' ? 'selected' : '' }}>Magang / Internship</option>
                    </select>
                </div>

                <!-- Lifecycle Stage -->
                <div>
                    <label for="lifecycle_stage" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Tahapan Siklus Hidup <span class="text-rose-500">*</span>
                    </label>
                    <select id="lifecycle_stage" name="lifecycle_stage" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="ONBOARDING" {{ old('lifecycle_stage', $employee->lifecycle_stage) === 'ONBOARDING' ? 'selected' : '' }}>1. Onboarding</option>
                        <option value="ACTIVE" {{ old('lifecycle_stage', $employee->lifecycle_stage) === 'ACTIVE' ? 'selected' : '' }}>2. Active (Aktif)</option>
                        <option value="SUSPENDED" {{ old('lifecycle_stage', $employee->lifecycle_stage) === 'SUSPENDED' ? 'selected' : '' }}>3. Suspended</option>
                        <option value="OFFBOARDING" {{ old('lifecycle_stage', $employee->lifecycle_stage) === 'OFFBOARDING' ? 'selected' : '' }}>4. Offboarding</option>
                        <option value="TERMINATED" {{ old('lifecycle_stage', $employee->lifecycle_stage) === 'TERMINATED' ? 'selected' : '' }}>5. Terminated</option>
                    </select>
                </div>

                <!-- TMT Karyawan (Join Date) -->
                <div>
                    <label for="join_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        TMT Masuk (Join Date) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- No. Kontrak Awal -->
                <div>
                    <label for="current_contract_no" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        No. Kontrak Saat Ini
                    </label>
                    <input type="text" id="current_contract_no" name="current_contract_no" value="{{ old('current_contract_no', $employee->current_contract_no) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Tgl Berakhir Kontrak -->
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Tgl Berakhir Kontrak
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $employee->end_date ? $employee->end_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Lokasi Kerja -->
                <div>
                    <label for="work_location" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Lokasi Kerja / Branch
                    </label>
                    <input type="text" id="work_location" name="work_location" value="{{ old('work_location', $employee->work_location) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Departemen -->
                <div>
                    <label for="department_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Departemen / Unit Kerja
                    </label>
                    <select id="department_id" name="department_id" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Pilih Unit Kerja —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="position_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Jabatan / Posisi
                    </label>
                    <select id="position_id" name="position_id" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Pilih Jabatan —</option>
                        @foreach ($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->title }} ({{ $pos->level ?? 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Atasan Langsung (Manager) -->
                <div>
                    <label for="manager_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Atasan Langsung (Manager)
                    </label>
                    <select id="manager_id" name="manager_id" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Tidak ada (Direct Director / Top) —</option>
                        @foreach ($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id) == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->full_name }} ({{ $mgr->position?->title ?? 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. IDENTITAS PERSONAL & KTP -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">2</span>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Identitas Personal & KTP</h3>
                    <p class="text-xs text-slate-400">Usia terhitung dinamis saat ini: <strong class="text-indigo-600">{{ $employee->age ?? '-' }} Tahun</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- No KTP -->
                <div>
                    <label for="ktp_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor KTP (NIK Kependudukan) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="ktp_number" name="ktp_number" value="{{ old('ktp_number', $employee->ktp_number) }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Nama Lengkap -->
                <div class="sm:col-span-2">
                    <label for="full_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select id="gender" name="gender" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="MALE" {{ old('gender', $employee->gender) === 'MALE' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="FEMALE" {{ old('gender', $employee->gender) === 'FEMALE' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Agama -->
                <div>
                    <label for="religion" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Agama
                    </label>
                    <select id="religion" name="religion" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'] as $rel)
                            <option value="{{ $rel }}" {{ old('religion', $employee->religion) === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Pernikahan -->
                <div>
                    <label for="marital_status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Status Pernikahan
                    </label>
                    <select id="marital_status" name="marital_status" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="SINGLE" {{ old('marital_status', $employee->marital_status) === 'SINGLE' ? 'selected' : '' }}>Belum Menikah (Single)</option>
                        <option value="MARRIED" {{ old('marital_status', $employee->marital_status) === 'MARRIED' ? 'selected' : '' }}>Menikah</option>
                        <option value="DIVORCED" {{ old('marital_status', $employee->marital_status) === 'DIVORCED' ? 'selected' : '' }}>Cerai</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Alamat KTP -->
                <div>
                    <label for="ktp_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Alamat Sesuai KTP
                    </label>
                    <textarea id="ktp_address" name="ktp_address" rows="2" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('ktp_address', $employee->ktp_address) }}</textarea>
                </div>

                <!-- Alamat Domisili -->
                <div>
                    <label for="current_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Alamat Domisili Tempat Tinggal
                    </label>
                    <textarea id="current_address" name="current_address" rows="2" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('current_address', $employee->current_address) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Email Perusahaan / Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- No HP / WA -->
                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $employee->phone_number) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 3. PERSIAPAN MODUL PAYROLL, PAJAK & BPJS -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">3</span>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Data Pajak (PPh 21 TER) & Payroll Bank</h3>
                    <p class="text-xs text-slate-400">Parameter krusial untuk pemotongan pajak PPh 21 tarif efektif & transfer gaji</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Status PTKP -->
                <div>
                    <label for="ptkp_status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Status PTKP (Kunci Pajak TER) <span class="text-rose-500">*</span>
                    </label>
                    <select id="ptkp_status" name="ptkp_status" required class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <optgroup label="Tidak Kawin">
                            @foreach(['TK/0', 'TK/1', 'TK/2', 'TK/3'] as $ptkp)
                                <option value="{{ $ptkp }}" {{ old('ptkp_status', $employee->ptkp_status) === $ptkp ? 'selected' : '' }}>{{ $ptkp }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Kawin">
                            @foreach(['K/0', 'K/1', 'K/2', 'K/3'] as $ptkp)
                                <option value="{{ $ptkp }}" {{ old('ptkp_status', $employee->ptkp_status) === $ptkp ? 'selected' : '' }}>{{ $ptkp }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <!-- NPWP -->
                <div>
                    <label for="npwp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Pokok Wajib Pajak (NPWP)
                    </label>
                    <input type="text" id="npwp" name="npwp" value="{{ old('npwp', $employee->npwp) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- BPJS Ketenagakerjaan (KPJ) -->
                <div>
                    <label for="bpjs_ketenagakerjaan_no" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        No. BPJS Ketenagakerjaan (KPJ)
                    </label>
                    <input type="text" id="bpjs_ketenagakerjaan_no" name="bpjs_ketenagakerjaan_no" value="{{ old('bpjs_ketenagakerjaan_no', $employee->bpjs_ketenagakerjaan_no) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <!-- BPJS Kesehatan -->
                <div>
                    <label for="bpjs_kesehatan_no" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        No. BPJS Kesehatan
                    </label>
                    <input type="text" id="bpjs_kesehatan_no" name="bpjs_kesehatan_no" value="{{ old('bpjs_kesehatan_no', $employee->bpjs_kesehatan_no) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Nama Bank -->
                <div>
                    <label for="bank_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Bank
                    </label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- No Rekening -->
                <div>
                    <label for="bank_account_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Rekening
                    </label>
                    <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Atas Nama Rekening -->
                <div>
                    <label for="bank_account_holder" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Atas Nama Rekening
                    </label>
                    <input type="text" id="bank_account_holder" name="bank_account_holder" value="{{ old('bank_account_holder', $employee->bank_account_holder) }}" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 4. ATRIBUT DINAMIS (JSONB PostgreSQL) -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">4</span>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Atribut Tambahan Dinamis (JSONB)</h3>
                    <p class="text-xs text-slate-400">Atribut pelengkap operasional tanpa merusak skema tabel utama</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="blood_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Golongan Darah
                    </label>
                    <select id="blood_type" name="custom_fields[blood_type]" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Pilih Golongan Darah —</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $bt)
                            <option value="{{ $bt }}" {{ old('custom_fields.blood_type', $employee->custom_fields['blood_type'] ?? '') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="uniform_size" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Ukuran Seragam Kerja
                    </label>
                    <select id="uniform_size" name="custom_fields[uniform_size]" class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Pilih Ukuran —</option>
                        @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                            <option value="{{ $sz }}" {{ old('custom_fields.uniform_size', $employee->custom_fields['uniform_size'] ?? '') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-semibold text-slate-800">
                    Status Aktif Bekerja
                </label>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('employees.show', $employee) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
