@extends('layouts.app')

@section('header', 'Monitoring & Log Aktivitas Presensi')

@section('actions')
    <a href="{{ route('attendance.check-in') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
        </svg>
        <span>Buka Terminal Presensi (Check-In)</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards Hari Terpilih -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Hadir Tepat Waktu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Tepat Waktu</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalPresent) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Presensi sesuai jadwal</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($totalLate) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Lewat batas toleransi shift</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Pulang Cepat -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pulang Cepat</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($totalEarlyLeave) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Sebelum jam selesai shift</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
        </div>

        <!-- Belum Presensi / Mangkir -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Belum Presensi / Mangkir</p>
                <h3 class="text-2xl font-extrabold text-slate-700 mt-1">{{ number_format($totalAbsent) }}</h3>
                <p class="text-xs text-slate-400 mt-1">Dari {{ $totalActiveEmployees }} karyawan aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Bar Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('attendance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Filter Tanggal -->
            <div>
                <label for="date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal</label>
                <input type="date" id="date" name="date" value="{{ $targetDate }}" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Filter Status -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Status Presensi</label>
                <select id="status" name="status" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="PRESENT" {{ request('status') === 'PRESENT' ? 'selected' : '' }}>Tepat Waktu (PRESENT)</option>
                    <option value="LATE" {{ request('status') === 'LATE' ? 'selected' : '' }}>Terlambat (LATE)</option>
                    <option value="EARLY_LEAVE" {{ request('status') === 'EARLY_LEAVE' ? 'selected' : '' }}>Pulang Cepat (EARLY_LEAVE)</option>
                    <option value="ABSENT" {{ request('status') === 'ABSENT' ? 'selected' : '' }}>Mangkir (ABSENT)</option>
                </select>
            </div>

            <!-- Filter Departemen -->
            <div>
                <label for="department_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Departemen</label>
                <select id="department_id" name="department_id" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pencarian Karyawan -->
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Cari Karyawan</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-xs">
                    Terapkan Filter
                </button>
                <a href="{{ route('attendance.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition-colors" title="Reset Filter">
                    &times;
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Log Presensi Harian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">
                    Log Presensi Tanggal {{ Carbon\Carbon::parse($targetDate)->translatedFormat('d F Y') }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Audit trail selfie snapshot, geolokasi, dan jam kerja pegawai</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-full">
                {{ $attendances->total() }} Log Tercatat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Unit / Jabatan</th>
                        <th class="py-3 px-5">Shift Kerja</th>
                        <th class="py-3 px-5">Clock In (Masuk)</th>
                        <th class="py-3 px-5">Clock Out (Pulang)</th>
                        <th class="py-3 px-5">Total Jam</th>
                        <th class="py-3 px-5">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attendances as $att)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Karyawan -->
                            <td class="py-3.5 px-5">
                                @if ($att->employee)
                                    <a href="{{ route('employees.show', $att->employee) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                        {{ $att->employee->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-400 font-mono">{{ $att->employee->nik }}</div>
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Unit / Jabatan -->
                            <td class="py-3.5 px-5">
                                <div class="text-slate-800">{{ $att->employee?->department?->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $att->employee?->position?->title ?? '-' }}</div>
                            </td>

                            <!-- Shift -->
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                <span class="font-semibold block">{{ $att->shift?->name ?? 'Shift Normal' }}</span>
                                <span class="text-slate-400 font-mono">
                                    {{ $att->shift?->start_time ?? '08:00' }} - {{ $att->shift?->end_time ?? '17:00' }}
                                </span>
                            </td>

                            <!-- Clock In & Foto Selfie -->
                            <td class="py-3.5 px-5">
                                @if ($att->clock_in)
                                    <div class="flex items-center gap-2.5">
                                        @if ($att->clock_in_photo_path)
                                            <button type="button" 
                                                    onclick="previewPhoto('{{ asset('storage/'.$att->clock_in_photo_path) }}', 'Selfie Clock-In - {{ $att->employee?->full_name }}')"
                                                    class="shrink-0 w-9 h-9 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 hover:opacity-80 transition-opacity">
                                                <img src="{{ asset('storage/'.$att->clock_in_photo_path) }}" class="w-full h-full object-cover" alt="Selfie Clock In">
                                            </button>
                                        @else
                                            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[10px] font-mono">
                                                No Pic
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-mono font-bold text-slate-900 text-xs block">
                                                {{ $att->clock_in->format('H:i:s') }}
                                            </span>
                                            @if ($att->clock_in_distance_meters)
                                                <span class="text-[10px] text-slate-400 block font-sans">
                                                    {{ round($att->clock_in_distance_meters) }}m dari kantor
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-300 font-mono text-xs">—</span>
                                @endif
                            </td>

                            <!-- Clock Out & Foto Selfie -->
                            <td class="py-3.5 px-5">
                                @if ($att->clock_out)
                                    <div class="flex items-center gap-2.5">
                                        @if ($att->clock_out_photo_path)
                                            <button type="button" 
                                                    onclick="previewPhoto('{{ asset('storage/'.$att->clock_out_photo_path) }}', 'Selfie Clock-Out - {{ $att->employee?->full_name }}')"
                                                    class="shrink-0 w-9 h-9 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 hover:opacity-80 transition-opacity">
                                                <img src="{{ asset('storage/'.$att->clock_out_photo_path) }}" class="w-full h-full object-cover" alt="Selfie Clock Out">
                                            </button>
                                        @else
                                            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[10px] font-mono">
                                                No Pic
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-mono font-bold text-slate-900 text-xs block">
                                                {{ $att->clock_out->format('H:i:s') }}
                                            </span>
                                            @if ($att->early_leave_minutes > 0)
                                                <span class="text-[10px] text-rose-500 font-semibold block">
                                                    Pulang cepat {{ $att->early_leave_minutes }}m
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum Clock-Out</span>
                                @endif
                            </td>

                            <!-- Total Jam Kerja -->
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                @if ($att->total_work_minutes > 0)
                                    <span class="font-bold text-slate-900">
                                        {{ floor($att->total_work_minutes / 60) }}j {{ $att->total_work_minutes % 60 }}m
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            <!-- Status Kehadiran Badge -->
                            <td class="py-3.5 px-5">
                                @php
                                    $badge = match($att->status) {
                                        'PRESENT' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'LATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'EARLY_LEAVE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $att->status }}
                                    @if ($att->late_minutes > 0)
                                        (+{{ $att->late_minutes }}m)
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Tidak ada catatan log presensi untuk kriteria tanggal atau filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attendances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Foto Preview -->
<div id="photo-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl space-y-4 p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 id="photo-modal-title" class="font-bold text-slate-900 text-sm">Bukti Foto Presensi</h4>
            <button type="button" onclick="closePhotoModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">
                &times;
            </button>
        </div>
        <div class="aspect-[4/3] bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center">
            <img id="photo-modal-img" src="" class="w-full h-full object-contain" alt="Bukti Foto Selfie">
        </div>
        <div class="text-right">
            <button type="button" onclick="closePhotoModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function previewPhoto(url, title) {
        document.getElementById('photo-modal-img').src = url;
        document.getElementById('photo-modal-title').textContent = title;
        document.getElementById('photo-modal').classList.remove('hidden');
    }

    function closePhotoModal() {
        document.getElementById('photo-modal').classList.add('hidden');
    }
</script>
@endsection
