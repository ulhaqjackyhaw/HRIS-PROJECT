<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Formulir Data Diri & Profil Pelamar - HRIS Careers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .form-section {
            scroll-margin-top: 100px;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-indigo-600 selection:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <a href="{{ route('career.dashboard') }}" class="flex items-center space-x-2.5 sm:space-x-3.5 group">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-500 flex items-center justify-center text-white font-black text-lg sm:text-xl shadow-md">
                    H
                </div>
                <div>
                    <span class="font-extrabold tracking-tight text-base sm:text-lg text-slate-900">HRIS Core</span>
                    <span class="text-[10px] sm:text-xs text-slate-500 block font-medium">Formulir Data Diri & CV</span>
                </div>
            </a>

            <div class="flex items-center space-x-4">
                <a href="{{ route('career.dashboard') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                    ← <span class="hidden sm:inline">Kembali ke </span>Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <!-- Notification / Alerts -->
        @if(request('job'))
            <div class="mb-6 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-base shrink-0">🎯</span>
                    <div>
                        <div class="text-xs font-bold text-slate-900">Melamar Posisi: <span class="text-indigo-600 font-extrabold">{{ request('job') }}</span></div>
                        <div class="text-[11px] text-slate-500">Lengkapi data inti wajib bertanda (<span class="text-rose-500 font-bold">*</span>) di bawah ini, lalu simpan untuk langsung mengirim lamaran Anda.</div>
                    </div>
                </div>
                <a href="{{ route('career.jobs.show', request('job')) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors shrink-0">
                    ← Kembali ke Detail Lowongan
                </a>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="font-bold text-sm mb-2 text-rose-900">Harap periksa kembali isian formulir:</div>
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="mb-8 p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Intro Title & Instructions -->
        <div class="mb-8 bg-white border border-slate-200/90 shadow-sm rounded-3xl p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-2">Formulir Kelengkapan Data Pelamar Kerja</h1>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-2xl">
                        Untuk mempercepat proses lamaran, Anda hanya diwajibkan melengkapi <strong class="text-rose-400">Data Inti bertanda bintang (*)</strong> (Data Pribadi, No. WhatsApp, Alamat Domisili, dan CV). Bagian selebihnya bersifat <strong class="text-slate-700">opsional</strong> dan dapat Anda lengkapi kapan saja.
                    </p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5 shrink-0">
                    <div class="flex items-center space-x-2 text-emerald-400 font-semibold">
                        <span>✓</span>
                        <span>Data Inti (<span class="text-rose-400">*</span>) : Wajib diisi</span>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-400">
                        <span>○</span>
                        <span>Data Lainnya : Opsional (bisa dilewati)</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('career.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf
            <input type="hidden" name="redirect_job" value="{{ request('job', old('redirect_job')) }}">

            <!-- ==========================================
                 BAGIAN I: DATA DIRI & UPLOAD DOKUMEN
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold flex items-center justify-center text-sm">I</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Data Diri & Berkas Dokumen</h2>
                        <p class="text-xs text-slate-400">Lengkapi data diri inti dan unggah dokumen CV Anda.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-400 font-bold">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? $user->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Panggilan <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="text" name="nickname" value="{{ old('nickname', $profile->nickname) }}" placeholder="Contoh: Budi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Kelamin <span class="text-rose-400 font-bold">*</span></label>
                        <select name="gender" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('gender', $profile->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender', $profile->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Pernikahan <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <select name="marital_status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih Status Pernikahan --</option>
                            <option value="Belum Menikah" {{ old('marital_status', $profile->marital_status) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="Menikah" {{ old('marital_status', $profile->marital_status) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="Cerai Hidup" {{ old('marital_status', $profile->marital_status) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('marital_status', $profile->marital_status) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Lahir <span class="text-rose-400 font-bold">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $profile->birth_date?->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tempat Lahir <span class="text-rose-400 font-bold">*</span></label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $profile->birth_place) }}" required placeholder="Kota kelahiran" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Agama <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <select name="religion" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih Agama --</option>
                            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $rel)
                                <option value="{{ $rel }}" {{ old('religion', $profile->religion) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kewarganegaraan <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="text" name="nationality" value="{{ old('nationality', $profile->nationality ?? 'Indonesia') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Suku Bangsa <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="text" name="ethnicity" value="{{ old('ethnicity', $profile->ethnicity) }}" placeholder="Contoh: Jawa, Sunda, Batak" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tinggi Badan (cm) <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="number" name="height_cm" value="{{ old('height_cm', $profile->height_cm) }}" placeholder="Contoh: 170" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Berat Badan (kg) <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="number" name="weight_kg" value="{{ old('weight_kg', $profile->weight_kg) }}" placeholder="Contoh: 65" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ukuran Baju <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <select name="clothing_size" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- S / M / L / XL / XXL --</option>
                            @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $sz)
                                <option value="{{ $sz }}" {{ old('clothing_size', $profile->clothing_size) == $sz ? 'selected' : '' }}>{{ $sz }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ukuran Sepatu</label>
                        <input type="text" name="shoe_size" value="{{ old('shoe_size', $profile->shoe_size) }}" placeholder="Contoh: 41, 42" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Golongan Darah</label>
                        <select name="blood_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih --</option>
                            @foreach(['A', 'B', 'AB', 'O'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type', $profile->blood_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Resus</label>
                        <select name="blood_rhesus" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih --</option>
                            <option value="Positif (+)" {{ old('blood_rhesus', $profile->blood_rhesus) == 'Positif (+)' ? 'selected' : '' }}>Positif (+)</option>
                            <option value="Negatif (-)" {{ old('blood_rhesus', $profile->blood_rhesus) == 'Negatif (-)' ? 'selected' : '' }}>Negatif (-)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hobi & Kegemaran</label>
                        <input type="text" name="hobbies" value="{{ old('hobbies', $profile->hobbies) }}" placeholder="Membaca, olahraga, koding, fotografi..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Riwayat Medis</label>
                        <input type="text" name="medical_history" value="{{ old('medical_history', $profile->medical_history) }}" placeholder="Penyakit berat, operasi, alergi. Kosongkan jika tidak ada." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>
                </div>

                <!-- Upload Dokumen Cards -->
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-indigo-400 uppercase tracking-wider mb-4">Berkas Digital Pelamar (Maks 2 MB)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <label class="block text-xs font-bold text-slate-900 mb-1">Upload CV / Resume <span class="text-rose-400 font-bold">*</span></label>
                            <p class="text-[10px] text-slate-400 mb-3">PDF, DOC, DOCX. Maks 2 MB.</p>
                            <input type="file" name="cv" accept=".pdf,.doc,.docx" {{ !($profile->cv_path || $user->resume_path) ? 'required' : '' }} class="w-full text-xs text-slate-400 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:text-xs" />
                            @if($profile->cv_path || $user->resume_path)
                                <span class="text-[10px] text-emerald-400 block mt-2">✓ Berkas telah tersimpan</span>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <label class="block text-xs font-bold text-slate-900 mb-1">Foto Formal Diri <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                            <p class="text-[10px] text-slate-400 mb-3">JPG atau PNG, maksimal 2 MB.</p>
                            <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-700 file:text-xs" />
                            @if($profile->photo_path)
                                <span class="text-[10px] text-emerald-400 block mt-2">✓ Berkas telah tersimpan</span>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <label class="block text-xs font-bold text-slate-900 mb-1">Ijazah Terakhir <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                            <p class="text-[10px] text-slate-400 mb-3">PDF, JPG, atau PNG. Maks 2 MB.</p>
                            <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-700 file:text-xs" />
                            @if($profile->certificate_path)
                                <span class="text-[10px] text-emerald-400 block mt-2">✓ Berkas telah tersimpan</span>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <label class="block text-xs font-bold text-slate-900 mb-1">Transkrip Nilai <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                            <p class="text-[10px] text-slate-400 mb-3">PDF, JPG, atau PNG. Maks 2 MB.</p>
                            <input type="file" name="transcript" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-700 file:text-xs" />
                            @if($profile->transcript_path)
                                <span class="text-[10px] text-emerald-400 block mt-2">✓ Berkas telah tersimpan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN II: IDENTITAS DIRI & KENDARAAN (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-2 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-cyan-600/20 text-cyan-400 font-bold flex items-center justify-center text-sm">II</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Identitas Diri & Kendaraan <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Semua isian di bagian ini bersifat opsional. Kosongkan kolom yang belum ingin diisi.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 pt-4">
                    <!-- KTP -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No KTP (NIK)</label>
                        <input type="text" name="ktp_number" value="{{ old('ktp_number', $profile->ktp_number) }}" placeholder="16 digit NIK KTP" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku KTP</label>
                        <input type="date" name="ktp_expiry" value="{{ old('ktp_expiry', $profile->ktp_expiry?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>

                    <!-- NPWP -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No NPWP</label>
                        <input type="text" name="npwp_number" value="{{ old('npwp_number', $profile->npwp_number) }}" placeholder="Nomor Pokok Wajib Pajak" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku NPWP</label>
                        <input type="date" name="npwp_expiry" value="{{ old('npwp_expiry', $profile->npwp_expiry?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>

                    <!-- Passport & BPJS -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No Passport</label>
                        <input type="text" name="passport_number" value="{{ old('passport_number', $profile->passport_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku Passport</label>
                        <input type="date" name="passport_expiry" value="{{ old('passport_expiry', $profile->passport_expiry?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No BPJS Ketenagakerjaan</label>
                        <input type="text" name="bpjs_tk_number" value="{{ old('bpjs_tk_number', $profile->bpjs_tk_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No Kartu Keluarga (KK)</label>
                        <input type="text" name="family_card_number" value="{{ old('family_card_number', $profile->family_card_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>

                    <!-- SIM A & SIM C -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No SIM A</label>
                        <input type="text" name="sim_a_number" value="{{ old('sim_a_number', $profile->sim_a_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku SIM A</label>
                        <input type="date" name="sim_a_expiry" value="{{ old('sim_a_expiry', $profile->sim_a_expiry?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No SIM C</label>
                        <input type="text" name="sim_c_number" value="{{ old('sim_c_number', $profile->sim_c_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku SIM C</label>
                        <input type="date" name="sim_c_expiry" value="{{ old('sim_c_expiry', $profile->sim_c_expiry?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                </div>

                <!-- Kendaraan Pribadi -->
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-white mb-4">Kendaraan Pribadi <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Mobil -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-indigo-400 block">Mobil Pribadi</span>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" name="vehicles[car][brand]" value="{{ $profile->vehicles_data['car']['brand'] ?? '' }}" placeholder="Merek" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="vehicles[car][year]" value="{{ $profile->vehicles_data['car']['year'] ?? '' }}" placeholder="Tahun" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <select name="vehicles[car][status]" class="px-2 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="">Status</option>
                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                    <option value="Keluarga">Keluarga</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>

                        <!-- Motor -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-indigo-400 block">Sepeda Motor</span>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="text" name="vehicles[motorcycle][brand]" value="{{ $profile->vehicles_data['motorcycle']['brand'] ?? '' }}" placeholder="Merek" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="vehicles[motorcycle][year]" value="{{ $profile->vehicles_data['motorcycle']['year'] ?? '' }}" placeholder="Tahun" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <select name="vehicles[motorcycle][status]" class="px-2 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="">Status</option>
                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                    <option value="Keluarga">Keluarga</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN III: KONTAK & ALAMAT
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-emerald-600/20 text-emerald-400 font-bold flex items-center justify-center text-sm">III</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Kontak & Alamat Domisili</h2>
                        <p class="text-xs text-slate-400">Nomor WhatsApp dan Alamat Domisili wajib diisi untuk koordinasi proses seleksi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No Handphone (WhatsApp) <span class="text-rose-400 font-bold">*</span></label>
                        <input type="tel" name="phone_wa" value="{{ old('phone_wa', $profile->phone_wa ?? $user->phone) }}" required placeholder="08123456789" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Aktif <span class="text-rose-400 font-bold">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $profile->email ?? $user->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-white" />
                    </div>
                </div>

                <!-- Alamat Domisili Sekarang (Wajib) -->
                <div class="p-5 rounded-2xl bg-slate-950 border border-indigo-500/30 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white">Alamat Domisili Sekarang <span class="text-rose-400 font-bold">* (Wajib)</span></h3>
                        <span class="text-xs text-indigo-400">Alamat tempat tinggal Anda saat ini</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Alamat Domisili Lengkap (Jalan, RT/RW, Kelurahan, Kecamatan, Kota)*</label>
                            <textarea name="domicile_address" rows="2" required placeholder="Tuliskan alamat lengkap tempat tinggal Anda saat ini..." class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white">{{ old('domicile_address', $profile->domicile_address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Provinsi</label>
                            <input type="text" name="domicile_province" value="{{ old('domicile_province', $profile->domicile_province) }}" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="domicile_city" value="{{ old('domicile_city', $profile->domicile_city) }}" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Status Rumah</label>
                            <select name="domicile_housing_status" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white">
                                <option value="">-- Pilih --</option>
                                @foreach(['Milik Sendiri', 'Orang Tua', 'Sewa / Kontrak', 'Kost', 'Dinas'] as $hs)
                                    <option value="{{ $hs }}" {{ old('domicile_housing_status', $profile->domicile_housing_status) == $hs ? 'selected' : '' }}>{{ $hs }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Alamat KTP (Opsional) -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white">Alamat Sesuai KTP <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h3>
                        <button type="button" onclick="document.querySelector('[name=ktp_address]').value = document.querySelector('[name=domicile_address]').value; document.querySelector('[name=ktp_province]').value = document.querySelector('[name=domicile_province]').value; document.querySelector('[name=ktp_city]').value = document.querySelector('[name=domicile_city]').value;" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                            ⚡ Samakan dengan Alamat Domisili
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Alamat KTP (Kosongkan bila sama dengan domisili)</label>
                            <textarea name="ktp_address" rows="2" placeholder="Kosongkan jika sama dengan domisili..." class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white">{{ old('ktp_address', $profile->ktp_address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Provinsi</label>
                            <input type="text" name="ktp_province" value="{{ old('ktp_province', $profile->ktp_province) }}" placeholder="Contoh: Jawa Barat" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="ktp_city" value="{{ old('ktp_city', $profile->ktp_city) }}" placeholder="Contoh: Kota Bandung" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Status Rumah</label>
                            <select name="ktp_housing_status" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white">
                                <option value="">-- Pilih --</option>
                                @foreach(['Milik Sendiri', 'Orang Tua', 'Sewa / Kontrak', 'Kost', 'Dinas'] as $hs)
                                    <option value="{{ $hs }}" {{ old('ktp_housing_status', $profile->ktp_housing_status) == $hs ? 'selected' : '' }}>{{ $hs }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Kontak Darurat (Opsional) -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <h3 class="text-sm font-bold text-white mb-4">Kontak Darurat (Emergency Contact) <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Kontak Darurat</label>
                            <input type="text" name="emergency_contact[name]" value="{{ $profile->emergency_contact['name'] ?? '' }}" placeholder="Nama keluarga / kerabat" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Hubungan</label>
                            <input type="text" name="emergency_contact[relation]" value="{{ $profile->emergency_contact['relation'] ?? '' }}" placeholder="Orang tua, Suami/Istri, Saudara" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">Nomor Telepon / HP</label>
                            <input type="tel" name="emergency_contact[phone]" value="{{ $profile->emergency_contact['phone'] ?? '' }}" placeholder="0812xxxxxxxx" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN IV: DATA KELUARGA (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-purple-600/20 text-purple-400 font-bold flex items-center justify-center text-sm">IV</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Data Keluarga <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Informasi susunan keluarga bersifat opsional dan dapat dilewati.</span>
                    </div>
                </div>

                <!-- Ayah & Ibu -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Ayah -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <span class="text-sm font-bold text-white block">Data Ayah</span>
                        <input type="text" name="family_father[name]" value="{{ $profile->family_father['name'] ?? '' }}" placeholder="Nama Ayah*" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="family_father[birth_date]" value="{{ $profile->family_father['birth_date'] ?? '' }}" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="family_father[education]" value="{{ $profile->family_father['education'] ?? '' }}" placeholder="Pendidikan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="family_father[job]" value="{{ $profile->family_father['job'] ?? '' }}" placeholder="Pekerjaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="family_father[company]" value="{{ $profile->family_father['company'] ?? '' }}" placeholder="Perusahaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                        <input type="tel" name="family_father[phone]" value="{{ $profile->family_father['phone'] ?? '' }}" placeholder="No HP" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                    </div>

                    <!-- Ibu -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <span class="text-sm font-bold text-white block">Data Ibu</span>
                        <input type="text" name="family_mother[name]" value="{{ $profile->family_mother['name'] ?? '' }}" placeholder="Nama Ibu*" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="family_mother[birth_date]" value="{{ $profile->family_mother['birth_date'] ?? '' }}" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="family_mother[education]" value="{{ $profile->family_mother['education'] ?? '' }}" placeholder="Pendidikan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="family_mother[job]" value="{{ $profile->family_mother['job'] ?? '' }}" placeholder="Pekerjaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="family_mother[company]" value="{{ $profile->family_mother['company'] ?? '' }}" placeholder="Perusahaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                        <input type="tel" name="family_mother[phone]" value="{{ $profile->family_mother['phone'] ?? '' }}" placeholder="No HP" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                    </div>
                </div>

                <!-- Saudara Kandung -->
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-white mb-3">Saudara Kandung</h3>
                    <div class="space-y-3" id="siblings-container">
                        @php
                            $siblings = $profile->family_siblings ?? [['name' => '', 'gender' => '', 'birth_date' => '', 'education' => '', 'job' => '']];
                        @endphp
                        @foreach($siblings as $idx => $sib)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-5 gap-2">
                                <input type="text" name="family_siblings[{{ $idx }}][name]" value="{{ $sib['name'] ?? '' }}" placeholder="Nama Saudara" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <select name="family_siblings[{{ $idx }}][gender]" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="">L/P</option>
                                    <option value="Laki-laki" {{ ($sib['gender'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ ($sib['gender'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <input type="date" name="family_siblings[{{ $idx }}][birth_date]" value="{{ $sib['birth_date'] ?? '' }}" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="family_siblings[{{ $idx }}][education]" value="{{ $sib['education'] ?? '' }}" placeholder="Pendidikan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="family_siblings[{{ $idx }}][job]" value="{{ $sib['job'] ?? '' }}" placeholder="Pekerjaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Keluarga Inti (Pasangan & Anak) -->
                <div>
                    <h3 class="text-sm font-bold text-white mb-1">Keluarga Inti (Pasangan & Anak)</h3>
                    <p class="text-[10px] text-slate-400 mb-3">Wajib diisi bila sudah atau pernah menikah.</p>
                    <div class="space-y-3" id="core-family-container">
                        @php
                            $coreFamily = $profile->family_core ?? [['relation' => '', 'name' => '', 'gender' => '', 'birth_date' => '', 'education' => '', 'job' => '']];
                        @endphp
                        @foreach($coreFamily as $idx => $core)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-6 gap-2">
                                <select name="family_core[{{ $idx }}][relation]" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="">Hubungan</option>
                                    <option value="Suami" {{ ($core['relation'] ?? '') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                    <option value="Istri" {{ ($core['relation'] ?? '') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                    <option value="Anak" {{ ($core['relation'] ?? '') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                </select>
                                <input type="text" name="family_core[{{ $idx }}][name]" value="{{ $core['name'] ?? '' }}" placeholder="Nama Anggota" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <select name="family_core[{{ $idx }}][gender]" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="">L/P</option>
                                    <option value="Laki-laki" {{ ($core['gender'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ ($core['gender'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <input type="date" name="family_core[{{ $idx }}][birth_date]" value="{{ $core['birth_date'] ?? '' }}" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="family_core[{{ $idx }}][education]" value="{{ $core['education'] ?? '' }}" placeholder="Pendidikan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="family_core[{{ $idx }}][job]" value="{{ $core['job'] ?? '' }}" placeholder="Pekerjaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN V: PENDIDIKAN & KETERAMPILAN (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-amber-600/20 text-amber-400 font-bold flex items-center justify-center text-sm">V</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Pendidikan & Keterampilan <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Riwayat pendidikan dan kemampuan bahasa bersifat opsional.</span>
                    </div>
                </div>

                <!-- A. Pendidikan Formal -->
                <div class="space-y-4 mb-8">
                    <h3 class="text-sm font-bold text-white mb-2">A. Pendidikan Formal</h3>

                    @php
                        $formalLevels = [
                            'sd' => 'SD',
                            'smp' => 'SLTP / SMP',
                            'sma' => 'SLTA / SMA / SMK',
                            'diploma' => 'Diploma (D1 / D2 / D3)',
                            's1' => 'Sarjana (S1)',
                            's2' => 'Magister (S2)',
                            's3' => 'Pendidikan Pasca S2 / S3',
                        ];
                    @endphp

                    @foreach($formalLevels as $key => $label)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-xs font-bold text-indigo-400 block mb-2">{{ $label }}</span>
                            <div class="grid grid-cols-1 sm:grid-cols-6 gap-2">
                                <input type="text" name="education_formal[{{ $key }}][school]" value="{{ $profile->education_formal[$key]['school'] ?? '' }}" placeholder="Nama Sekolah / Universitas" class="sm:col-span-2 px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="education_formal[{{ $key }}][place]" value="{{ $profile->education_formal[$key]['place'] ?? '' }}" placeholder="Tempat / Kota" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="education_formal[{{ $key }}][major]" value="{{ $profile->education_formal[$key]['major'] ?? '' }}" placeholder="Jurusan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="education_formal[{{ $key }}][graduation_year]" value="{{ $profile->education_formal[$key]['graduation_year'] ?? '' }}" placeholder="Tahun Lulus" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="education_formal[{{ $key }}][score]" value="{{ $profile->education_formal[$key]['score'] ?? '' }}" placeholder="Nilai / IPK" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- B. Kemampuan Bahasa -->
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-white mb-4">D. Kemampuan Bahasa</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Indonesia -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-slate-900 block">Bahasa Indonesia</span>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Berbicara</label>
                                <select name="languages[indonesia][speak]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Membaca</label>
                                <select name="languages[indonesia][read]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Menulis</label>
                                <select name="languages[indonesia][write]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                        </div>

                        <!-- Inggris -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-slate-900 block">Bahasa Inggris</span>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Berbicara</label>
                                <select name="languages[english][speak]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Membaca</label>
                                <select name="languages[english][read]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Menulis</label>
                                <select name="languages[english][write]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bahasa Lainnya -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-slate-900 block">Bahasa Lainnya (Mandarin, Jepang, dll.)</span>
                            <input type="text" name="languages[other][name]" value="{{ $profile->languages['other']['name'] ?? '' }}" placeholder="Nama Bahasa Lain" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white mb-2" />
                            <div>
                                <label class="text-[10px] text-slate-400 block">Berbicara</label>
                                <select name="languages[other][speak]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Membaca</label>
                                <select name="languages[other][read]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block">Menulis</label>
                                <select name="languages[other][write]" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Pasif">Pasif</option>
                                    <option value="Terbatas">Terbatas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN VI: PENGALAMAN KERJA (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-2 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 font-bold flex items-center justify-center text-sm">VI</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Pengalaman Kerja <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Boleh dilewati bila Anda adalah lulusan baru (Fresh Graduate) atau belum memiliki riwayat kerja.</span>
                    </div>
                </div>

                <div class="space-y-4 pt-4">
                    @php
                        $works = $profile->work_experiences ?? [
                            ['company' => '', 'position_end' => '', 'period_start' => '', 'period_end' => '', 'salary' => '', 'job_desc' => '', 'leave_reason' => '']
                        ];
                    @endphp

                    @foreach($works as $idx => $work)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <span class="text-xs font-bold text-indigo-400">Pengalaman {{ $idx + 1 }}</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <input type="text" name="work_experiences[{{ $idx }}][company]" value="{{ $work['company'] ?? '' }}" placeholder="Nama Perusahaan" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="work_experiences[{{ $idx }}][position_end]" value="{{ $work['position_end'] ?? '' }}" placeholder="Jabatan Akhir" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="work_experiences[{{ $idx }}][salary]" value="{{ $work['salary'] ?? '' }}" placeholder="Gaji Akhir (Rp)" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input type="text" name="work_experiences[{{ $idx }}][period_start]" value="{{ $work['period_start'] ?? '' }}" placeholder="Bulan & Tahun Masuk (misal: Jan 2021)" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                                <input type="text" name="work_experiences[{{ $idx }}][period_end]" value="{{ $work['period_end'] ?? '' }}" placeholder="Bulan & Tahun Keluar (Kosongkan bila masih aktif)" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            </div>
                            <textarea name="work_experiences[{{ $idx }}][job_desc]" rows="2" placeholder="Tugas & Tanggung Jawab Utama..." class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white">{{ $work['job_desc'] ?? '' }}</textarea>
                            <input type="text" name="work_experiences[{{ $idx }}][leave_reason]" value="{{ $work['leave_reason'] ?? '' }}" placeholder="Alasan Berhenti / Pindah" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN VII: REFERENSI (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-2 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-teal-600/20 text-teal-400 font-bold flex items-center justify-center text-sm">VII</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Referensi Profesional <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Orang yang dapat dihubungi untuk memberikan referensi — bukan anggota keluarga. Kosongkan jika belum ada.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                    @for($i = 0; $i < 3; $i++)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-xs font-bold text-slate-900 block">Referensi {{ $i + 1 }}</span>
                            <input type="text" name="references[{{ $i }}][name]" value="{{ $profile->references_data[$i]['name'] ?? '' }}" placeholder="Nama Lengkap" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="references[{{ $i }}][position]" value="{{ $profile->references_data[$i]['position'] ?? '' }}" placeholder="Jabatan / Perusahaan" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="text" name="references[{{ $i }}][relation]" value="{{ $profile->references_data[$i]['relation'] ?? '' }}" placeholder="Hubungan (Atasan / Rekan)" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                            <input type="tel" name="references[{{ $i }}][phone]" value="{{ $profile->references_data[$i]['phone'] ?? '' }}" placeholder="No Telepon / WhatsApp" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white" />
                        </div>
                    @endfor
                </div>
            </section>

            <!-- ==========================================
                 BAGIAN VIII: ESAI & PROSES REKRUTMEN (OPSIONAL)
                 ========================================== -->
            <section class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl form-section">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-8 h-8 rounded-xl bg-rose-600/20 text-rose-400 font-bold flex items-center justify-center text-sm">VIII</span>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Esai Diri & Preferensi Rekrutmen <span class="text-xs text-slate-500 font-normal">(Opsional)</span></h2>
                        <span class="text-xs text-slate-400">Isian esai dan preferensi rekrutmen dapat dilengkapi nanti.</span>
                    </div>
                </div>

                <div class="space-y-6 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Sebutkan kelebihan dan kekurangan diri Anda <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <textarea name="strengths_weaknesses" rows="3" placeholder="Jelaskan secara objektif kelebihan utama dan aspek diri yang sedang Anda kembangkan..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">{{ old('strengths_weaknesses', $profile->strengths_weaknesses) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Pencapaian apa yang paling Anda banggakan? <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <textarea name="proudest_achievement" rows="3" placeholder="Prestasi akademis, keberhasilan proyek di tempat kerja sebelumnya, atau inisiatif kepemimpinan..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">{{ old('proudest_achievement', $profile->proudest_achievement) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">Berapa ekspektasi gaji bulanan Anda (Rp)? <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                            <input type="number" name="expected_salary" value="{{ old('expected_salary', $profile->expected_salary) }}" placeholder="Contoh: 12000000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-900 mb-1.5">Estimasi Waktu Mulai Bekerja <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                            <select name="estimated_start_date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                                <option value="">-- Pilih Waktu (Opsional) --</option>
                                <option value="Segera (Immediately)">Segera (Immediately)</option>
                                <option value="1 Minggu">1 Minggu setelah Offering</option>
                                <option value="2 Minggu">2 Minggu setelah Offering</option>
                                <option value="1 Bulan (1 Month Notice)">1 Bulan (1 Month Notice)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <span class="text-xs text-slate-700 font-semibold">Pernah melamar di perusahaan ini sebelumnya?</span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center text-xs text-slate-800 font-medium">
                                    <input type="radio" name="applied_before" value="1" {{ old('applied_before', $profile->applied_before) ? 'checked' : '' }} class="mr-1.5" /> Ya
                                </label>
                                <label class="inline-flex items-center text-xs text-slate-800 font-medium">
                                    <input type="radio" name="applied_before" value="0" {{ !old('applied_before', $profile->applied_before) ? 'checked' : '' }} class="mr-1.5" /> Tidak
                                </label>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <span class="text-xs text-slate-700 font-semibold">Bersedia ditempatkan di luar kota?</span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center text-xs text-slate-800 font-medium">
                                    <input type="radio" name="willing_to_relocate" value="1" {{ old('willing_to_relocate', $profile->willing_to_relocate) ? 'checked' : '' }} class="mr-1.5" /> Ya
                                </label>
                                <label class="inline-flex items-center text-xs text-slate-800 font-medium">
                                    <input type="radio" name="willing_to_relocate" value="0" {{ !old('willing_to_relocate', $profile->willing_to_relocate) ? 'checked' : '' }} class="mr-1.5" /> Tidak
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Pilih Lokasi Rekrutmen Terdekat <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <select name="recruitment_location" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih Lokasi Rekrutmen (Opsional) --</option>
                            <option value="Jakarta HQ (Head Office)">Jakarta HQ (Head Office)</option>
                            <option value="Bandung Tech Hub">Bandung Tech Hub</option>
                            <option value="Surabaya Hub">Surabaya Hub</option>
                            <option value="Semarang Hub">Semarang Hub</option>
                            <option value="Online / Remote Interview">Online / Remote Interview</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1.5">Apa yang perlu Anda persiapkan sebelum mulai bekerja? <span class="text-xs text-slate-500 font-normal">(Opsional)</span></label>
                        <input type="text" name="preparation_notes" value="{{ old('preparation_notes', $profile->preparation_notes) }}" placeholder="Handover pekerjaan sebelumnya, relokasi domisili, dll." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-indigo-600" />
                    </div>
                </div>

                <!-- Pernyataan Persetujuan -->
                <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200 mb-8">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" name="agreement_signed" value="1" required {{ old('agreement_signed', $profile->agreement_signed) ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-xs text-slate-700 leading-relaxed">
                            Dengan ini saya menyatakan bahwa seluruh informasi yang tercantum dalam formulir ini adalah <strong class="text-slate-900 font-bold">benar dan dapat dipertanggungjawabkan</strong>. Saya menyadari bahwa informasi palsu atau yang menyesatkan dapat menjadi dasar penolakan lamaran atau pemutusan hubungan kerja di kemudian hari.
                        </span>
                    </label>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('career.dashboard') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 shadow-xl shadow-indigo-600/30 transition-all text-center">
                        Simpan Formulir Data Diri & CV Lengkap
                    </button>
                </div>
            </section>
        </form>
    </main>
</body>
</html>
