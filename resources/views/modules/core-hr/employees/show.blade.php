@extends('layouts.app')

@section('header', 'Profil Karyawan: ' . $employee->full_name)

@section('actions')
    <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 shadow-xs transition-colors">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        <span>Daftar Karyawan</span>
    </a>

    <!-- Tombol Cepat Ganti Password -->
    <button type="button" onclick="openPasswordModal()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-sm font-semibold rounded-xl border border-amber-200/80 shadow-xs transition-colors cursor-pointer">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
        </svg>
        <span>Ganti Password</span>
    </button>

    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        <span>Edit Data</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <span class="font-bold block text-emerald-900">Operasi Berhasil</span>
                    <span class="text-xs text-emerald-700">{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold p-1">
                &times;
            </button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-1 text-rose-900">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Hero Card (Strict Modern Light Theme) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <!-- Top decorative banner with clean light gradient -->
        <div class="h-32 bg-gradient-to-r from-indigo-100 via-slate-100 to-indigo-50 px-6 sm:px-8 border-b border-slate-200/60 relative">
            <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </div>

        <div class="px-6 sm:px-8 pb-6 pt-0 relative flex flex-col md:flex-row md:items-end md:justify-between gap-6 -mt-14">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
                <!-- Avatar -->
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white font-extrabold text-2xl flex items-center justify-center border-4 border-white shadow-md shrink-0">
                    {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $employee->full_name }}</h1>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $employee->employment_status === 'PKWTT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ $employee->employment_status }}
                        </span>

                        @php
                            $stage = $employee->lifecycle_stage ?? 'ACTIVE';
                            $stageBadge = match($stage) {
                                'ONBOARDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'SUSPENDED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'OFFBOARDING' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'TERMINATED' => 'bg-slate-100 text-slate-700 border-slate-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $stageBadge }}">
                            Stage: {{ $stage }}
                        </span>

                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                            {{ $employee->is_active ? 'Karyawan Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs sm:text-sm text-slate-600 font-medium">
                        <span class="font-bold text-slate-800">{{ $employee->position?->title ?? 'Belum ada jabatan' }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-indigo-600 font-semibold">{{ $employee->department?->name ?? 'Belum ada divisi' }}</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-500 font-mono font-semibold">NIK: {{ $employee->nik }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Contacts & Manager -->
            <div class="flex flex-col sm:flex-row md:flex-col gap-2 text-xs text-slate-600 border-t md:border-t-0 border-slate-100 pt-3 md:pt-0">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $employee->email }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="font-mono">{{ $employee->phone_number ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2 text-slate-700">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Lokasi: <strong class="text-slate-900">{{ $employee->work_location ?? 'Kantor Pusat' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Life-Cycle Stage Visual Stepper (STRICT LIGHT THEME) -->
        <div class="px-6 sm:px-8 py-3.5 bg-slate-50 border-t border-slate-200/80">
            <div class="flex items-center justify-between text-xs mb-2.5">
                <span class="font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full {{ $stage === 'ACTIVE' ? 'bg-emerald-500 animate-pulse' : 'bg-indigo-500' }}"></span>
                    Siklus Hidup Pegawai (Employee Life-Cycle Tracker)
                </span>
                <span class="text-xs text-slate-500 font-medium">
                    Fase Terkini: <strong class="text-indigo-700 font-bold">{{ $stage }}</strong>
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs font-semibold">
                @php
                    $stages = [
                        'ONBOARDING' => '1. Onboarding',
                        'ACTIVE' => '2. Active Working',
                        'SUSPENDED' => '3. Suspended',
                        'OFFBOARDING' => '4. Offboarding',
                        'TERMINATED' => '5. Terminated'
                    ];
                @endphp
                @foreach($stages as $key => $label)
                    @php
                        $isCurrent = ($stage === $key);
                    @endphp
                    <div class="py-2 px-2 rounded-xl transition-all border {{ $isCurrent ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200' }}">
                        <span class="block truncate">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Dynamic Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 border-t border-slate-200/80 bg-white divide-y lg:divide-y-0 lg:divide-x divide-slate-100 text-center">
            <div class="p-4">
                <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Usia Karyawan</span>
                <span class="text-base sm:text-lg font-bold text-slate-800 mt-0.5 block">
                    {{ $employee->age ? $employee->age . ' Tahun' : '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Masa Kerja</span>
                <span class="text-base sm:text-lg font-bold text-indigo-600 mt-0.5 block">
                    {{ $employee->tenure ?? '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">TMT Bergabung</span>
                <span class="text-base sm:text-lg font-bold text-slate-800 mt-0.5 block">
                    {{ $employee->join_date ? $employee->join_date->translatedFormat('d M Y') : '-' }}
                </span>
            </div>
            <div class="p-4">
                <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Saldo Cuti Tahunan</span>
                <span class="text-base sm:text-lg font-bold text-emerald-600 mt-0.5 block">
                    {{ $employee->remaining_annual_leave }} <span class="text-xs font-medium text-slate-500">Hari Tersisa</span>
                </span>
            </div>
            <div class="p-4 col-span-2 lg:col-span-1">
                <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">Akun Login Sistem</span>
                <div class="mt-0.5 flex items-center justify-center gap-1.5">
                    @if ($employee->user)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Belum Ada Akun
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- TABBED NAVIGATION BAR (SEMUA TABEL DATA EMPLOYEE) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-1.5">
        <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5" id="profile-tabs" aria-label="Tabs">
            <button type="button" onclick="switchTab('tab-personal')" id="btn-tab-personal" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap bg-indigo-600 text-white shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Identitas & Pajak</span>
            </button>

            <button type="button" onclick="switchTab('tab-career')" id="btn-tab-career" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span>Karir & Mutasi</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->careerHistories->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-contracts')" id="btn-tab-contracts" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Kontrak Kerja</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->contracts->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-education')" id="btn-tab-education" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
                <span>Pendidikan</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->educations->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-attendance')" id="btn-tab-attendance" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Presensi / Absensi</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->attendances->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-leave')" id="btn-tab-leave" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Cuti & Izin</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->leaveRequests->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-overtime')" id="btn-tab-overtime" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                </svg>
                <span>Lembur</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->overtimeRequests->count() }}</span>
            </button>

            <button type="button" onclick="switchTab('tab-schedules')" id="btn-tab-schedules" 
                    class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Jadwal Shift</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->schedules->count() }}</span>
            </button>

            @if ($employee->subordinates->isNotEmpty())
                <button type="button" onclick="switchTab('tab-subordinates')" id="btn-tab-subordinates" 
                        class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Bawahan Langsung</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $employee->subordinates->count() }}</span>
                </button>
            @endif

            @if ($jobApplications->isNotEmpty())
                <button type="button" onclick="switchTab('tab-recruitment')" id="btn-tab-recruitment" 
                        class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Histori Rekrutmen</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">{{ $jobApplications->count() }}</span>
                </button>
            @endif
        </nav>
    </div>

    <!-- TAB CONTENTS -->

    <!-- ===================================================================== -->
    <!-- TAB 1: IDENTITAS PERSONAL, PAJAK & AKUN LOGIN -->
    <!-- ===================================================================== -->
    <div id="tab-personal" class="tab-content space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Cols: Personal Identity & Tax Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Data Identitas Personal & KTP -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                            <span>Data Identitas Kependudukan & Personal</span>
                        </h3>
                        <span class="text-xs text-slate-400 font-mono">ID: #{{ $employee->id }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                            <span class="text-xs text-slate-400 font-medium block">Nomor KTP (NIK Kependudukan)</span>
                            <span class="font-mono font-bold text-slate-900 text-base mt-0.5 block">{{ $employee->ktp_number }}</span>
                        </div>
                        <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                            <span class="text-xs text-slate-400 font-medium block">Nomor Induk Karyawan (NIK Internal)</span>
                            <span class="font-mono font-bold text-indigo-700 text-base mt-0.5 block">{{ $employee->nik }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">Jenis Kelamin</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->gender === 'MALE' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">Tanggal Lahir & Usia</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">
                                {{ $employee->birth_date ? $employee->birth_date->translatedFormat('d F Y') : '-' }}
                                @if($employee->age)
                                    <span class="text-xs text-indigo-600 font-normal">({{ $employee->age }} thn)</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">Agama</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->religion ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">Status Pernikahan</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $employee->marital_status ?? 'SINGLE' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-xs text-slate-400 font-medium block">Alamat Sesuai KTP</span>
                            <span class="font-medium text-slate-800 mt-0.5 block leading-relaxed">{{ $employee->ktp_address ?? '-' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-xs text-slate-400 font-medium block">Alamat Domisili Saat Ini</span>
                            <span class="font-medium text-slate-800 mt-0.5 block leading-relaxed">{{ $employee->current_address ?? 'Sesuai dengan alamat KTP' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Data Pajak & Payroll -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                    <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Pajak (PPh 21 TER), BPJS & Rekening Bank Payroll</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 bg-amber-50/70 border border-amber-200/70 rounded-xl">
                            <span class="text-xs text-amber-800 font-bold uppercase tracking-wider block">Status PTKP (Kunci Pajak TER)</span>
                            <span class="text-2xl font-extrabold text-amber-900 mt-1 block">{{ $employee->ptkp_status ?? 'TK/0' }}</span>
                            <span class="text-[11px] text-amber-700 block mt-1">Dasar klasifikasi tarif efektif rata-rata TER bulanan</span>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200/70 rounded-xl">
                            <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider block">Nomor Pokok Wajib Pajak (NPWP)</span>
                            <span class="font-mono font-bold text-slate-900 text-lg mt-1 block">{{ $employee->npwp ?? 'Belum ada NPWP' }}</span>
                            <span class="text-[11px] text-slate-500 block mt-1">Validasi status perpajakan DJP</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 font-medium block">BPJS Ketenagakerjaan (TK)</span>
                            <span class="font-mono font-semibold text-slate-800 text-sm mt-0.5 block">{{ $employee->bpjs_ketenagakerjaan_no ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 font-medium block">BPJS Kesehatan</span>
                            <span class="font-mono font-semibold text-slate-800 text-sm mt-0.5 block">{{ $employee->bpjs_kesehatan_no ?? '-' }}</span>
                        </div>

                        <div class="sm:col-span-2 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                            <span class="text-xs text-indigo-700 font-bold uppercase tracking-wider block">Rekening Payroll Karyawan</span>
                            <div class="mt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <div class="font-bold text-slate-900 text-base">{{ $employee->bank_name ?? 'Belum ditentukan' }}</div>
                                    <div class="font-mono text-indigo-700 font-extrabold text-lg">{{ $employee->bank_account_number ?? '-' }}</div>
                                </div>
                                <div class="text-xs text-slate-600 sm:text-right">
                                    <span class="block text-slate-400">Atas Nama:</span>
                                    <span class="font-bold text-slate-800">{{ $employee->bank_account_holder ?? $employee->full_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Fields (JSONB) jika ada -->
                @if ($employee->custom_fields && count($employee->custom_fields) > 0)
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Atribut Kustomisasi Tambahan (JSONB)</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                            @foreach ($employee->custom_fields as $key => $val)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                    <span class="text-[11px] text-slate-400 font-semibold block uppercase">{{ str_replace('_', ' ', $key) }}</span>
                                    <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: User Account Management & Password Feature -->
            <div class="space-y-6">
                <!-- User Account & Password Box -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Akun Login Pegawai</span>
                        </h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $employee->user ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $employee->user ? 'Aktif' : 'Unlinked' }}
                        </span>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="p-3.5 bg-slate-50 border border-slate-200/70 rounded-xl space-y-1">
                            <span class="text-[11px] text-slate-400 font-medium block uppercase tracking-wider">Email Akun Sistem</span>
                            <span class="font-semibold text-slate-900 text-sm block truncate">{{ $employee->user?->email ?? $employee->email }}</span>
                            <span class="text-xs text-slate-500 block">
                                Tipe Akses: <strong class="text-indigo-600">{{ $employee->user?->user_type ?? 'EMPLOYEE (Default)' }}</strong>
                            </span>
                        </div>

                        @if ($employee->user)
                            <div class="text-xs text-slate-500 space-y-1">
                                <div>Status Verifikasi: <strong class="text-slate-800">{{ $employee->user->email_verified_at ? 'Terverifikasi' : 'Belum Diverifikasi' }}</strong></div>
                                <div>ID Akun User: <span class="font-mono text-slate-700">#{{ $employee->user->id }}</span></div>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl text-xs text-amber-800">
                                Karyawan belum memiliki akun user login aktif. Mengatur password di bawah akan otomatis membuatkan akun login pegawai untuk email ini.
                            </div>
                        @endif

                        <!-- In-Card Quick Password Change Form -->
                        <div class="pt-2 border-t border-slate-100">
                            <button type="button" onclick="openPasswordModal()" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <span>Ganti / Reset Password Karyawan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Struktur Organisasi & Atasan -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                    <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Hirarki & Manajemen</span>
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 font-medium block">Atasan Langsung (Direct Manager)</span>
                            @if ($employee->manager)
                                <a href="{{ route('employees.show', $employee->manager) }}" class="font-bold text-indigo-600 hover:underline mt-0.5 block">
                                    {{ $employee->manager->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $employee->manager->position?->title ?? 'Atasan' }} ({{ $employee->manager->nik }})</span>
                            @else
                                <span class="font-semibold text-slate-700 mt-0.5 block">Top Management / Direksi (Tanpa Atasan)</span>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-xs text-slate-400 font-medium block">Total Staf Bawahan Langsung</span>
                            <span class="font-bold text-slate-900 mt-0.5 block">
                                {{ $employee->subordinates->count() }} Orang
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 2: KARIR & MUTASI -->
    <!-- ===================================================================== -->
    <div id="tab-career" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-base">Riwayat Perjalanan Karir & Mutasi Pegawai</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $employee->careerHistories->count() }} Peristiwa
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan resmi promosi jabatan, mutasi unit kerja, demosi, atau perubahan status kepegawaian berdasar SK</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('form-add-career').classList.toggle('hidden')" 
                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Catat Mutasi / Promosi</span>
                </button>
            </div>

            <!-- Form Tambah Mutasi Karir -->
            <div id="form-add-career" class="hidden p-6 bg-slate-50 border-b border-slate-200/80">
                <form action="{{ route('employees.careers.store', $employee) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pergerakan Karir *</label>
                            <select name="transition_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="PROMOTION">PROMOTION (Kenaikan Jabatan / Level)</option>
                                <option value="MUTATION">MUTATION (Pindah Unit / Divisi / Cabang)</option>
                                <option value="DEMOTION">DEMOTION (Penyesuaian Tanggung Jawab)</option>
                                <option value="JOIN">JOIN (Penetapan / Onboarding Awal)</option>
                                <option value="RESIGN">RESIGN (Pengunduran Diri)</option>
                                <option value="TERMINATE">TERMINATE (PHK / Pensiun)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku SK *</label>
                            <input type="date" name="effective_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Surat Keputusan (SK)</label>
                            <input type="text" name="reference_doc_no" placeholder="Contoh: SK/045/DIR-HC/X/2026" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Unit Kerja / Departemen Baru</label>
                            <select name="new_department_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="">— Tetap di Unit Saat Ini —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan / Posisi Baru</label>
                            <select name="new_position_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="">— Tetap di Jabatan Saat Ini —</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->title }} ({{ $pos->level ?? 'Staff' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Update Status Life-Cycle</label>
                            <select name="lifecycle_stage" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="ACTIVE" {{ $employee->lifecycle_stage === 'ACTIVE' ? 'selected' : '' }}>ACTIVE (Aktif Bekerja)</option>
                                <option value="ONBOARDING" {{ $employee->lifecycle_stage === 'ONBOARDING' ? 'selected' : '' }}>ONBOARDING (Orientasi)</option>
                                <option value="SUSPENDED" {{ $employee->lifecycle_stage === 'SUSPENDED' ? 'selected' : '' }}>SUSPENDED (Skorsing)</option>
                                <option value="OFFBOARDING" {{ $employee->lifecycle_stage === 'OFFBOARDING' ? 'selected' : '' }}>OFFBOARDING (Clearance)</option>
                                <option value="TERMINATED" {{ $employee->lifecycle_stage === 'TERMINATED' ? 'selected' : '' }}>TERMINATED (Alumni)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Catatan Perubahan Karir</label>
                        <input type="text" name="notes" placeholder="Contoh: Kenaikan jabatan atas kinerja gemilang Q3..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('form-add-career').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl shadow-xs hover:bg-indigo-700">Simpan SK Karir</button>
                    </div>
                </form>
            </div>

            <!-- Tabel Karir -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Tipe Pergerakan</th>
                            <th class="py-3 px-5">Tanggal Berlaku SK</th>
                            <th class="py-3 px-5">Nomor SK / Ref</th>
                            <th class="py-3 px-5">Perubahan Jabatan</th>
                            <th class="py-3 px-5">Perubahan Unit Kerja</th>
                            <th class="py-3 px-5">Catatan</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->careerHistories as $career)
                            @php
                                $typeBadge = match($career->transition_type) {
                                    'PROMOTION' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'MUTATION' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'DEMOTION' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'JOIN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'RESIGN', 'TERMINATE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $typeBadge }}">
                                        {{ $career->transition_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-semibold text-slate-800 whitespace-nowrap">
                                    {{ $career->effective_date ? $career->effective_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-600">
                                    {{ $career->reference_doc_no ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    @if ($career->oldPosition || $career->newPosition)
                                        <span class="text-slate-500">{{ $career->oldPosition?->title ?? 'Awal' }}</span>
                                        <span class="text-slate-400 mx-1">&rarr;</span>
                                        <span class="font-bold text-indigo-700">{{ $career->newPosition?->title ?? $career->oldPosition?->title }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    @if ($career->oldDepartment || $career->newDepartment)
                                        <span class="text-slate-500">{{ $career->oldDepartment?->name ?? 'Awal' }}</span>
                                        <span class="text-slate-400 mx-1">&rarr;</span>
                                        <span class="font-bold text-indigo-700">{{ $career->newDepartment?->name ?? $career->oldDepartment?->name }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-500 italic max-w-xs truncate">
                                    {{ $career->notes ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <form action="{{ route('employees.careers.destroy', [$employee, $career]) }}" method="POST" onsubmit="return confirm('Hapus histori mutasi karir ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada rekam jejak mutasi atau promosi karir untuk karyawan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 3: KONTRAK KERJA (PKWT / PKWTT) -->
    <!-- ===================================================================== -->
    <div id="tab-contracts" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-base">Histori Dokumen & Perjanjian Kerja (PKWT/PKWTT)</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $employee->contracts->count() }} Dokumen
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Mencatat riwayat kontrak, adendum, atau surat perjanjian kerja tanpa menimpa data sebelumnya</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('form-add-contract').classList.toggle('hidden')" 
                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Tambah Kontrak Baru</span>
                </button>
            </div>

            <!-- Form Tambah Kontrak -->
            <div id="form-add-contract" class="hidden p-6 bg-slate-50 border-b border-slate-200/80">
                <form action="{{ route('employees.contracts.store', $employee) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Dokumen Kontrak *</label>
                            <input type="text" name="contract_number" required placeholder="Contoh: 002/PKWT-EXT/2026" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Kontrak *</label>
                            <select name="contract_type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="PKWT-1">PKWT 1 (Tahun Pertama)</option>
                                <option value="PKWT-2">PKWT 2 (Perpanjangan)</option>
                                <option value="PKWTT">PKWTT (Pegawai Tetap)</option>
                                <option value="PROBATION">Probation (Masa Percobaan)</option>
                                <option value="MAGANG">Magang / Internship</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku *</label>
                            <input type="date" name="start_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Berakhir (Kosongkan jika PKWTT)</label>
                            <input type="date" name="end_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <input type="text" name="notes" placeholder="Keterangan pasal penting atau kesepakatan perpanjangan..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('form-add-contract').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl shadow-xs hover:bg-indigo-700">Simpan Kontrak</button>
                    </div>
                </form>
            </div>

            <!-- Tabel Kontrak -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Nomor Kontrak</th>
                            <th class="py-3 px-5">Jenis Kontrak</th>
                            <th class="py-3 px-5">Tanggal Mulai</th>
                            <th class="py-3 px-5">Tanggal Berakhir</th>
                            <th class="py-3 px-5">Status Berlaku</th>
                            <th class="py-3 px-5">Catatan</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->contracts as $contract)
                            @php
                                $isCurrent = false;
                                if ($contract->start_date && $contract->start_date <= now()) {
                                    if (! $contract->end_date || $contract->end_date >= now()) {
                                        $isCurrent = true;
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-slate-900 font-mono text-xs">
                                    {{ $contract->contract_number }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $contract->contract_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700 font-medium">
                                    {{ $contract->start_date ? $contract->start_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700 font-medium">
                                    {{ $contract->end_date ? $contract->end_date->translatedFormat('d M Y') : 'Seterusnya (PKWTT)' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($isCurrent)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif Berjalan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
                                            Histori
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-500 italic max-w-xs truncate">
                                    {{ $contract->notes ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <form action="{{ route('employees.contracts.destroy', [$employee, $contract]) }}" method="POST" onsubmit="return confirm('Hapus dokumen kontrak ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada rekam dokumen kontrak kerja.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 4: PENDIDIKAN FORMAL -->
    <!-- ===================================================================== -->
    <div id="tab-education" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-base">Riwayat Pendidikan Formal Pegawai</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $employee->educations->count() }} Data
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Membedakan kualifikasi pendidikan yang diakui perusahaan vs kualifikasi tambahan yang dimiliki</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('form-add-education').classList.toggle('hidden')" 
                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Tambah Pendidikan</span>
                </button>
            </div>

            <!-- Form Tambah Pendidikan -->
            <div id="form-add-education" class="hidden p-6 bg-slate-50 border-b border-slate-200/80">
                <form action="{{ route('employees.educations.store', $employee) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenjang Pendidikan *</label>
                            <select name="education_level" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                                <option value="SMA/SMK">SMA / SMK</option>
                                <option value="D3">D3 (Diploma)</option>
                                <option value="D4">D4 (Sarjana Terapan)</option>
                                <option value="S1" selected>S1 (Sarjana)</option>
                                <option value="S2">S2 (Magister)</option>
                                <option value="S3">S3 (Doktor)</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan / Program Studi *</label>
                            <input type="text" name="major" required placeholder="Contoh: Manajemen, Teknik Informatika" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perguruan Tinggi / Sekolah *</label>
                            <input type="text" name="institution_name" required placeholder="Contoh: Universitas Diponegoro" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Kelulusan</label>
                            <input type="number" name="graduation_year" min="1960" max="{{ date('Y') + 5 }}" placeholder="{{ date('Y') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_recognized" name="is_recognized" value="1" checked class="w-4 h-4 text-indigo-600 rounded">
                        <label for="is_recognized" class="text-xs font-semibold text-slate-800">
                            Pendidikan Diakui Perusahaan (Menjadi acuan dasar grading & kualifikasi karir)
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('form-add-education').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-xl shadow-xs hover:bg-indigo-700">Simpan Pendidikan</button>
                    </div>
                </form>
            </div>

            <!-- Tabel Pendidikan -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Jenjang</th>
                            <th class="py-3 px-5">Institusi / Perguruan Tinggi</th>
                            <th class="py-3 px-5">Jurusan / Program Studi</th>
                            <th class="py-3 px-5">Tahun Lulus</th>
                            <th class="py-3 px-5">Status Pengakuan</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->educations as $edu)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $edu->education_level }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-bold text-slate-800 text-sm">
                                    {{ $edu->institution_name }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-700 font-medium">
                                    {{ $edu->major }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 font-mono text-xs">
                                    {{ $edu->graduation_year ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($edu->is_recognized)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Diakui Perusahaan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            Dimiliki Pribadi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <form action="{{ route('employees.educations.destroy', [$employee, $edu]) }}" method="POST" onsubmit="return confirm('Hapus riwayat pendidikan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada catatan riwayat pendidikan formal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 5: PRESENSI & ABSENSI (ATTENDANCE LOGS) -->
    <!-- ===================================================================== -->
    <div id="tab-attendance" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900 text-base">Log Presensi & Rekam Kehadiran Harian</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $employee->attendances->count() }} Data Terbaru
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Mencatat clock-in, clock-out, jam kerja aktual, serta status keterlambatan</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Maks. 30 log terakhir</span>
                </div>
            </div>

            <!-- Tabel Presensi -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Tanggal Presensi</th>
                            <th class="py-3 px-5">Shift Kerja</th>
                            <th class="py-3 px-5">Clock In</th>
                            <th class="py-3 px-5">Clock Out</th>
                            <th class="py-3 px-5">Durasi Kerja</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5">Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->attendances as $att)
                            @php
                                $statusBadge = match(strtoupper($att->status ?? 'PRESENT')) {
                                    'PRESENT', 'HADIR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'LATE', 'TERLAMBAT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'ABSENT', 'ALPHA' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'LEAVE', 'CUTI' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $att->date ? $att->date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    <span class="font-semibold">{{ $att->shift?->name ?? 'Default Shift' }}</span>
                                    @if ($att->shift)
                                        <span class="text-slate-400 block text-[11px]">{{ $att->shift->start_time }} - {{ $att->shift->end_time }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-800 font-semibold">
                                    {{ $att->clock_in ? $att->clock_in->format('H:i:s') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-800 font-semibold">
                                    {{ $att->clock_out ? $att->clock_out->format('H:i:s') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700 font-medium">
                                    @if ($att->total_work_minutes)
                                        {{ floor($att->total_work_minutes / 60) }} jam {{ $att->total_work_minutes % 60 }} mnt
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadge }}">
                                        {{ $att->status ?? 'HADIR' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-xs">
                                    @if ($att->late_minutes && $att->late_minutes > 0)
                                        <span class="font-semibold text-rose-600">+{{ $att->late_minutes }} menit</span>
                                    @else
                                        <span class="text-emerald-600 font-semibold">Tepat Waktu</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada data presensi harian yang tercatat untuk karyawan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 6: CUTI & IZIN (LEAVE REQUESTS) -->
    <!-- ===================================================================== -->
    <div id="tab-leave" class="tab-content hidden space-y-6">
        <!-- Leave Quota Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Total Kuota Cuti Tahunan</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $employee->annual_leave_quota ?? 12 }} Hari</span>
                <span class="text-xs text-slate-500 mt-1 block">Hak cuti reguler tahun berjalan</span>
            </div>
            <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-xs text-amber-700 font-bold uppercase tracking-wider block">Cuti Terpakai</span>
                <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $employee->annual_leave_used ?? 0 }} Hari</span>
                <span class="text-xs text-slate-500 mt-1 block">Total hari cuti yang disetujui</span>
            </div>
            <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
                <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider block">Sisa Saldo Cuti</span>
                <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $employee->remaining_annual_leave }} Hari</span>
                <span class="text-xs text-slate-500 mt-1 block">Tersedia untuk pengajuan mendatang</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Riwayat Permohonan Cuti & Izin</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Semua pengajuan cuti, izin sakit, dan dispensasi beserta status persetujuan atasan</p>
                </div>
            </div>

            <!-- Tabel Cuti -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Tipe Cuti / Izin</th>
                            <th class="py-3 px-5">Periode Tanggal</th>
                            <th class="py-3 px-5">Durasi (Hari)</th>
                            <th class="py-3 px-5">Alasan / Catatan</th>
                            <th class="py-3 px-5">Penyetuju (Approver)</th>
                            <th class="py-3 px-5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->leaveRequests as $leave)
                            @php
                                $leaveBadge = match(strtoupper($leave->status ?? 'PENDING')) {
                                    'APPROVED', 'DISETUJUI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'REJECTED', 'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'CANCELLED' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-slate-900">
                                    {{ $leave->leaveType?->name ?? $leave->type ?? 'Cuti Tahunan' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700 font-medium whitespace-nowrap">
                                    {{ $leave->start_date ? $leave->start_date->translatedFormat('d M Y') : '-' }}
                                    s/d
                                    {{ $leave->end_date ? $leave->end_date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-bold text-indigo-700 text-xs">
                                    {{ $leave->total_days }} Hari
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600 max-w-xs truncate">
                                    {{ $leave->reason ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    {{ $leave->approver?->full_name ?? 'HR / Management' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $leaveBadge }}">
                                        {{ $leave->status ?? 'PENDING' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada permohonan cuti atau izin yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 7: LEMBUR (OVERTIME REQUESTS) -->
    <!-- ===================================================================== -->
    <div id="tab-overtime" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Riwayat Permohonan Kerja Lembur</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Surat perintah dan pengajuan lembur yang telah diajukan beserta jam kerja lembur aktual</p>
                </div>
            </div>

            <!-- Tabel Lembur -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Tanggal Lembur</th>
                            <th class="py-3 px-5">Waktu Mulai</th>
                            <th class="py-3 px-5">Waktu Selesai</th>
                            <th class="py-3 px-5">Total Durasi</th>
                            <th class="py-3 px-5">Uraian Tugas / Alasan</th>
                            <th class="py-3 px-5">Penyetuju</th>
                            <th class="py-3 px-5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->overtimeRequests as $ot)
                            @php
                                $otBadge = match(strtoupper($ot->status ?? 'PENDING')) {
                                    'APPROVED', 'DISETUJUI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'REJECTED', 'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $ot->date ? $ot->date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-800">
                                    {{ $ot->start_time ? $ot->start_time->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-800">
                                    {{ $ot->end_time ? $ot->end_time->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-5 font-bold text-indigo-700 text-xs">
                                    {{ $ot->total_hours }} Jam
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-600 max-w-xs truncate">
                                    {{ $ot->reason ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    {{ $ot->approver?->full_name ?? 'Atasan / HR' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $otBadge }}">
                                        {{ $ot->status ?? 'PENDING' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada rekam permohonan lembur untuk karyawan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 8: JADWAL SHIFT KERJA (ROSTER) -->
    <!-- ===================================================================== -->
    <div id="tab-schedules" class="tab-content hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Jadwal Shift Kerja & Penempatan Roster</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penetapan shift berkala serta status hari libur kerja karyawan</p>
                </div>
            </div>

            <!-- Tabel Jadwal -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                            <th class="py-3 px-5">Tanggal Penugasan</th>
                            <th class="py-3 px-5">Kode / Nama Shift</th>
                            <th class="py-3 px-5">Jam Kerja Shift</th>
                            <th class="py-3 px-5">Lokasi Kerja</th>
                            <th class="py-3 px-5">Status Hari</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employee->schedules as $sched)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-bold text-slate-900 whitespace-nowrap">
                                    {{ $sched->date ? $sched->date->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="font-bold text-slate-800 text-xs">{{ $sched->shift?->name ?? 'Default Shift' }}</span>
                                    @if ($sched->shift?->code)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-slate-100 text-slate-600 ml-1">({{ $sched->shift->code }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 font-mono text-xs text-slate-700">
                                    {{ $sched->shift?->start_time ?? '-' }} s/d {{ $sched->shift?->end_time ?? '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-xs text-slate-700">
                                    {{ $sched->officeLocation?->name ?? $employee->work_location ?? 'Kantor Utama' }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($sched->is_day_off)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Hari Libur (Off)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Masuk Bekerja
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada penugasan shift roster khusus untuk karyawan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 9: BAWAHAN LANGSUNG (SUBORDINATES) JIKA MANAGER -->
    <!-- ===================================================================== -->
    @if ($employee->subordinates->isNotEmpty())
        <div id="tab-subordinates" class="tab-content hidden space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200/80">
                    <h3 class="font-bold text-slate-900 text-base">Bawahan Langsung (Direct Reports)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar staf dan pegawai yang melapor langsung secara struktural ke {{ $employee->full_name }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                                <th class="py-3 px-5">NIK</th>
                                <th class="py-3 px-5">Nama Karyawan</th>
                                <th class="py-3 px-5">Jabatan</th>
                                <th class="py-3 px-5">Unit / Divisi</th>
                                <th class="py-3 px-5">Status Kepegawaian</th>
                                <th class="py-3 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($employee->subordinates as $sub)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-5 font-mono font-bold text-xs text-indigo-700">
                                        {{ $sub->nik }}
                                    </td>
                                    <td class="py-3.5 px-5 font-bold text-slate-900">
                                        <a href="{{ route('employees.show', $sub) }}" class="hover:text-indigo-600 hover:underline">
                                            {{ $sub->full_name }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-700 text-xs">
                                        {{ $sub->position?->title ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600 text-xs">
                                        {{ $sub->department?->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sub->employment_status === 'PKWTT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $sub->employment_status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <a href="{{ route('employees.show', $sub) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                            Lihat Profil &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- TAB 10: HISTORI REKRUTMEN (JIKA DARI ATS / PELAMAR) -->
    <!-- ===================================================================== -->
    @if ($jobApplications->isNotEmpty())
        <div id="tab-recruitment" class="tab-content hidden space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200/80">
                    <h3 class="font-bold text-slate-900 text-base">Rekam Jejak Rekrutmen & Seleksi Pelamar</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Histori pendaftaran lowongan, tahapan evaluasi rekrutmen, penawaran (offering), dan hasil psikotes awal</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($jobApplications as $app)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base">
                                        {{ $app->jobPosting?->title ?? 'Posisi Rekrutmen Terbuka' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Tanggal Melamar: <strong class="text-slate-700">{{ $app->applied_at ? $app->applied_at->translatedFormat('d F Y H:i') : '-' }}</strong>
                                        @if ($app->hired_at)
                                            &bull; Diterima Bekerja: <strong class="text-emerald-700">{{ $app->hired_at->translatedFormat('d F Y') }}</strong>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $app->current_stage === 'HIRED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        Tahap: {{ $app->current_stage }}
                                    </span>
                                </div>
                            </div>

                            @if ($app->stage_notes)
                                <div class="p-3 bg-slate-50 border border-slate-200/70 rounded-xl text-xs text-slate-700">
                                    <span class="font-bold text-slate-800 block mb-0.5">Catatan Evaluasi Rekruter:</span>
                                    <span>{{ $app->stage_notes }}</span>
                                </div>
                            @endif

                            <!-- Hasil Psikotes Jika Ada -->
                            @if ($app->psychotestResults && $app->psychotestResults->isNotEmpty())
                                <div class="space-y-2 pt-2">
                                    <span class="text-xs font-bold text-slate-800 block">Hasil Tes Psikotes yang Diikuti:</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach ($app->psychotestResults as $res)
                                            <div class="p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs space-y-1">
                                                <div class="font-bold text-slate-900">{{ $res->psychotest?->name ?? 'Psikotes' }}</div>
                                                <div class="text-slate-600">Skor: <strong class="text-indigo-700 font-bold text-sm">{{ $res->total_score ?? '-' }}</strong></div>
                                                <div class="text-[11px] text-slate-500">Status: {{ $res->status ?? 'COMPLETED' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>

<!-- ========================================================================= -->
<!-- MODAL GANTI PASSWORD KARYAWAN -->
<!-- ========================================================================= -->
<div id="modal-password" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs hidden transition-all">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Ubah Password Karyawan</h3>
                    <p class="text-xs text-slate-500">Perbarui kredensial akun login sistem pegawai</p>
                </div>
            </div>
            <button type="button" onclick="closePasswordModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>
        </div>

        <form action="{{ route('employees.password.update', $employee) }}" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- User Context Badge -->
            <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-1 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Pegawai:</span>
                    <strong class="text-slate-900">{{ $employee->full_name }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Email Login:</span>
                    <strong class="text-indigo-600 font-mono">{{ $employee->user?->email ?? $employee->email }}</strong>
                </div>
            </div>

            <!-- Generate Password Button -->
            <div class="flex items-center justify-between pt-1">
                <label class="block text-xs font-bold text-slate-700">Password Baru *</label>
                <button type="button" onclick="generateRandomPassword()" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Buat Password Acak</span>
                </button>
            </div>

            <div class="relative">
                <input type="password" id="input_password" name="password" required minlength="8" 
                       placeholder="Minimal 8 karakter..." 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm pr-10">
                <button type="button" onclick="togglePasswordVisibility('input_password', this)" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru *</label>
                <div class="relative">
                    <input type="password" id="input_password_confirmation" name="password_confirmation" required minlength="8" 
                           placeholder="Ketik ulang password baru..." 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl text-sm pr-10">
                    <button type="button" onclick="togglePasswordVisibility('input_password_confirmation', this)" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Password Helper Notification & Copy Box -->
            <div id="password-copy-box" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center justify-between">
                <div>
                    <span class="font-bold block">Password Acak Terisi:</span>
                    <span id="generated-password-preview" class="font-mono text-emerald-900 font-bold"></span>
                </div>
                <button type="button" onclick="copyGeneratedPassword()" class="px-2.5 py-1 bg-white border border-emerald-300 text-emerald-700 font-bold rounded-lg hover:bg-emerald-100 transition-colors cursor-pointer">
                    Salin Password
                </button>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePasswordModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 rounded-xl shadow-xs hover:bg-indigo-700 transition-all cursor-pointer">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab Switching Functionality
    function switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.classList.add('hidden');
        });

        // Reset all button styles
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
            // Reset icon colors
            const icon = btn.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-white');
                icon.classList.add('text-slate-400');
            }
        });

        // Show selected tab content
        const activeContent = document.getElementById(tabId);
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        // Active button style
        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            const icon = activeBtn.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-white');
            }
        }
    }

    // Modal Password Handlers
    function openPasswordModal() {
        document.getElementById('modal-password').classList.remove('hidden');
    }

    function closePasswordModal() {
        document.getElementById('modal-password').classList.add('hidden');
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.add('text-indigo-600');
            btn.classList.remove('text-slate-400');
        } else {
            input.type = 'password';
            btn.classList.remove('text-indigo-600');
            btn.classList.add('text-slate-400');
        }
    }

    // Secure Random Password Generator
    function generateRandomPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
        let pass = '';
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        const inputPass = document.getElementById('input_password');
        const inputConfirm = document.getElementById('input_password_confirmation');
        const preview = document.getElementById('generated-password-preview');
        const copyBox = document.getElementById('password-copy-box');

        if (inputPass && inputConfirm) {
            inputPass.value = pass;
            inputConfirm.value = pass;
            inputPass.type = 'text';
            inputConfirm.type = 'text';
        }

        if (preview && copyBox) {
            preview.innerText = pass;
            copyBox.classList.remove('hidden');
        }
    }

    function copyGeneratedPassword() {
        const preview = document.getElementById('generated-password-preview');
        if (!preview || !preview.innerText) return;
        navigator.clipboard.writeText(preview.innerText).then(function() {
            alert('Password berhasil disalin ke clipboard: ' + preview.innerText);
        });
    }

    // Escape Key Listener for Modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePasswordModal();
        }
    });
</script>
@endsection
