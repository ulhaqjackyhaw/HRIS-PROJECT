@extends('employee.layouts.app', ['title' => 'Dashboard Karyawan'])

@section('content')
<div class="space-y-6">

    <!-- 1. Banner Sambutan Karyawan (Modern Amber Gradient Hero) -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-amber-700 text-white shadow-xl relative overflow-hidden">
        <!-- Ambient shapes -->
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-4 sm:space-x-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/20 backdrop-blur-md border-2 border-white/40 flex items-center justify-center font-black text-2xl sm:text-3xl text-white shadow-lg shrink-0">
                    {{ strtoupper(substr($employee->full_name ?? $user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wider uppercase bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                            {{ $employee->nik ?? 'NIK: EMP-001' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-400 text-emerald-950">
                            {{ $employee->employment_status ?? 'PKWTT' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white">
                            📍 {{ $employee->work_location ?? 'Kantor Pusat' }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                        Halo, {{ $employee->full_name ?? $user->name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-amber-100 font-medium mt-0.5">
                        {{ $employee->position->title ?? 'Staff Spesialis' }} &bull; {{ $employee->department->name ?? 'Internal Division' }}
                    </p>
                </div>
            </div>

            <!-- Quick Action CTA Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('employee.attendance.check-in') }}" 
                   class="px-5 py-3 rounded-2xl font-bold text-xs bg-white text-slate-900 hover:bg-amber-50 shadow-md transition-all flex items-center gap-2 group cursor-pointer">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-extrabold text-amber-900">📸 Presensi Selfie & GPS</span>
                </a>
                <a href="{{ route('employee.leaves.index') }}" 
                   class="px-4 py-3 rounded-2xl font-bold text-xs bg-black/20 hover:bg-black/30 border border-white/30 text-white backdrop-blur-sm transition-all flex items-center gap-2 cursor-pointer">
                    <span>🏖️ Ajukan Cuti</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Status Presensi Hari Ini & Waktu Real-Time -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider block">Hari Ini &bull; {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-base font-extrabold text-slate-900" id="live-clock">--:--:-- WIB</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    @if ($todayAttendance && $todayAttendance->clock_in)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                            ✓ Sudah Clock-In ({{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }})
                        </span>
                        @if ($todayAttendance->clock_out)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                                ✓ Sudah Clock-Out ({{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') }})
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-md">
                            ⏳ Belum Clock-In Masuk
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div>
            @if ($todayAttendance && $todayAttendance->clock_in && ! $todayAttendance->clock_out)
                <a href="{{ route('employee.attendance.check-in') }}" 
                   class="w-full md:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-amber-600 hover:bg-amber-500 text-white shadow-xs transition-all flex items-center justify-center gap-1.5">
                    <span>Clock-Out Pulang Kerja &rarr;</span>
                </a>
            @elseif (! $todayAttendance || ! $todayAttendance->clock_in)
                <a href="{{ route('employee.attendance.check-in') }}" 
                   class="w-full md:w-auto px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition-all flex items-center justify-center gap-1.5">
                    <span>Clock-In Masuk Kerja Sekarang &rarr;</span>
                </a>
            @else
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl block text-center">
                    ✓ Presensi Hari Ini Telah Lengkap
                </span>
            @endif
        </div>
    </div>

    <!-- 3. Empat Kartu Ringkasan Indikator (KPI) Karyawan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Kartu 1: Presensi Hari Ini -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Kehadiran</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div>
                @if ($todayAttendance && $todayAttendance->clock_in)
                    <div class="text-2xl font-black text-slate-900">
                        {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-bold {{ $todayAttendance->status === 'LATE' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $todayAttendance->status === 'LATE' ? 'Terlambat' : 'Tepat Waktu' }}
                        </span>
                        @if ($todayAttendance->clock_out)
                            <span class="text-slate-500">Pulang: {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') }}</span>
                        @endif
                    </div>
                @else
                    <div class="text-2xl font-black text-slate-400">Belum Ada</div>
                    <p class="text-xs text-slate-500 mt-1">Belum melakukan presensi masuk</p>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('employee.attendance.check-in') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                    <span>Halaman Presensi</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Kartu 2: Saldo Cuti Tahunan -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Saldo Cuti Tahunan</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-black text-slate-900">{{ $employee->remaining_annual_leave ?? 12 }}</span>
                    <span class="text-xs text-slate-500 font-semibold">Hari Tersisa</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Total Kuota: {{ $employee->annual_leave_quota ?? 12 }} &bull; Dipakai: {{ $employee->annual_leave_used ?? 0 }} hari
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('employee.leaves.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <span>Ajukan Cuti / Izin</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Kartu 3: Jadwal Shift Hari Ini -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Shift Kerja</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div>
                @if ($schedule && $schedule->shift)
                    <div class="text-lg font-bold text-slate-900 truncate">{{ $schedule->shift->name }}</div>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }} WIB
                    </p>
                @else
                    <div class="text-lg font-bold text-slate-800">Shift Reguler</div>
                    <p class="text-xs text-slate-500 mt-1">08:00 - 17:00 WIB (Waktu Kerja Normal)</p>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('employee.schedules.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <span>Lihat Roster Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Kartu 4: Pengajuan Lembur -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lembur (Overtime)</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">
                    {{ $recentOvertimes->count() }}
                </div>
                <p class="text-xs text-slate-500 mt-1">Pengajuan lembur aktif periode ini</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('employee.overtimes.index') }}" class="text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                    <span>Ajukan Lembur Baru</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

    </div>

    <!-- 4. Dua Kolom Log Riwayat: Kehadiran Terakhir & Pengajuan Cuti -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Kolom Kiri: Riwayat Presensi Terakhir -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Riwayat Presensi Terbaru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">5 hari kerja terakhir</p>
                </div>
                <a href="{{ route('employee.attendance.history') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                    <span>Semua Riwayat</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if ($recentAttendances->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($recentAttendances as $att)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold {{ $att->status === 'LATE' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                                    {{ \Carbon\Carbon::parse($att->date)->format('d') }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">
                                        {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        Shift: {{ $att->shift->name ?? 'Reguler' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-mono font-bold text-slate-900">
                                    {{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '--:--' }} - 
                                    {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '--:--' }}
                                </div>
                                <span class="inline-block mt-0.5 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $att->status === 'LATE' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $att->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-slate-400 text-xs">
                    Belum ada data presensi yang tercatat.
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Pengajuan Cuti & Lembur Terkini -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pengajuan Cuti Terkini</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Status persetujuan permohonan</p>
                </div>
                <a href="{{ route('employee.leaves.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <span>Semua Pengajuan</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if ($recentLeaves->count() > 0)
                <div class="divide-y divide-slate-100">
                    @foreach ($recentLeaves as $leave)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ $leave->leaveType->name ?? 'Cuti Tahunan' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }} &bull; ({{ $leave->total_days }} Hari)
                                </div>
                            </div>
                            <div>
                                @if ($leave->status === 'APPROVED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Disetujui
                                    </span>
                                @elseif ($leave->status === 'REJECTED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        ✕ Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ Menunggu
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-slate-400 text-xs">
                    Belum ada pengajuan cuti yang diajukan.
                </div>
            @endif
        </div>

    </div>

</div>

<!-- Realtime Clock Script -->
<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
        const clockEl = document.getElementById('live-clock');
        if (clockEl) {
            clockEl.textContent = timeString;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
