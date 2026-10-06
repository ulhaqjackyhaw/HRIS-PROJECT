@extends('layouts.app', ['title' => 'Review 360° Pelamar: ' . $application->applicant_name])

@section('content')
<div class="space-y-6">
    <!-- Top Bar Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('recruitment.applications.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
            ← Kembali ke Pipeline Seleksi
        </a>

        <div class="flex items-center space-x-2">
            @if($application->employee)
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center space-x-1.5 shadow-xs">
                    <span>✓ Karyawan Aktif Core HR:</span>
                    <a href="{{ route('employees.show', $application->employee->id) }}" class="underline hover:text-emerald-900 font-extrabold">{{ $application->employee->nik }}</a>
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Candidate Profile Header Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center space-x-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md shrink-0 overflow-hidden">
                    @if($profile && $profile->photo_path)
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $application->applicant_name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($application->applicant_name, 0, 2)) }}
                    @endif
                </div>

                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $application->applicant_name }}</h1>
                        <span class="px-3 py-1 rounded-xl text-xs font-bold {{ $application->current_stage === 'HIRED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($application->current_stage === 'REJECTED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200') }}">
                            {{ $application->stage_label }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                        <span>Melamar: <strong class="text-slate-900">{{ $application->jobPosting->title ?? '-' }}</strong> ({{ $application->jobPosting->department->name ?? '-' }})</span>
                        <span>•</span>
                        <span>{{ $application->applicant_email }}</span>
                        <span>•</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->applicant_phone) }}" target="_blank" class="text-emerald-700 font-semibold hover:underline">
                            WhatsApp: {{ $application->applicant_phone }} ↗
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Bar -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Ubah Tahapan -->
                <button type="button" onclick="document.getElementById('modal-update-stage').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-colors">
                    🔄 Pindah Tahap
                </button>

                <!-- Jadwalkan Wawancara -->
                <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-colors">
                    📅 Jadwal Interview
                </button>

                <!-- Kirim Pesan / Broadcast -->
                <button type="button" onclick="document.getElementById('modal-send-message').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-xs">
                    💬 WhatsApp / Email
                </button>

                <!-- Convert to Employee (Core HR Onboarding) -->
                @if(!$application->employee)
                    <button type="button" onclick="document.getElementById('modal-convert-employee').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all flex items-center space-x-1.5">
                        <span>🎉 Convert to Employee</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 8-Stage Selection Stepper Progress -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs overflow-x-auto">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Linimasa Progres Seleksi</h3>
        <div class="flex items-center min-w-[900px] justify-between relative">
            @php
                $activeOrder = $application->stage_order;
            @endphp
            @foreach(\App\Models\JobApplication::STAGES as $stKey => $stVal)
                @if($stKey !== 'REJECTED')
                    @php
                        $isCurrent = $application->current_stage === $stKey;
                        $isPast = $stVal['order'] < $activeOrder && $activeOrder > 0;
                    @endphp
                    <div class="flex flex-col items-center text-center flex-1 relative group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs mb-2 transition-all {{ $isCurrent ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 shadow-xs' : ($isPast ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-100 text-slate-400') }}">
                            {{ $isPast ? '✓' : $stVal['order'] }}
                        </div>
                        <span class="text-[11px] font-semibold {{ $isCurrent ? 'text-indigo-600 font-bold' : ($isPast ? 'text-slate-700' : 'text-slate-400') }} line-clamp-1">
                            {{ $stVal['label'] }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Candidate Detailed Dossier (Tabs / Accordions) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: Main Dossier -->
        <div class="lg:col-span-2 space-y-6">
            <!-- I. Berkas CV & Dokumen Digital -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold flex items-center justify-center text-xs">I</span>
                        <h3 class="text-base font-bold text-slate-900">Berkas CV & Dokumen Digital Pelamar</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- CV / Resume -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Berkas CV / Resume</span>
                            <span class="text-[10px] text-slate-500">PDF / Dokumen utama</span>
                        </div>
                        @if($application->resume_path || ($profile && $profile->cv_path))
                            <a href="{{ asset('storage/' . ($application->resume_path ?? $profile->cv_path)) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Unduh / Lihat CV ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Belum ada berkas</span>
                        @endif
                    </div>

                    <!-- Ijazah Terakhir -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Ijazah Terakhir</span>
                            <span class="text-[10px] text-slate-500">Dokumen kelulusan</span>
                        </div>
                        @if($profile && $profile->certificate_path)
                            <a href="{{ asset('storage/' . $profile->certificate_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Ijazah ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>

                    <!-- Transkrip Nilai -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Transkrip Nilai</span>
                            <span class="text-[10px] text-slate-500">Daftar nilai akademik</span>
                        </div>
                        @if($profile && $profile->transcript_path)
                            <a href="{{ asset('storage/' . $profile->transcript_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Transkrip ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>

                    <!-- Foto Formal -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Foto Formal Diri</span>
                            <span class="text-[10px] text-slate-500">Pas foto resmi</span>
                        </div>
                        @if($profile && $profile->photo_path)
                            <a href="{{ asset('storage/' . $profile->photo_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Foto ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>
                </div>

                @if($application->cover_letter)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Cover Letter / Motivasi Pelamar:</span>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $application->cover_letter }}</p>
                    </div>
                @endif
            </div>

            <!-- II. Data Pribadi, Karakteristik Fisik & Medis -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 font-bold flex items-center justify-center text-xs">II</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Data Pribadi, Fisik & Riwayat Medis</h3>
                        <p class="text-[11px] text-slate-500">Profil personal, ukuran pakaian, postur fisik, dan status kesehatan.</p>
                    </div>
                </div>

                <!-- Personal Info Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-slate-500 block mb-0.5">Nama Lengkap</span>
                        <span class="font-bold text-slate-900">{{ $profile->full_name ?? $application->applicant_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Nama Panggilan</span>
                        <span class="font-semibold text-slate-800">{{ $profile->nickname ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-800">{{ $profile->gender ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Tempat & Tgl Lahir</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->birth_place ?? '-' }}, {{ $profile?->birth_date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Agama</span>
                        <span class="font-semibold text-slate-800">{{ $profile->religion ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Status Pernikahan</span>
                        <span class="font-semibold text-slate-800">{{ $profile->marital_status ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Kewarganegaraan</span>
                        <span class="font-semibold text-slate-800">{{ $profile->nationality ?? 'Indonesia' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">Suku Bangsa</span>
                        <span class="font-semibold text-slate-800">{{ $profile->ethnicity ?? '-' }}</span>
                    </div>
                </div>

                <!-- Posture, Blood & Uniform Sizes -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Fisik, Golongan Darah & Ukuran Seragam</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Tinggi Badan</span>
                            <span class="font-bold text-slate-900">{{ $profile && $profile->height_cm ? $profile->height_cm . ' cm' : '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Berat Badan</span>
                            <span class="font-bold text-slate-900">{{ $profile && $profile->weight_kg ? $profile->weight_kg . ' kg' : '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Ukuran Baju</span>
                            <span class="font-bold text-slate-900">{{ $profile->clothing_size ?? '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Ukuran Sepatu</span>
                            <span class="font-bold text-slate-900">{{ $profile->shoe_size ?? '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Gol. Darah</span>
                            <span class="font-bold text-slate-900">{{ $profile->blood_type ?? '-' }} {{ $profile->blood_rhesus ? '(' . $profile->blood_rhesus . ')' : '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hobbies & Medical Notes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 block text-[11px] mb-1 font-semibold">Hobi & Kegemaran:</span>
                        <span class="text-slate-800 font-medium">{{ $profile->hobbies ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 block text-[11px] mb-1 font-semibold">Riwayat Medis / Kesehatan:</span>
                        <span class="text-slate-800 font-medium">{{ $profile->medical_history ?? 'Tidak ada riwayat penyakit berat' }}</span>
                    </div>
                </div>
            </div>

            <!-- III. Identitas Legalitas, SIM & Kendaraan Pribadi -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold flex items-center justify-center text-xs">III</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Identitas Legalitas, SIM & Kendaraan Pribadi</h3>
                        <p class="text-[11px] text-slate-500">Nomor dokumen kenegaraan, lisensi mengemudi, dan kendaraan operasional.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-0.5">No KTP (NIK)</span>
                        <span class="font-bold text-slate-900">{{ $profile->ktp_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">Exp: {{ $profile?->ktp_expiry?->format('d/m/Y') ?? 'Seumur Hidup' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">No NPWP</span>
                        <span class="font-bold text-slate-900">{{ $profile->npwp_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">Exp: {{ $profile?->npwp_expiry?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">No Paspor</span>
                        <span class="font-bold text-slate-900">{{ $profile->passport_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">Exp: {{ $profile?->passport_expiry?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">BPJS TK / No KK</span>
                        <span class="font-bold text-slate-900">{{ $profile->bpjs_tk_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">KK: {{ $profile->family_card_number ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-0.5">SIM A (Mobil)</span>
                        <span class="font-bold text-slate-900">{{ $profile->sim_a_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">Exp: {{ $profile?->sim_a_expiry?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-0.5">SIM C (Motor)</span>
                        <span class="font-bold text-slate-900">{{ $profile->sim_c_number ?? '-' }}</span>
                        <span class="text-[10px] text-slate-500 block">Exp: {{ $profile?->sim_c_expiry?->format('d/m/Y') ?? '-' }}</span>
                    </div>

                    <!-- Kendaraan Mobil -->
                    <div class="sm:col-span-1 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-indigo-700 block mb-0.5">Mobil Pribadi</span>
                        @if(!empty($profile->vehicles_data['car']['brand']))
                            <div class="font-bold text-slate-900">{{ $profile->vehicles_data['car']['brand'] }} ({{ $profile->vehicles_data['car']['year'] ?? '-' }})</div>
                            <span class="text-[10px] text-slate-500">Status: {{ $profile->vehicles_data['car']['status'] ?? '-' }}</span>
                        @else
                            <span class="text-slate-400">Tidak ada</span>
                        @endif
                    </div>

                    <!-- Kendaraan Motor -->
                    <div class="sm:col-span-1 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-indigo-700 block mb-0.5">Sepeda Motor</span>
                        @if(!empty($profile->vehicles_data['motorcycle']['brand']))
                            <div class="font-bold text-slate-900">{{ $profile->vehicles_data['motorcycle']['brand'] }} ({{ $profile->vehicles_data['motorcycle']['year'] ?? '-' }})</div>
                            <span class="text-[10px] text-slate-500">Status: {{ $profile->vehicles_data['motorcycle']['status'] ?? '-' }}</span>
                        @else
                            <span class="text-slate-400">Tidak ada</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- IV. Kontak & Alamat Lengkap Domisili -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 font-bold flex items-center justify-center text-xs">IV</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kontak, Domisili & Kontak Darurat</h3>
                        <p class="text-[11px] text-slate-500">Alamat domisili saat ini, alamat KTP, dan nomor keluarga darurat.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Alamat Domisili -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">Alamat Domisili Sekarang</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ $profile->domicile_housing_status ?? 'Tempat Tinggal' }}
                            </span>
                        </div>
                        <p class="text-slate-700 font-medium leading-relaxed">{{ $profile->domicile_address ?? '-' }}</p>
                        <div class="text-[11px] text-slate-500 pt-1">
                            Kota/Kab: <strong class="text-slate-800">{{ $profile->domicile_city ?? '-' }}</strong> • Prov: <strong class="text-slate-800">{{ $profile->domicile_province ?? '-' }}</strong>
                        </div>
                    </div>

                    <!-- Alamat KTP -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">Alamat Sesuai KTP</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $profile->ktp_housing_status ?? 'KTP' }}
                            </span>
                        </div>
                        <p class="text-slate-700 font-medium leading-relaxed">{{ $profile->ktp_address ?? ($profile->domicile_address ?? '-') }}</p>
                        <div class="text-[11px] text-slate-500 pt-1">
                            Kota/Kab: <strong class="text-slate-800">{{ $profile->ktp_city ?? ($profile->domicile_city ?? '-') }}</strong> • Prov: <strong class="text-slate-800">{{ $profile->ktp_province ?? ($profile->domicile_province ?? '-') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-[11px] font-bold text-indigo-700 block">Kontak Darurat (Emergency Contact):</span>
                        <span class="text-slate-900 font-extrabold text-sm">{{ $profile->emergency_contact['name'] ?? 'Belum diisi' }}</span>
                        <span class="text-slate-600"> ({{ $profile->emergency_contact['relation'] ?? 'Kerabat' }})</span>
                    </div>
                    @if(!empty($profile->emergency_contact['phone']))
                        <a href="tel:{{ $profile->emergency_contact['phone'] }}" class="px-3 py-1.5 rounded-xl bg-white text-indigo-700 font-bold border border-indigo-200 hover:bg-indigo-50 shadow-xs">
                            📞 {{ $profile->emergency_contact['phone'] }}
                        </a>
                    @else
                        <span class="text-slate-400">No telepon belum dicantumkan</span>
                    @endif
                </div>
            </div>

            <!-- V. Susunan Anggota Keluarga -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 font-bold flex items-center justify-center text-xs">V</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Susunan Anggota Keluarga</h3>
                        <p class="text-[11px] text-slate-500">Data orang tua, saudara kandung, dan keluarga inti pasangan/anak.</p>
                    </div>
                </div>

                <!-- Ayah & Ibu Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Ayah -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs font-bold text-indigo-700 block">Data Ayah</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $profile->family_father['name'] ?? '-' }}</div>
                        <div class="grid grid-cols-2 gap-1 text-[11px] text-slate-600">
                            <div>Pekerjaan: <strong class="text-slate-800">{{ $profile->family_father['job'] ?? '-' }}</strong></div>
                            <div>Instansi: <strong class="text-slate-800">{{ $profile->family_father['company'] ?? '-' }}</strong></div>
                            <div>Pendidikan: <strong class="text-slate-800">{{ $profile->family_father['education'] ?? '-' }}</strong></div>
                            <div>Tgl Lahir: <strong class="text-slate-800">{{ $profile->family_father['birth_date'] ?? '-' }}</strong></div>
                        </div>
                        @if(!empty($profile->family_father['phone']))
                            <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200">No HP: <strong class="text-slate-800">{{ $profile->family_father['phone'] }}</strong></div>
                        @endif
                    </div>

                    <!-- Ibu -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs font-bold text-indigo-700 block">Data Ibu</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $profile->family_mother['name'] ?? '-' }}</div>
                        <div class="grid grid-cols-2 gap-1 text-[11px] text-slate-600">
                            <div>Pekerjaan: <strong class="text-slate-800">{{ $profile->family_mother['job'] ?? '-' }}</strong></div>
                            <div>Instansi: <strong class="text-slate-800">{{ $profile->family_mother['company'] ?? '-' }}</strong></div>
                            <div>Pendidikan: <strong class="text-slate-800">{{ $profile->family_mother['education'] ?? '-' }}</strong></div>
                            <div>Tgl Lahir: <strong class="text-slate-800">{{ $profile->family_mother['birth_date'] ?? '-' }}</strong></div>
                        </div>
                        @if(!empty($profile->family_mother['phone']))
                            <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200">No HP: <strong class="text-slate-800">{{ $profile->family_mother['phone'] }}</strong></div>
                        @endif
                    </div>
                </div>

                <!-- Saudara Kandung -->
                @if(!empty($profile->family_siblings) && count(array_filter($profile->family_siblings, fn($s) => !empty($s['name']))))
                    <div class="pt-2">
                        <h4 class="text-xs font-bold text-slate-700 mb-2">Saudara Kandung:</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 text-slate-900 font-bold border-b border-slate-200 text-[11px]">
                                    <tr>
                                        <th class="p-2.5">Nama Saudara</th>
                                        <th class="p-2.5">L/P</th>
                                        <th class="p-2.5">Tgl Lahir</th>
                                        <th class="p-2.5">Pendidikan</th>
                                        <th class="p-2.5">Pekerjaan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($profile->family_siblings as $sib)
                                        @if(!empty($sib['name']))
                                            <tr>
                                                <td class="p-2.5 font-bold text-slate-900">{{ $sib['name'] }}</td>
                                                <td class="p-2.5">{{ $sib['gender'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $sib['birth_date'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $sib['education'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $sib['job'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Keluarga Inti (Pasangan & Anak) -->
                @if(!empty($profile->family_core) && count(array_filter($profile->family_core, fn($c) => !empty($c['name']))))
                    <div class="pt-2">
                        <h4 class="text-xs font-bold text-slate-700 mb-2">Keluarga Inti (Pasangan & Anak):</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 text-slate-900 font-bold border-b border-slate-200 text-[11px]">
                                    <tr>
                                        <th class="p-2.5">Hubungan</th>
                                        <th class="p-2.5">Nama Anggota</th>
                                        <th class="p-2.5">L/P</th>
                                        <th class="p-2.5">Tgl Lahir</th>
                                        <th class="p-2.5">Pendidikan</th>
                                        <th class="p-2.5">Pekerjaan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($profile->family_core as $core)
                                        @if(!empty($core['name']))
                                            <tr>
                                                <td class="p-2.5 font-bold text-indigo-700">{{ $core['relation'] ?? '-' }}</td>
                                                <td class="p-2.5 font-bold text-slate-900">{{ $core['name'] }}</td>
                                                <td class="p-2.5">{{ $core['gender'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $core['birth_date'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $core['education'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $core['job'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- VI. Riwayat Pendidikan Formal & Kemampuan Bahasa -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-bold flex items-center justify-center text-xs">VI</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Riwayat Pendidikan Formal & Kemampuan Bahasa</h3>
                        <p class="text-[11px] text-slate-500">Pendidikan formal berjenjang dan kemampuan penguasaan bahasa.</p>
                    </div>
                </div>

                <!-- Formal Education Table -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 mb-2">Riwayat Pendidikan Formal:</h4>
                    @php
                        $formalLabels = [
                            'sd' => 'SD',
                            'smp' => 'SLTP / SMP',
                            'sma' => 'SLTA / SMA / SMK',
                            'diploma' => 'Diploma (D1-D3)',
                            's1' => 'Sarjana (S1)',
                            's2' => 'Magister (S2)',
                            's3' => 'Doktor (S3)',
                        ];
                        $hasEdu = false;
                        if (!empty($profile->education_formal)) {
                            foreach($profile->education_formal as $lvl => $val) {
                                if (!empty($val['school'])) $hasEdu = true;
                            }
                        }
                    @endphp

                    @if($hasEdu)
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 text-slate-900 font-bold border-b border-slate-200 text-[11px]">
                                    <tr>
                                        <th class="p-2.5">Jenjang</th>
                                        <th class="p-2.5">Nama Institusi / Sekolah</th>
                                        <th class="p-2.5">Tempat / Kota</th>
                                        <th class="p-2.5">Jurusan</th>
                                        <th class="p-2.5">Tahun Lulus</th>
                                        <th class="p-2.5">IPK / Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($formalLabels as $key => $lbl)
                                        @if(!empty($profile->education_formal[$key]['school']))
                                            <tr>
                                                <td class="p-2.5 font-bold text-indigo-700">{{ $lbl }}</td>
                                                <td class="p-2.5 font-bold text-slate-900">{{ $profile->education_formal[$key]['school'] }}</td>
                                                <td class="p-2.5">{{ $profile->education_formal[$key]['place'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $profile->education_formal[$key]['major'] ?? '-' }}</td>
                                                <td class="p-2.5">{{ $profile->education_formal[$key]['graduation_year'] ?? '-' }}</td>
                                                <td class="p-2.5 font-semibold text-emerald-700">{{ $profile->education_formal[$key]['score'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-xs text-slate-400 py-2">Data riwayat pendidikan belum diisi.</div>
                    @endif
                </div>

                <!-- Languages Skills -->
                @if(!empty($profile->languages))
                    <div class="pt-3 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-700 mb-2">Kemampuan Penguasaan Bahasa:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                <span class="font-bold text-slate-900 block text-xs">Bahasa Indonesia</span>
                                <div class="text-[11px] text-slate-600">Bicara: <strong class="text-slate-900">{{ $profile->languages['indonesia']['speak'] ?? 'Aktif' }}</strong></div>
                                <div class="text-[11px] text-slate-600">Baca: <strong class="text-slate-900">{{ $profile->languages['indonesia']['read'] ?? 'Aktif' }}</strong></div>
                                <div class="text-[11px] text-slate-600">Tulis: <strong class="text-slate-900">{{ $profile->languages['indonesia']['write'] ?? 'Aktif' }}</strong></div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                <span class="font-bold text-slate-900 block text-xs">Bahasa Inggris</span>
                                <div class="text-[11px] text-slate-600">Bicara: <strong class="text-slate-900">{{ $profile->languages['english']['speak'] ?? '-' }}</strong></div>
                                <div class="text-[11px] text-slate-600">Baca: <strong class="text-slate-900">{{ $profile->languages['english']['read'] ?? '-' }}</strong></div>
                                <div class="text-[11px] text-slate-600">Tulis: <strong class="text-slate-900">{{ $profile->languages['english']['write'] ?? '-' }}</strong></div>
                            </div>

                            @if(!empty($profile->languages['other']['name']))
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <span class="font-bold text-slate-900 block text-xs">{{ $profile->languages['other']['name'] }}</span>
                                    <div class="text-[11px] text-slate-600">Bicara: <strong class="text-slate-900">{{ $profile->languages['other']['speak'] ?? '-' }}</strong></div>
                                    <div class="text-[11px] text-slate-600">Baca: <strong class="text-slate-900">{{ $profile->languages['other']['read'] ?? '-' }}</strong></div>
                                    <div class="text-[11px] text-slate-600">Tulis: <strong class="text-slate-900">{{ $profile->languages['other']['write'] ?? '-' }}</strong></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- VII. Riwayat Pengalaman Kerja & Referensi -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold flex items-center justify-center text-xs">VII</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Riwayat Pengalaman Kerja & Referensi</h3>
                        <p class="text-[11px] text-slate-500">Histori karir profesional dan kontak referensi pemberi kerja sebelumnya.</p>
                    </div>
                </div>

                <!-- Experience Cards -->
                @if($profile && !empty($profile->work_experiences))
                    <div class="space-y-3">
                        @foreach($profile->work_experiences as $exp)
                            @if(!empty($exp['company']) || !empty($exp['position_end']))
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $exp['position_end'] ?? '-' }} di {{ $exp['company'] ?? '-' }}</h4>
                                        <span class="text-xs text-slate-500 font-semibold">{{ $exp['period_start'] ?? '' }} - {{ $exp['period_end'] ?? 'Sekarang' }}</span>
                                    </div>
                                    @if(!empty($exp['salary']))
                                        <div class="text-[11px] text-emerald-700 font-bold">Gaji Akhir: Rp {{ number_format((float) $exp['salary'], 0, ',', '.') }}</div>
                                    @endif
                                    @if(!empty($exp['job_desc']))
                                        <div class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200 whitespace-pre-line leading-relaxed">
                                            <span class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wider">Tugas & Tanggung Jawab:</span>
                                            {{ $exp['job_desc'] }}
                                        </div>
                                    @endif
                                    @if(!empty($exp['leave_reason']))
                                        <div class="text-[11px] text-slate-500">
                                            Alasan Berhenti / Pindah: <strong class="text-slate-800">{{ $exp['leave_reason'] }}</strong>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-2">Tidak ada data riwayat pekerjaan / Fresh Graduate.</div>
                @endif

                <!-- Professional References -->
                @if(!empty($profile->references_data) && count(array_filter($profile->references_data, fn($r) => !empty($r['name']))))
                    <div class="pt-3 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-700 mb-2">Referensi Profesional:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            @foreach($profile->references_data as $ref)
                                @if(!empty($ref['name']))
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                        <span class="font-bold text-slate-900 block text-xs">{{ $ref['name'] }}</span>
                                        <div class="text-[11px] text-slate-600">{{ $ref['position'] ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-500">Hubungan: <strong class="text-slate-800">{{ $ref['relation'] ?? '-' }}</strong></div>
                                        @if(!empty($ref['phone']))
                                            <div class="text-[11px] text-indigo-700 font-semibold pt-1">Telp: {{ $ref['phone'] }}</div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- VIII. Esai Diri, Preferensi Rekrutmen & Persetujuan -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold flex items-center justify-center text-xs">VIII</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Esai Diri, Preferensi Rekrutmen & Pernyataan Sah</h3>
                        <p class="text-[11px] text-slate-500">Ekspektasi gaji, ketersediaan mulai kerja, lokasi rekrutmen, dan persetujuan integritas.</p>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-1 font-semibold">Kelebihan & Kekurangan Diri:</span>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $profile->strengths_weaknesses ?? 'Belum diisi' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-1 font-semibold">Pencapaian Paling Membanggakan:</span>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $profile->proudest_achievement ?? 'Belum diisi' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Ekspektasi Gaji:</span>
                            <span class="font-bold text-emerald-700 text-sm">
                                {{ $profile && $profile->expected_salary ? 'Rp ' . number_format((float) $profile->expected_salary, 0, ',', '.') : 'Tidak disebutkan' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Mulai Bekerja:</span>
                            <span class="font-bold text-slate-900">{{ $profile->estimated_start_date ?? 'Segera' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Pernah Melamar:</span>
                            <span class="font-bold {{ $profile && $profile->applied_before ? 'text-indigo-700' : 'text-slate-700' }}">
                                {{ $profile && $profile->applied_before ? '✓ Ya, Pernah' : 'Tidak' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block text-[11px]">Bersedia Relokasi:</span>
                            <span class="font-bold {{ $profile && $profile->willing_to_relocate ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $profile && $profile->willing_to_relocate ? '✓ Bersedia' : 'Tidak' }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($profile->recruitment_location) || !empty($profile->preparation_notes))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            @if(!empty($profile->recruitment_location))
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-500 block text-[11px]">Lokasi Rekrutmen Pilihan:</span>
                                    <span class="font-bold text-slate-900">{{ $profile->recruitment_location }}</span>
                                </div>
                            @endif
                            @if(!empty($profile->preparation_notes))
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-500 block text-[11px]">Catatan Persiapan Kerja:</span>
                                    <span class="text-slate-800">{{ $profile->preparation_notes }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Agreement Status -->
                    <div class="p-3.5 rounded-2xl {{ $profile && $profile->agreement_signed ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }} flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-base">{{ $profile && $profile->agreement_signed ? '✅' : '⏳' }}</span>
                            <span class="text-xs font-bold {{ $profile && $profile->agreement_signed ? 'text-emerald-800' : 'text-slate-700' }}">
                                {{ $profile && $profile->agreement_signed ? 'Pernyataan Kebenaran Data Resmi Ditandatangani Pelamar' : 'Pernyataan kebenaran data belum ditandai' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-slate-500">Legal Agreement</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Psychotest Results, Interview & Communications -->
        <div class="space-y-6">
            <!-- Hasil Psikotes Online -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Hasil Ujian Psikotes</h3>
                    <span class="text-xs text-indigo-600 font-semibold">Online Assessment</span>
                </div>

                @forelse($application->psychotestResults as $res)
                    @php
                        $isPassed = $res->result_status === 'PASSED' || $res->total_score >= ($res->psychotest?->passing_score ?? 70);
                        $answers = is_array($res->answers_submitted) ? $res->answers_submitted : [];
                        $ansType = $answers['type'] ?? '';
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 border {{ $isPassed ? 'border-emerald-300' : 'border-rose-300' }} space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $ansType === 'KRAEPELIN' ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }} inline-block mb-1">
                                    {{ $ansType === 'KRAEPELIN' ? '⚡ KRAEPELIN NUMERIK' : ($ansType === 'LIKERT_PERSONALITY' ? '🧠 KEPRIBADIAN (1-5)' : 'PSIKOMETRI') }}
                                </span>
                                <div class="text-xs font-bold text-slate-900">{{ $res->psychotest->title ?? 'Psikotes Standar' }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $isPassed ? '✓ LULUS' : '✗ TIDAK LULUS' }}
                            </span>
                        </div>

                        <div class="flex items-baseline space-x-1.5">
                            <span class="text-2xl font-black {{ $isPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $res->total_score }}</span>
                            <span class="text-xs text-slate-400 font-semibold">/ 100</span>
                        </div>

                        @if($ansType === 'KRAEPELIN')
                            <div class="grid grid-cols-2 gap-1.5 text-[11px] text-slate-600 pt-1 border-t border-slate-200">
                                <div>Akurasi: <strong class="text-emerald-700">{{ $answers['accuracy_rate'] ?? '-' }}%</strong></div>
                                <div>Hitungan: <strong class="text-slate-900">{{ $answers['total_attempted'] ?? '-' }} butir</strong></div>
                                <div>Jawaban Benar: <strong class="text-cyan-700">{{ $answers['correct_count'] ?? '-' }}</strong></div>
                                <div>Kolom Selesai: <strong class="text-slate-900">{{ $answers['columns_completed'] ?? '-' }} kolom</strong></div>
                            </div>
                            @if(!empty($answers['metrics']))
                                <div class="grid grid-cols-2 gap-1.5 text-[10px] text-slate-600 pt-1.5 bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                                    <div>Panker (Kecepatan): <strong class="text-slate-900 block">{{ $answers['metrics']['panker'] ?? '-' }} butir/kolom</strong></div>
                                    <div>Hanker (Ketahanan): <strong class="text-indigo-700 block">{{ $answers['metrics']['hanker_trend'] ?? '-' }}</strong></div>
                                </div>
                            @endif
                            <button type="button"
                                    onclick='openKraepelinModal(@json($answers), "{{ addslashes($application->applicant_name) }}", "{{ $res->total_score }}", "{{ $res->result_status }}")'
                                    class="w-full mt-2 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                <span>📈</span>
                                <span>Lihat Grafik Kurva Kraepelin</span>
                            </button>
                        @elseif($ansType === 'LIKERT_PERSONALITY')
                            <div class="space-y-1.5 text-[11px] text-slate-600 pt-1 border-t border-slate-200">
                                <div class="flex justify-between">
                                    <span>Skor Rata-rata:</span>
                                    <strong class="text-purple-700">{{ $answers['average_rating'] ?? '-' }} / 5.0</strong>
                                </div>
                                @if(!empty($answers['dimension_scores']) && is_array($answers['dimension_scores']))
                                    <div class="space-y-1 pt-1">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-500 font-bold block">Dimensi Karakter:</span>
                                        @foreach(array_slice($answers['dimension_scores'], 0, 4) as $dim => $dimPct)
                                            <div class="flex justify-between text-[10px]">
                                                <span class="truncate max-w-[140px] text-slate-600">{{ $dim }}</span>
                                                <span class="font-bold text-slate-900">{{ $dimPct }}%</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="text-[10px] text-slate-400 pt-1">
                            Diselesaikan pada: {{ $res->completed_at ? $res->completed_at->format('d M Y, H:i') : '-' }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center border border-dashed border-slate-200 rounded-2xl text-xs text-slate-400">
                        Kandidat belum mengambil asesmen psikotes.
                    </div>
                @endforelse
            </div>

            <!-- Jadwal Wawancara Terjadwal -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Jadwal Wawancara</h3>
                    <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.remove('hidden')" class="text-xs text-indigo-600 font-semibold hover:underline">
                        + Atur Jadwal
                    </button>
                </div>

                @if($application->interview_scheduled_at)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="text-xs font-bold text-indigo-600">{{ $application->stage_label }}</div>
                        <div class="text-sm font-bold text-slate-900">{{ $application->interview_scheduled_at->format('l, d F Y - H:i') }} WIB</div>
                        <div class="text-xs text-slate-600">Lokasi / Link: {{ $application->interview_location }}</div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3 text-center">Belum ada jadwal wawancara aktif.</div>
                @endif
            </div>

            <!-- Log Komunikasi (WhatsApp / Email) -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Komunikasi</h3>
                    <button type="button" onclick="document.getElementById('modal-send-message').classList.remove('hidden')" class="text-xs text-emerald-700 font-semibold hover:underline">
                        + Kirim Pesan
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($application->communications as $comm)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ $comm->channel }}</span>
                                <span class="text-[10px] text-slate-400">{{ $comm->sent_at?->diffForHumans() }}</span>
                            </div>
                            <div class="text-[11px] text-slate-700 font-semibold">{{ $comm->subject }}</div>
                            <p class="text-[11px] text-slate-500 line-clamp-2">{{ $comm->message }}</p>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-3 text-center">Belum ada catatan komunikasi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MODALS (Light Theme with clear borders)
     ========================================== -->

<!-- 1. Modal Update Stage -->
<div id="modal-update-stage" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Pindahkan Tahap Seleksi</h3>
        <p class="text-xs text-slate-500 mb-6">Pilih status tahapan terbaru untuk pelamar {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.stage', $application->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Tahap Baru*</label>
                <select name="stage" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                    @foreach(\App\Models\JobApplication::STAGES as $stK => $stV)
                        <option value="{{ $stK }}" {{ $application->current_stage === $stK ? 'selected' : '' }}>
                            {{ $stV['label'] }} (Order: {{ $stV['order'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Evaluasi HR (Opsional)</label>
                <textarea name="stage_notes" rows="3" placeholder="Catatan hasil diskusi, poin kekuatan kandidat..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">{{ $application->stage_notes }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-update-stage').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                    Simpan Perubahan Tahap
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Schedule Interview -->
<div id="modal-schedule-interview" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Jadwalkan Wawancara</h3>
        <p class="text-xs text-slate-500 mb-6">Atur jadwal temu wawancara kerja untuk {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.interview', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Babak Interview*</label>
                <select name="round" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                    <option value="INTERVIEW_HR">Interview HR (Culture & Basic Competency)</option>
                    <option value="INTERVIEW_USER">Interview User / Hiring Manager</option>
                    <option value="INTERVIEW_BOD">Interview Direksi (BOD / Executive)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu & Tanggal Pertemuan*</label>
                <input type="datetime-local" name="interview_scheduled_at" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi / Tautan Meeting Online (Google Meet / Zoom)*</label>
                <input type="text" name="interview_location" required placeholder="Contoh: https://meet.google.com/xyz atau Ruang Rapat Lt. 4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pewawancara & Catatan Khusus</label>
                <input type="text" name="notes" placeholder="Contoh: Bersama Ibu Sarah (HR Manager)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                    Jadwalkan & Kirim Undangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Send Communication (WhatsApp / Email) -->
<div id="modal-send-message" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Kirim Pesan Komunikasi</h3>
        <p class="text-xs text-slate-500 mb-6">Kirimkan notifikasi resmi via WhatsApp atau Email ke {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.communicate', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Saluran (Channel)*</label>
                    <select name="channel" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="WHATSAPP">WhatsApp ({{ $application->applicant_phone }})</option>
                        <option value="EMAIL">Email ({{ $application->applicant_email }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Cepat</label>
                    <select onchange="applyTemplate(this.value)" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="">-- Pilih Template --</option>
                        <option value="shortlist">Undangan Psikotes Online</option>
                        <option value="interview">Undangan Sesi Interview</option>
                        <option value="offering">Pemberitahuan Offering Letter</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Subjek / Judul Pesan*</label>
                <input type="text" id="comm_subject" name="subject" required value="Undangan Seleksi - {{ $application->jobPosting->title }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Isi Pesan*</label>
                <textarea id="comm_message" name="message" rows="5" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">Halo {{ $application->applicant_name }}, terima kasih telah melamar posisi {{ $application->jobPosting->title }} di perusahaan kami.</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-send-message').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition-all">
                    Kirim Pesan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Modal Auto-Onboard to Core HR (Convert to Employee) -->
<div id="modal-convert-employee" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <div class="flex items-center space-x-3 mb-2">
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold flex items-center justify-center text-lg">🎉</span>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Convert to Core HR Employee</h3>
                <span class="text-xs text-emerald-700 font-semibold">Auto-Onboarding Karyawan Baru</span>
            </div>
        </div>
        <p class="text-xs text-slate-500 mb-6">
            Kandidat <strong class="text-slate-900">{{ $application->applicant_name }}</strong> akan secara otomatis dibuatkan berkas pegawai aktif di master Core HR dengan seluruh data profil yang telah dilengkapi.
        </p>

        <form action="{{ route('recruitment.applications.convert-employee', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Induk Karyawan (NIK)*</label>
                <input type="text" name="nik" value="EMP-{{ now()->format('Ym') }}-{{ str_pad((string)(\App\Models\Employee::count() + 1), 4, '0', STR_PAD_LEFT) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
                <span class="text-[10px] text-slate-400 mt-0.5 block">Format otomatis dapat disesuaikan bila perlu.</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Departemen*</label>
                    <select name="department_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $dept->id == $application->jobPosting->department_id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Posisi Jabatan*</label>
                    <select name="position_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Ikatan Kerja*</label>
                    <select name="employment_status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="PROBATION">Probation (Masa Percobaan)</option>
                        <option value="CONTRACT">Kontrak (PKWT)</option>
                        <option value="PERMANENT">Tetap (PKWTT)</option>
                        <option value="INTERNSHIP">Magang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai Bekerja (Join Date)*</label>
                    <input type="date" name="join_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-convert-employee').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all">
                    ✓ Konversi Resmi ke Core HR
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function applyTemplate(type) {
    const subjectEl = document.getElementById('comm_subject');
    const messageEl = document.getElementById('comm_message');
    const name = "{{ $application->applicant_name }}";
    const job = "{{ $application->jobPosting->title }}";

    if (type === 'shortlist') {
        subjectEl.value = `Undangan Psikotes Online - ${job}`;
        messageEl.value = `Halo ${name},\n\nSelamat! Berkas lamaran Anda untuk posisi ${job} telah berhasil lolos tahap screening. Silakan masuk ke Candidate Portal kami di ${window.location.origin}/career untuk mengikuti asesmen psikotes online.\n\nSalam,\nTim HR Recruitment`;
    } else if (type === 'interview') {
        subjectEl.value = `Undangan Wawancara Kerja - ${job}`;
        messageEl.value = `Halo ${name},\n\nKami mengundang Anda untuk mengikuti tahapan wawancara posisi ${job}. Detail waktu dan link pertemuan tercantum pada Candidate Portal Anda.\n\nSalam,\nTim HR Recruitment`;
    } else if (type === 'offering') {
        subjectEl.value = `Offering Letter & Onboarding - ${job}`;
        messageEl.value = `Halo ${name},\n\nSelamat! Anda terpilih untuk bergabung bersama kami pada posisi ${job}. Kami telah menerbitkan penawaran kerja resmi. Silakan hubungi tim HR untuk konfirmasi jadwal onboarding.\n\nSalam,\nTim HR Recruitment`;
    }
}
</script>

<!-- Modal Kurva Kraepelin Detail (HR Dossier View) -->
<div id="kraepelin-hr-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl space-y-5 my-auto max-h-[92vh] overflow-y-auto">
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 mb-2">
                    <span>⚡ KRAEPELIN NUMERIK</span>
                </div>
                <h3 class="text-lg font-black text-slate-900" id="hr-modal-candidate-name">Kurva Kerja Kraepelin</h3>
                <p class="text-xs text-slate-500 mt-0.5">Analisis ritme kerja, kecepatan, ketelitian, dan ketahanan kalkulasi per kolom.</p>
            </div>
            <button type="button" onclick="closeKraepelinModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-lg font-bold cursor-pointer transition-colors">
                ✕
            </button>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Panker (Kecepatan)</span>
                <span class="text-sm font-black text-slate-900 mt-0.5 block" id="hr-modal-panker">-</span>
                <span class="text-[10px] text-slate-500">Rata-rata hitungan/kolom</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Tianker (Ketelitian)</span>
                <span class="text-sm font-black text-emerald-600 mt-0.5 block" id="hr-modal-tianker">-</span>
                <span class="text-[10px] text-slate-500">Akurasi kalkulasi</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Hanker (Ketahanan)</span>
                <span class="text-sm font-black text-indigo-600 mt-0.5 block" id="hr-modal-hanker">-</span>
                <span class="text-[10px] text-slate-500">Tren kurva kerja</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Janker (Kestabilan)</span>
                <span class="text-sm font-black text-amber-600 mt-0.5 block" id="hr-modal-janker">-</span>
                <span class="text-[10px] text-slate-500">Rentang fluktuasi puncak</span>
            </div>
        </div>

        <!-- SVG Chart Container -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 px-1">
                <span>Grafik Kurva Kerja Kolom (1 - 30)</span>
                <span class="text-[11px] text-amber-600 font-semibold" id="hr-modal-avg-label">-</span>
            </div>
            <div id="hr-modal-chart-wrapper" class="w-full bg-white rounded-xl p-3 border border-slate-200/80 overflow-hidden">
                <!-- SVG injected via JS -->
            </div>
        </div>

        <!-- Column breakdown table -->
        <div>
            <div class="text-xs font-bold text-slate-800 mb-2">Tabel Rincian Kalkulasi Tiap Kolom:</div>
            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold sticky top-0">
                        <tr>
                            <th class="px-3 py-2">Kolom</th>
                            <th class="px-3 py-2 text-center">Hitungan</th>
                            <th class="px-3 py-2 text-center text-emerald-700">Benar</th>
                            <th class="px-3 py-2 text-center text-rose-700">Keliru</th>
                            <th class="px-3 py-2 text-right">Akurasi</th>
                        </tr>
                    </thead>
                    <tbody id="hr-modal-tbody" class="divide-y divide-slate-100 bg-white text-[11px]">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="closeKraepelinModal()" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openKraepelinModal(data, applicantName, score, status) {
        const modal = document.getElementById('kraepelin-hr-modal');
        if (!modal) return;

        document.getElementById('hr-modal-candidate-name').textContent = `Kurva Kraepelin: ${applicantName} (Skor: ${score}/100)`;

        const details = data.column_details || [];
        const metrics = data.metrics || {};
        const totalAttempted = data.total_attempted || 0;
        const accuracy = data.accuracy_rate || 0;
        const colCount = Math.max(1, data.columns_completed || details.length);

        const panker = metrics.panker || (totalAttempted / colCount).toFixed(1);
        const tianker = metrics.tianker || accuracy;
        const hanker = metrics.hanker_trend || 'Stabil (Konsisten)';
        const janker = metrics.janker !== undefined ? `±${metrics.janker}` : '-';

        document.getElementById('hr-modal-panker').textContent = `${panker} butir`;
        document.getElementById('hr-modal-tianker').textContent = `${tianker}%`;
        document.getElementById('hr-modal-hanker').textContent = hanker;
        document.getElementById('hr-modal-janker').textContent = janker;
        document.getElementById('hr-modal-avg-label').textContent = `Garis Panker: ${panker} butir/kolom`;

        // Render SVG Chart
        drawModalChart(details, 'hr-modal-chart-wrapper');

        // Render Table
        const tbody = document.getElementById('hr-modal-tbody');
        if (tbody) {
            if (details.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-3 py-4 text-center text-slate-400">Rincian per kolom belum tersedia untuk tes ini.</td></tr>`;
            } else {
                tbody.innerHTML = details.map(d => {
                    const attempted = d.attempted || d.rows_attempted || 0;
                    const correct = d.correct !== undefined ? d.correct : attempted;
                    const incorrect = d.incorrect !== undefined ? d.incorrect : 0;
                    const acc = attempted > 0 ? Math.round((correct / attempted) * 100) : 0;
                    return `<tr>
                        <td class="px-3 py-1.5 font-bold text-slate-800">Kolom ${d.column || (d.column_index + 1)}</td>
                        <td class="px-3 py-1.5 text-center font-bold text-blue-600">${attempted}</td>
                        <td class="px-3 py-1.5 text-center text-emerald-600 font-semibold">${correct}</td>
                        <td class="px-3 py-1.5 text-center text-rose-600 font-semibold">${incorrect}</td>
                        <td class="px-3 py-1.5 text-right font-bold text-slate-700">${acc}%</td>
                    </tr>`;
                }).join('');
            }
        }

        modal.classList.remove('hidden');
    }

    function closeKraepelinModal() {
        const modal = document.getElementById('kraepelin-hr-modal');
        if (modal) modal.classList.add('hidden');
    }

    function drawModalChart(details, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;
        if (!details || details.length === 0) {
            wrapper.innerHTML = `<div class="p-6 text-center text-xs text-slate-400">Data rincian kolom tidak tersedia.</div>`;
            return;
        }

        const n = details.length;
        const attemptedList = details.map(d => d.attempted || d.rows_attempted || 0);
        const maxVal = Math.max(15, ...attemptedList) + 3;
        const minVal = 0;
        const width = 740;
        const height = 210;
        const padL = 36;
        const padR = 24;
        const padT = 16;
        const padB = 28;
        const chartW = width - padL - padR;
        const chartH = height - padT - padB;

        const points = details.map((d, i) => {
            const att = d.attempted || d.rows_attempted || 0;
            const x = padL + (i / Math.max(1, n - 1)) * chartW;
            const y = padT + chartH - ((att - minVal) / (maxVal - minVal)) * chartH;
            return {
                x: Math.round(x * 10) / 10,
                y: Math.round(y * 10) / 10,
                col: d.column || (d.column_index + 1),
                attempted: att,
                correct: d.correct !== undefined ? d.correct : att,
                incorrect: d.incorrect !== undefined ? d.incorrect : 0
            };
        });

        const avg = attemptedList.reduce((s, v) => s + v, 0) / Math.max(1, n);
        const avgY = Math.round((padT + chartH - ((avg - minVal) / (maxVal - minVal)) * chartH) * 10) / 10;

        const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
        const areaPath = `${linePath} L ${points[points.length - 1].x} ${padT + chartH} L ${points[0].x} ${padT + chartH} Z`;

        const gridSteps = 4;
        let gridLinesSvg = '';
        for (let step = 0; step <= gridSteps; step++) {
            const val = Math.round(minVal + (step / gridSteps) * (maxVal - minVal));
            const gy = Math.round((padT + chartH - (step / gridSteps) * chartH) * 10) / 10;
            gridLinesSvg += `
                <line x1="${padL}" y1="${gy}" x2="${width - padR}" y2="${gy}" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3,3" />
                <text x="${padL - 6}" y="${gy + 3}" fill="#94a3b8" font-size="9" text-anchor="end" font-weight="600">${val}</text>
            `;
        }

        let pointsSvg = '';
        points.forEach((p) => {
            pointsSvg += `
                <g class="cursor-pointer">
                    <circle cx="${p.x}" cy="${p.y}" r="3" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                    <title>Kolom ${p.col}: ${p.attempted} hitungan (${p.correct} benar, ${p.incorrect} salah)</title>
                </g>
            `;
        });

        let xLabelsSvg = '';
        const labelInterval = n <= 10 ? 1 : (n <= 25 ? 2 : 5);
        for (let i = 0; i < n; i += labelInterval) {
            const p = points[i];
            xLabelsSvg += `
                <text x="${p.x}" y="${height - 8}" fill="#64748b" font-size="9" text-anchor="middle" font-weight="600">K${p.col}</text>
            `;
        }
        if ((n - 1) % labelInterval !== 0) {
            const lastP = points[n - 1];
            xLabelsSvg += `
                <text x="${lastP.x}" y="${height - 8}" fill="#64748b" font-size="9" text-anchor="middle" font-weight="600">K${lastP.col}</text>
            `;
        }

        wrapper.innerHTML = `
            <svg viewBox="0 0 ${width} ${height}" class="w-full h-auto select-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="hrKraepelinGrad2" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.01" />
                    </linearGradient>
                </defs>
                ${gridLinesSvg}
                <line x1="${padL}" y1="${avgY}" x2="${width - padR}" y2="${avgY}" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4,4" />
                <text x="${width - padR}" y="${avgY - 4}" fill="#d97706" font-size="9" font-weight="bold" text-anchor="end">Panker (Rata-rata): ${avg.toFixed(1)}</text>
                <path d="${areaPath}" fill="url(#hrKraepelinGrad2)" />
                <path d="${linePath}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                ${pointsSvg}
                ${xLabelsSvg}
            </svg>
        `;
    }
</script>
@endsection
