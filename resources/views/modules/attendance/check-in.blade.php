@extends('layouts.app')

@section('header', 'Presensi Harian & Geofence Check-In')

@section('actions')
    <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold rounded-xl border border-slate-700 transition-colors shadow-xs">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <span>Rekap Log Presensi</span>
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Profil Karyawan & Info Jadwal Shift Hari Ini -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white flex items-center justify-center font-extrabold text-xl shadow-md shadow-indigo-500/20">
                {{ strtoupper(substr($employee->full_name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-900">{{ $employee->full_name }}</h2>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $employee->employment_status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    NIK: {{ $employee->nik }} &bull; {{ $employee->position?->title ?? 'Staff' }} &bull; {{ $employee->department?->name ?? 'Head Office' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/60">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Jadwal Shift Hari Ini</span>
                <span class="text-sm font-bold text-slate-800">
                    {{ $schedule?->shift?->name ?? 'Reguler (08:00 - 17:00)' }}
                </span>
                <span class="text-xs text-indigo-600 font-mono block mt-0.5">
                    Jam: {{ $schedule?->shift?->start_time ?? '08:00' }} - {{ $schedule?->shift?->end_time ?? '17:00' }} (Toleransi {{ $schedule?->shift?->late_tolerance_minutes ?? 15 }}m)
                </span>
            </div>
            <div class="border-l border-slate-200 pl-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Titik Penugasan</span>
                <span class="text-sm font-bold text-slate-800">
                    {{ $schedule?->officeLocation?->name ?? 'Kantor Pusat Jakarta' }}
                </span>
                <span class="text-xs text-slate-500 block mt-0.5">
                    Radius Maks: {{ $schedule?->officeLocation?->radius_meters ?? 150 }} Meter
                </span>
            </div>
        </div>
    </div>

    <!-- Switch Simulasi Karyawan (Jika Admin ingin uji coba presensi pegawai lain) -->
    @if ($allEmployees->count() > 1)
        <div class="bg-indigo-50/70 border border-indigo-200/80 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-indigo-900">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Mode Uji Coba Multi-Karyawan:</strong> Anda dapat menguji presensi masuk/pulang atas nama karyawan lain di bawah ini.</span>
            </div>
            <form action="{{ route('attendance.check-in') }}" method="GET" class="flex items-center gap-2 shrink-0">
                <select name="simulate_employee_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-indigo-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach ($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ $employee->id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->nik }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    <!-- Alert Notifikasi / Error -->
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-bold text-sm">Gagal Melakukan Presensi</h4>
                <p class="text-xs mt-0.5">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <!-- Main Grid: Kamera Selfie & GPS Geofencing -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Kamera & Snapshot Live (Col 7) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-900 text-base">Verifikasi Wajah (Biometric Selfie)</h3>
                </div>
                <span id="camera-status" class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                    Menghubungkan Kamera...
                </span>
            </div>

            <!-- Frame Kamera Live -->
            <div class="relative w-full max-w-md mx-auto aspect-[4/3] sm:aspect-[4/3] bg-slate-950 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center border-2 border-slate-800">
                <!-- Video Stream Element -->
                <video id="webcam-video" autoplay playsinline class="w-full h-full object-cover mirror"></video>

                <!-- Hidden Canvas for Snapshot -->
                <canvas id="snapshot-canvas" class="hidden"></canvas>

                <!-- Snapshot Preview Overlay (saat foto diambil) -->
                <img id="snapshot-preview" class="hidden absolute inset-0 w-full h-full object-cover z-20" alt="Selfie Snapshot">

                <!-- Face Oval Guideline Overlay -->
                <div id="face-guide" class="absolute inset-0 pointer-events-none flex items-center justify-center z-10">
                    <div class="w-48 h-64 border-2 border-dashed border-emerald-400/70 rounded-[50%] shadow-[0_0_0_9999px_rgba(0,0,0,0.35)] flex items-end justify-center pb-4">
                        <span class="text-[11px] font-semibold text-emerald-300 bg-slate-950/80 px-2 py-0.5 rounded-full backdrop-blur-xs">
                            Posisikan Wajah di Sini
                        </span>
                    </div>
                </div>

                <!-- Fallback jika kamera tidak diizinkan -->
                <div id="camera-fallback" class="hidden absolute inset-0 bg-slate-900 text-white p-6 flex-col items-center justify-center text-center z-30">
                    <svg class="w-12 h-12 text-rose-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm font-bold">Kamera Tidak Dapat Diakses</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">Pastikan izin kamera di browser telah diberikan, atau gunakan tombol upload foto selfie alternatif di bawah.</p>
                    <label class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl cursor-pointer">
                        <span>Pilih Foto Selfie File</span>
                        <input type="file" id="fallback-file-input" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            <!-- Kontrol Kamera -->
            <div class="flex items-center justify-center gap-3">
                <button type="button" id="btn-retake" class="hidden px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                    Foto Ulang
                </button>
                <button type="button" id="btn-switch-cam" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Ganti Kamera</span>
                </button>
            </div>
        </div>

        <!-- Kanan: Geolocation GPS & Action Clock In/Out (Col 5) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card Status GPS & Geofence -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="font-bold text-slate-900 text-base">Lokasi & Geofence GPS</h3>
                    </div>
                    <button type="button" onclick="detectGPSLocation()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Refresh GPS
                    </button>
                </div>

                <!-- Status Koordinat -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Status Sinyal:</span>
                        <span id="gps-status-badge" class="font-semibold text-amber-600 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Mencari Satelit GPS...
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-slate-500">Latitude:</span>
                        <span id="display-lat" class="font-bold text-slate-800">-</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-slate-500">Longitude:</span>
                        <span id="display-lng" class="font-bold text-slate-800">-</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Akurasi GPS:</span>
                        <span id="display-accuracy" class="text-slate-700">-</span>
                    </div>
                </div>

                <!-- Indikator Jarak Haversine Realtime -->
                <div id="geofence-alert" class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                    <div class="flex items-center justify-between font-semibold">
                        <span>Jarak dari Kantor:</span>
                        <span id="display-distance" class="text-sm font-bold text-slate-900">Menghitung...</span>
                    </div>
                    <p id="geofence-status-text" class="text-[11px] text-slate-500 mt-1">
                        Sistem sedang memverifikasi posisi Anda terhadap radius kantor.
                    </p>
                </div>

                <!-- Tombol Simulasi Di Kantor (Jika browser di localhost tidak berada di koordinat kantor Jakarta) -->
                @php
                    $targetLocation = $schedule?->officeLocation ?? $officeLocations->first();
                @endphp
                @if ($targetLocation)
                    <div class="pt-2">
                        <button type="button" 
                                onclick="simulateOfficeLocation({{ $targetLocation->latitude }}, {{ $targetLocation->longitude }})" 
                                class="w-full text-center py-1.5 px-3 rounded-lg border border-dashed border-indigo-300 bg-indigo-50/50 hover:bg-indigo-100/50 text-indigo-700 text-xs font-medium transition-colors">
                            &bull; Simulasi: Posisikan GPS di {{ $targetLocation->name }}
                        </button>
                    </div>
                @endif
            </div>

            <!-- Card Tombol Aksi Check-In & Check-Out -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Aksi Kehadiran Hari Ini</h3>

                <!-- Status Kehadiran Hari Ini -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 rounded-xl {{ $todayAttendance?->clock_in ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-slate-50 border border-slate-200 text-slate-600' }}">
                        <span class="text-[11px] font-semibold uppercase tracking-wider block">Jam Masuk (Clock-In)</span>
                        <span class="text-base font-extrabold mt-1 block">
                            {{ $todayAttendance?->clock_in ? $todayAttendance->clock_in->format('H:i:s') : '—' }}
                        </span>
                        @if ($todayAttendance?->late_minutes > 0)
                            <span class="text-[10px] font-bold text-rose-600 block mt-0.5">
                                Telat {{ $todayAttendance->late_minutes }} Menit
                            </span>
                        @elseif ($todayAttendance?->clock_in)
                            <span class="text-[10px] font-bold text-emerald-600 block mt-0.5">
                                Tepat Waktu
                            </span>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl {{ $todayAttendance?->clock_out ? 'bg-indigo-50 border border-indigo-200 text-indigo-900' : 'bg-slate-50 border border-slate-200 text-slate-600' }}">
                        <span class="text-[11px] font-semibold uppercase tracking-wider block">Jam Pulang (Clock-Out)</span>
                        <span class="text-base font-extrabold mt-1 block">
                            {{ $todayAttendance?->clock_out ? $todayAttendance->clock_out->format('H:i:s') : '—' }}
                        </span>
                        @if ($todayAttendance?->total_work_minutes > 0)
                            <span class="text-[10px] font-bold text-indigo-600 block mt-0.5">
                                {{ floor($todayAttendance->total_work_minutes / 60) }}j {{ $todayAttendance->total_work_minutes % 60 }}m Kerja
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Forms Clock In & Clock Out -->
                @if (! $todayAttendance?->clock_in)
                    <!-- FORM CLOCK IN -->
                    <form id="form-clock-in" action="{{ route('attendance.clock-in') }}" method="POST" onsubmit="return handleAttendanceSubmit(event, 'in')">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <input type="hidden" name="latitude" id="in-latitude">
                        <input type="hidden" name="longitude" id="in-longitude">
                        <input type="hidden" name="photo_data" id="in-photo-data">

                        <button type="submit" id="btn-submit-in" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Ambil Foto & Clock-In Masuk</span>
                        </button>
                    </form>
                @elseif (! $todayAttendance?->clock_out)
                    <!-- FORM CLOCK OUT -->
                    <form id="form-clock-out" action="{{ route('attendance.clock-out') }}" method="POST" onsubmit="return handleAttendanceSubmit(event, 'out')">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <input type="hidden" name="latitude" id="out-latitude">
                        <input type="hidden" name="longitude" id="out-longitude">
                        <input type="hidden" name="photo_data" id="out-photo-data">

                        <button type="submit" id="btn-submit-out" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Ambil Foto & Clock-Out Pulang</span>
                        </button>
                    </form>
                @else
                    <!-- SUDAH SELESAI HARI INI -->
                    <div class="p-4 bg-slate-100 rounded-xl text-center text-slate-700 text-sm font-semibold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Presensi Hari Ini Lengkap (Clock-In & Clock-Out Selesai)</span>
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Histori 5 Presensi Terakhir Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Histori Presensi Terkini</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan kehadiran {{ $employee->full_name }} dalam beberapa hari terakhir</p>
            </div>
            <a href="{{ route('attendance.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                Lihat Seluruh Log &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Tanggal</th>
                        <th class="py-3 px-5">Shift Kerja</th>
                        <th class="py-3 px-5">Clock In</th>
                        <th class="py-3 px-5">Clock Out</th>
                        <th class="py-3 px-5">Durasi Kerja</th>
                        <th class="py-3 px-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                {{ $att->date->translatedFormat('d F Y') }}
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                {{ $att->shift?->name ?? 'Shift Normal' }}
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs">
                                <span class="font-bold text-slate-900">{{ $att->clock_in ? $att->clock_in->format('H:i') : '-' }}</span>
                                @if ($att->clock_in_distance_meters)
                                    <span class="text-[10px] text-slate-400 block font-sans">({{ round($att->clock_in_distance_meters) }}m dari kantor)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs">
                                <span class="font-bold text-slate-900">{{ $att->clock_out ? $att->clock_out->format('H:i') : '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                @if ($att->total_work_minutes > 0)
                                    {{ floor($att->total_work_minutes / 60) }} jam {{ $att->total_work_minutes % 60 }} menit
                                @else
                                    -
                                @endif
                            </td>
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
                            <td colspan="6" class="p-6 text-center text-slate-400 text-xs">
                                Belum ada catatan riwayat kehadiran sebelumnya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Styles and Client Logic for Webcam Stream & GPS Geofence -->
<style>
    .mirror {
        transform: scaleX(-1);
    }
</style>

<script>
    // State variables
    let videoStream = null;
    let currentFacingMode = 'user';
    let availableVideoDevices = [];
    let currentDeviceIndex = 0;
    let userLat = null;
    let userLng = null;
    let userAccuracy = null;
    let capturedPhotoBase64 = null;

    // Office Location Target (dari schedule atau fallback pertama)
    const officeTarget = {
        lat: {{ $targetLocation ? $targetLocation->latitude : -6.2087634 }},
        lng: {{ $targetLocation ? $targetLocation->longitude : 106.8455990 }},
        radius: {{ $targetLocation ? $targetLocation->radius_meters : 150 }},
        name: "{{ $targetLocation ? $targetLocation->name : 'Kantor Pusat' }}"
    };

    // Helper: Perbarui daftar kamera fisik yang tersedia
    async function enumerateCameraDevices() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            return [];
        }
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            availableVideoDevices = devices.filter(d => d.kind === 'videoinput');
            const switchBtnText = document.querySelector('#btn-switch-cam span');
            if (availableVideoDevices.length > 1 && switchBtnText) {
                switchBtnText.textContent = `Ganti Kamera (${availableVideoDevices.length})`;
            }
            return availableVideoDevices;
        } catch (e) {
            console.warn('Gagal membaca daftar kamera:', e);
            return [];
        }
    }

    // 1. Inisialisasi Kamera Web
    async function initCamera(preferFacingMode = null, specificDeviceId = null) {
        const video = document.getElementById('webcam-video');
        const cameraStatus = document.getElementById('camera-status');
        const fallback = document.getElementById('camera-fallback');

        if (preferFacingMode) {
            currentFacingMode = preferFacingMode;
        }

        // Hentikan stream kamera lama jika ada
        if (videoStream) {
            videoStream.getTracks().forEach(track => {
                try { track.stop(); } catch (e) {}
            });
            videoStream = null;
        }
        if (video) {
            video.srcObject = null;
        }

        // Siapkan constraints yang aman (gunakan ideal agar tidak melempar OverconstrainedError)
        let constraints = {
            audio: false,
            video: {
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 }
            }
        };

        if (specificDeviceId) {
            constraints.video.deviceId = { exact: specificDeviceId };
        } else {
            constraints.video.facingMode = { ideal: currentFacingMode };
        }

        try {
            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = videoStream;
            await video.play();

            // Atur efek cermin (mirroring hanya untuk kamera depan/selfie)
            if (currentFacingMode === 'user') {
                video.classList.add('mirror');
            } else {
                video.classList.remove('mirror');
            }

            cameraStatus.textContent = currentFacingMode === 'user' ? 'Kamera Depan Aktif' : 'Kamera Belakang Aktif';
            cameraStatus.className = 'text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
            fallback.classList.add('hidden');

            // Perbarui daftar kamera setelah izin kamera aktif (agar label deviceId terbaca)
            await enumerateCameraDevices();
        } catch (err) {
            console.warn('Percobaan kamera gagal, mencoba fallback aman:', err);
            
            // Fallback: Jika constraint spesifik gagal, coba minta video standar tanpa facingMode strict
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = videoStream;
                await video.play();

                currentFacingMode = 'user';
                video.classList.add('mirror');
                cameraStatus.textContent = 'Kamera Aktif';
                cameraStatus.className = 'text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
                fallback.classList.add('hidden');
                await enumerateCameraDevices();
            } catch (fallbackErr) {
                console.error('Semua akses kamera ditolak/gagal:', fallbackErr);
                cameraStatus.textContent = 'Kamera Error / Izin Ditolak';
                cameraStatus.className = 'text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-medium';
                fallback.classList.remove('hidden');
            }
        }
    }

    // Toggle Camera (Ganti Kamera Depan / Belakang / Multiple Webcams)
    document.getElementById('btn-switch-cam').addEventListener('click', async () => {
        const cameraStatus = document.getElementById('camera-status');
        
        // Pastikan daftar device up-to-date
        await enumerateCameraDevices();

        if (availableVideoDevices.length > 1) {
            // Jika ada lebih dari 1 kamera fisik (misal: smartphone depan & belakang, atau laptop + webcam USB)
            currentDeviceIndex = (currentDeviceIndex + 1) % availableVideoDevices.length;
            const targetDevice = availableVideoDevices[currentDeviceIndex];
            
            // Deteksi apakah label mengindikasikan kamera belakang
            const isBack = /back|rear|environment|belakang/i.test(targetDevice.label);
            currentFacingMode = isBack ? 'environment' : 'user';

            cameraStatus.textContent = 'Mengganti Kamera...';
            await initCamera(currentFacingMode, targetDevice.deviceId);
        } else {
            // Jika hanya terdeteksi 1 kamera atau browser mobile menyembunyikan deviceId
            const nextMode = currentFacingMode === 'user' ? 'environment' : 'user';
            cameraStatus.textContent = 'Beralih ' + (nextMode === 'user' ? 'Kamera Depan...' : 'Kamera Belakang...');
            await initCamera(nextMode);

            // Jika setelah dicoba ternyata perangkat hanya punya 1 kamera
            if (availableVideoDevices.length === 1) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs px-4 py-2.5 rounded-xl shadow-lg border border-slate-700 flex items-center gap-2';
                toast.innerHTML = '<span>ℹ️ Hanya 1 kamera terdeteksi pada perangkat ini.</span>';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3500);
            }
        }
    });

    // 2. Ambil Geolocation GPS Browser
    function detectGPSLocation() {
        const badge = document.getElementById('gps-status-badge');
        badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Mengunci Sinyal GPS...';
        badge.className = 'font-semibold text-amber-600 flex items-center gap-1.5';

        if (!navigator.geolocation) {
            badge.textContent = 'Geolocation tidak didukung browser ini';
            badge.className = 'font-semibold text-rose-600';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;
                userAccuracy = pos.coords.accuracy;

                document.getElementById('display-lat').textContent = userLat.toFixed(6);
                document.getElementById('display-lng').textContent = userLng.toFixed(6);
                document.getElementById('display-accuracy').textContent = `± ${Math.round(userAccuracy)} meter`;

                badge.textContent = 'GPS Terkunci Akurat';
                badge.className = 'font-semibold text-emerald-600';

                // Hitung jarak Haversine ke titik kantor
                evaluateDistance();
            },
            (err) => {
                console.warn('GPS Error:', err.message);
                badge.textContent = 'Izin GPS Ditolak / Tidak Aktif';
                badge.className = 'font-semibold text-rose-600';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // 3. Rumus Haversine di JavaScript untuk feedback visual seketika
    function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c);
    }

    function evaluateDistance() {
        if (userLat === null || userLng === null) return;

        const distanceMeters = calculateHaversineDistance(userLat, userLng, officeTarget.lat, officeTarget.lng);
        const display = document.getElementById('display-distance');
        const alertBox = document.getElementById('geofence-alert');
        const statusText = document.getElementById('geofence-status-text');

        display.textContent = `${distanceMeters} Meter`;

        if (distanceMeters <= officeTarget.radius) {
            display.className = 'text-sm font-bold text-emerald-600';
            alertBox.className = 'p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-xs text-emerald-800';
            statusText.textContent = `Posisi valid! Anda berada di dalam zona radius ${officeTarget.name} (Maksimal ${officeTarget.radius}m).`;
        } else {
            display.className = 'text-sm font-bold text-rose-600';
            alertBox.className = 'p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-xs text-rose-800';
            statusText.textContent = `Perhatian: Anda berada di luar radius kantor (${distanceMeters}m). Presensi mungkin ditolak jika melebihi ${officeTarget.radius}m.`;
        }
    }

    // Fitur simulasi GPS di lokasi kantor untuk pengujian developer
    window.simulateOfficeLocation = function(lat, lng) {
        userLat = lat;
        userLng = lng;
        userAccuracy = 5;

        document.getElementById('display-lat').textContent = userLat.toFixed(6) + ' (Simulasi)';
        document.getElementById('display-lng').textContent = userLng.toFixed(6) + ' (Simulasi)';
        document.getElementById('display-accuracy').textContent = '± 5 meter (Simulasi Kantor)';

        const badge = document.getElementById('gps-status-badge');
        badge.textContent = 'GPS Terkunci (Titik Kantor)';
        badge.className = 'font-semibold text-emerald-600';

        evaluateDistance();
    };

    // 4. Jepret Foto Selfie dari Video Stream
    function captureSelfie() {
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('snapshot-canvas');
        const preview = document.getElementById('snapshot-preview');
        const btnRetake = document.getElementById('btn-retake');

        // Jika foto sudah pernah di-fallback via file input
        if (capturedPhotoBase64 && !videoStream) {
            return capturedPhotoBase64;
        }

        if (!video || !video.videoWidth) {
            return null;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');

        // Balik horizontal jika kamera depan
        if (currentFacingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        // Tampilkan preview snapshot
        preview.src = capturedPhotoBase64;
        preview.classList.remove('hidden');
        btnRetake.classList.remove('hidden');

        return capturedPhotoBase64;
    }

    document.getElementById('btn-retake').addEventListener('click', () => {
        const preview = document.getElementById('snapshot-preview');
        const btnRetake = document.getElementById('btn-retake');
        preview.classList.add('hidden');
        btnRetake.classList.add('hidden');
        capturedPhotoBase64 = null;
    });

    // Fallback file input jika tidak ada kamera
    document.getElementById('fallback-file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                capturedPhotoBase64 = evt.target.result;
                const preview = document.getElementById('snapshot-preview');
                preview.src = capturedPhotoBase64;
                preview.classList.remove('hidden');
                document.getElementById('camera-fallback').classList.add('hidden');
                document.getElementById('btn-retake').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // 5. Submit Handler
    window.handleAttendanceSubmit = function(event, type) {
        if (userLat === null || userLng === null) {
            alert('Koordinat GPS belum terdeteksi. Silakan aktifkan GPS atau klik tombol simulasi jika menguji di localhost.');
            detectGPSLocation();
            return false;
        }

        const photo = captureSelfie();
        if (!photo) {
            alert('Gagal mengambil foto selfie. Pastikan kamera menyala atau unggah foto selfie.');
            return false;
        }

        if (type === 'in') {
            document.getElementById('in-latitude').value = userLat;
            document.getElementById('in-longitude').value = userLng;
            document.getElementById('in-photo-data').value = photo;
            document.getElementById('btn-submit-in').disabled = true;
            document.getElementById('btn-submit-in').innerHTML = 'Memproses Clock-In...';
        } else {
            document.getElementById('out-latitude').value = userLat;
            document.getElementById('out-longitude').value = userLng;
            document.getElementById('out-photo-data').value = photo;
            document.getElementById('btn-submit-out').disabled = true;
            document.getElementById('btn-submit-out').innerHTML = 'Memproses Clock-Out...';
        }

        return true;
    };

    // Run on Mount
    document.addEventListener('DOMContentLoaded', () => {
        initCamera();
        detectGPSLocation();
    });
</script>
@endsection
