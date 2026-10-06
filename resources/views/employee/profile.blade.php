@extends('employee.layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Profil Saya</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>👤 Profil & Berkas Karyawan</span>
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                ✓ Akun Karyawan Aktif
            </span>
        </div>
    </div>

    <!-- Hero Card Profile Dossier -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <!-- Big Avatar -->
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-black text-3xl sm:text-4xl flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                {{ strtoupper(substr($employee->full_name ?? $user->name, 0, 2)) }}
            </div>

            <!-- Basic Info -->
            <div class="space-y-1.5 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-2xl font-extrabold text-slate-900">{{ $employee->full_name ?? $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $employee->nik ?? 'NIK: EMP-001' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $employee->employment_status ?? 'PKWTT Tetap' }}
                    </span>
                </div>
                <p class="text-sm font-semibold text-slate-600">
                    {{ $employee->position->title ?? 'Staff Spesialis' }} &bull; Departemen {{ $employee->department->name ?? 'Operasional' }}
                </p>
                <div class="flex items-center gap-4 text-xs text-slate-500 pt-1 flex-wrap">
                    <span>📧 {{ $employee->email ?? $user->email }}</span>
                    <span>📱 {{ $employee->phone ?? '-' }}</span>
                    <span>📍 {{ $employee->work_location ?? 'Kantor Pusat' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Detail Karyawan (Grid Informasi) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. Informasi Kepegawaian & Penempatan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <span>🏢 Data Kepegawaian</span>
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Nomor Induk Karyawan (NIK)</span>
                    <span class="font-bold text-slate-900">{{ $employee->nik ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Departemen</span>
                    <span class="font-bold text-slate-900">{{ $employee->department->name ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Jabatan / Posisi</span>
                    <span class="font-bold text-slate-900">{{ $employee->position->title ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Atasan Langsung (Manager)</span>
                    <span class="font-bold text-slate-900">{{ $employee->manager->full_name ?? 'Direktur Operasional' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Tanggal Bergabung</span>
                    <span class="font-bold text-slate-900">
                        {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->isoFormat('D MMMM Y') : '-' }}
                    </span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Sisa Jatah Cuti Tahunan</span>
                    <span class="font-bold text-amber-700">{{ $employee->remaining_annual_leave ?? 12 }} Hari</span>
                </div>
            </div>
        </div>

        <!-- 2. Data Pribadi & Identitas -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <span>📋 Data Pribadi</span>
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Jenis Kelamin</span>
                    <span class="font-bold text-slate-900">{{ $employee->gender === 'MALE' ? 'Laki-Laki' : ($employee->gender === 'FEMALE' ? 'Perempuan' : '-') }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Tanggal Lahir</span>
                    <span class="font-bold text-slate-900">
                        {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->isoFormat('D MMMM Y') : '-' }}
                    </span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Status Pernikahan</span>
                    <span class="font-bold text-slate-900">{{ $employee->marital_status ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">Agama</span>
                    <span class="font-bold text-slate-900">{{ $employee->religion ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">No. KTP / NIK Kependudukan</span>
                    <span class="font-bold font-mono text-slate-900">{{ $employee->identity_number ?? '-' }}</span>
                </div>
                <div class="py-2.5 flex justify-between">
                    <span class="text-slate-500 font-medium">NPWP</span>
                    <span class="font-bold font-mono text-slate-900">{{ $employee->tax_number ?? '-' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Riwayat Pendidikan & Kontrak Kerja -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Riwayat Pendidikan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <span>🎓 Riwayat Pendidikan</span>
            </h3>
            @if($employee->educations && $employee->educations->isNotEmpty())
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($employee->educations as $edu)
                        <div class="py-3">
                            <span class="font-bold text-slate-900 block">{{ $edu->institution_name }}</span>
                            <span class="text-slate-600 block">{{ $edu->degree }} &bull; {{ $edu->major }}</span>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Tahun: {{ $edu->graduation_year ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-slate-400 text-xs">
                    Belum ada catatan riwayat pendidikan formal terlampir.
                </div>
            @endif
        </div>

        <!-- Kontrak Kerja Terdaftar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                <span>📄 Riwayat Kontrak Kerja</span>
            </h3>
            @if($employee->contracts && $employee->contracts->isNotEmpty())
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($employee->contracts as $contract)
                        <div class="py-3">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ $contract->contract_number ?? 'Kontrak Kerja' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700">
                                    {{ $contract->contract_type ?? 'PKWT' }}
                                </span>
                            </div>
                            <span class="text-slate-500 block mt-1">
                                Masa Berlaku: {{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->isoFormat('D MMM Y') : '-' }} s/d {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->isoFormat('D MMM Y') : 'Selesai' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-slate-400 text-xs">
                    Status Ikatan Kerja: PKWTT (Karyawan Tetap).
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
