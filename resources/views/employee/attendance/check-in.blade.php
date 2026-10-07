@extends('employee.layouts.app', ['title' => 'Presensi Selfie & GPS'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb & Status Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Presensi Selfie & GPS</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>📸 Presensi Mandiri (ESS)</span>
            </h1>
        </div>

        <a href="{{ route('employee.attendance.history') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:text-amber-600 hover:border-amber-200 transition-all shadow-xs shrink-0">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Lihat Riwayat Presensi</span>
        </a>
    </div>

    <!-- Info Jadwal & Penugasan Hari Ini -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                {{ strtoupper(substr($employee->full_name ?? auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">{{ $employee->full_name ?? auth()->user()->name }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $employee->nik ?? 'Karyawan Aktif' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $employee->position->title ?? 'Staff' }} &bull; {{ $employee->department->name ?? 'Operasional' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200/80 text-xs text-slate-700">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Shift Hari Ini</span>
                <span class="font-extrabold text-slate-900">{{ $schedule?->shift?->name ?? 'Reguler Office (08:00 - 17:00)' }}</span>
            </div>
            <div class="border-l border-slate-200 pl-3">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Titik Penugasan</span>
                <span class="font-extrabold text-slate-900">{{ $schedule?->officeLocation?->name ?? 'Kantor Pusat' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Kamera Selfie & GPS Validasi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kiri: Frame Kamera Live & Snapshot (Col 7) -->
        <div class="lg:col-span-7 bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Kamera Verifikasi Wajah</h3>
                </div>
                <span id="camera-status" class="text-[11px] px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold">
                    Memulai Kamera...
                </span>
            </div>

            <!-- Viewport Kamera -->
            <div class="relative w-full aspect-[4/3] bg-slate-950 rounded-2xl overflow-hidden shadow-inner flex items-center justify-center border-2 border-slate-800">
                <video id="webcam-video" autoplay playsinline class="w-full h-full object-cover -scale-x-100"></video>
                <canvas id="snapshot-canvas" class="hidden"></canvas>
                <img id="snapshot-preview" class="hidden absolute inset-0 w-full h-full object-cover z-20" alt="Selfie Snapshot">

                <!-- Face Oval Guideline Overlay -->
                <div id="face-guide" class="absolute inset-0 pointer-events-none flex items-center justify-center z-10">
                    <div class="w-44 h-60 border-2 border-dashed border-amber-400/80 rounded-[50%] shadow-[0_0_0_9999px_rgba(0,0,0,0.35)] flex items-end justify-center pb-3">
                        <span class="text-[10px] font-bold text-white uppercase bg-black/60 px-2 py-0.5 rounded-full tracking-wider">
                            Posisikan Wajah
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kontrol Kamera -->
            <div class="flex items-center justify-between gap-3 pt-1">
                <button type="button" id="btn-switch-cam" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Ganti Kamera</span>
                </button>

                <button type="button" id="btn-retake" class="hidden px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    <span>Foto Ulang</span>
                </button>
            </div>

            <!-- Fallback unggah foto jika kamera perangkat bermasalah -->
            <div id="camera-fallback" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-2">
                <p class="font-bold">Akses Kamera Browser Tidak Tersedia?</p>
                <p class="text-[11px] text-amber-800">Anda dapat mengunggah file selfie secara langsung melalui tombol di bawah:</p>
                <input type="file" id="fallback-file-input" accept="image/*" capture="user" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-white hover:file:bg-amber-600">
            </div>
        </div>

        <!-- Kanan: Geolocation GPS & Tombol Clock-In / Clock-Out (Col 5) -->
        <div class="lg:col-span-5 space-y-5">

            <!-- Card Lokasi & Radius Geofence -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h3 class="font-bold text-slate-900 text-sm">Status Lokasi GPS</h3>
                    </div>
                    <span id="gps-status-badge" class="text-[11px] font-bold text-amber-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        Mencari GPS...
                    </span>
                </div>

                <!-- Info Koordinat Terkini -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Koordinat Anda:</span>
                        <span class="font-mono font-bold text-slate-900"><span id="display-lat">-</span>, <span id="display-lng">-</span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Akurasi GPS:</span>
                        <span id="display-accuracy" class="font-semibold text-slate-700">-</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200/80 pt-2">
                        <span class="text-slate-500">Jarak ke Kantor:</span>
                        <span id="display-distance" class="font-bold text-slate-900">-</span>
                    </div>
                </div>

                <!-- Alert Geofence Radius -->
                <div id="geofence-alert" class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-xs text-slate-700">
                    <p id="geofence-status-text" class="font-medium">
                        Menghitung posisi Anda terhadap titik kantor...
                    </p>
                </div>

                <!-- Shortcut Uji Coba Titik Kantor (Localhost / Testing) -->
                @php
                    $targetLoc = $schedule?->officeLocation ?? $officeLocations->first();
                @endphp
                @if ($targetLoc)
                    <div class="pt-1">
                        <button type="button" 
                                onclick="simulateOfficeLocation({{ $targetLoc->latitude }}, {{ $targetLoc->longitude }})" 
                                class="w-full py-2 px-3 rounded-xl text-[11px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>🎯</span>
                            <span>Simulasi GPS di {{ $targetLoc->name }} (Testing)</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Card Tombol Aksi Clock-In & Clock-Out -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100">Eksekusi Presensi Hari Ini</h3>

                @if(! $todayAttendance || ! $todayAttendance->clock_in)
                    <!-- Tombol Clock In -->
                    <form action="{{ route('employee.attendance.clock-in') }}" method="POST" id="form-clock-in" onsubmit="return handleAttendanceSubmit(event, 'in')">
                        @csrf
                        <input type="hidden" name="latitude" id="in-latitude">
                        <input type="hidden" name="longitude" id="in-longitude">
                        <input type="hidden" name="photo_data" id="in-photo-data">

                        <button type="submit" id="btn-submit-in" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer" style="touch-action: manipulation; min-height: 48px;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>CLOCK-IN MASUK</span>
                        </button>
                    </form>
                @elseif($todayAttendance->clock_in && ! $todayAttendance->clock_out)
                    <!-- Status Sudah Masuk, Siap Clock-Out -->
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 space-y-1">
                        <div class="flex items-center gap-2 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Sudah Clock-In Hari Ini: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} WIB</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Status: <strong class="uppercase">{{ $todayAttendance->status }}</strong> {{ $todayAttendance->late_minutes ? '('.$todayAttendance->late_minutes.'m terlambat)' : '' }}</p>
                    </div>

                    <!-- Tombol Clock Out -->
                    <form action="{{ route('employee.attendance.clock-out') }}" method="POST" id="form-clock-out" onsubmit="return handleAttendanceSubmit(event, 'out')">
                        @csrf
                        <input type="hidden" name="latitude" id="out-latitude">
                        <input type="hidden" name="longitude" id="out-longitude">
                        <input type="hidden" name="photo_data" id="out-photo-data">

                        <button type="submit" id="btn-submit-out" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-extrabold text-sm shadow-md shadow-rose-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer" style="touch-action: manipulation; min-height: 48px;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>CLOCK-OUT PULANG</span>
                        </button>
                    </form>
                @else
                    <!-- Selesai Dua-duanya -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-lg font-bold">
                            ✓
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Presensi Hari Ini Selesai</h4>
                        <p class="text-xs text-slate-500">
                            Masuk: {{ \Carbon\Carbon::parse($todayAttendance->clock_in)->format('H:i') }} &bull; Pulang: {{ \Carbon\Carbon::parse($todayAttendance->clock_out)->format('H:i') }}
                        </p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- Camera & Geolocation Scripts -->
<script>
    let videoStream = null;
    let userLat = null;
    let userLng = null;
    let userAccuracy = null;
    let capturedPhotoBase64 = null;
    let currentFacingMode = 'user';
    let availableVideoDevices = [];
    let currentDeviceIndex = 0;

    const officeTarget = {
        name: '{{ $targetLoc?->name ?? 'Kantor' }}',
        lat: {{ $targetLoc?->latitude ?? -6.2088 }},
        lng: {{ $targetLoc?->longitude ?? 106.8456 }},
        radius: {{ $targetLoc?->radius_meters ?? 200 }}
    };

    // 1. Inisialisasi Kamera
    async function enumerateCameraDevices() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            availableVideoDevices = devices.filter(d => d.kind === 'videoinput');
        } catch (err) {
            console.warn('Gagal membaca daftar kamera:', err);
        }
    }

    async function initCamera(facingMode = 'user', preferredDeviceId = null) {
        const video = document.getElementById('webcam-video');
        const cameraStatus = document.getElementById('camera-status');
        const fallback = document.getElementById('camera-fallback');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraStatus.textContent = 'Browser Tidak Didukung';
            cameraStatus.className = 'text-[11px] px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-semibold';
            fallback.classList.remove('hidden');
            return;
        }

        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }

        try {
            const constraints = {
                video: preferredDeviceId 
                    ? { deviceId: { exact: preferredDeviceId }, width: { ideal: 1280 }, height: { ideal: 720 } }
                    : { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            };

            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = videoStream;
            await video.play();

            cameraStatus.textContent = 'Kamera Aktif';
            cameraStatus.className = 'text-[11px] px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold';
            fallback.classList.add('hidden');
            await enumerateCameraDevices();
        } catch (err) {
            console.warn('Kamera utama gagal, mencoba fallback:', err);
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = videoStream;
                await video.play();
                cameraStatus.textContent = 'Kamera Aktif (Default)';
                cameraStatus.className = 'text-[11px] px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-semibold';
            } catch (fallbackErr) {
                cameraStatus.textContent = 'Kamera Ditolak / Tidak Ditemukan';
                cameraStatus.className = 'text-[11px] px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-semibold';
                fallback.classList.remove('hidden');
            }
        }
    }

    document.getElementById('btn-switch-cam').addEventListener('click', async () => {
        await enumerateCameraDevices();
        if (availableVideoDevices.length > 1) {
            currentDeviceIndex = (currentDeviceIndex + 1) % availableVideoDevices.length;
            const target = availableVideoDevices[currentDeviceIndex];
            await initCamera(currentFacingMode, target.deviceId);
        } else {
            currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
            await initCamera(currentFacingMode);
        }
    });

    // 2. Geolocation GPS
    function detectGPSLocation() {
        const badge = document.getElementById('gps-status-badge');
        badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Mengunci GPS...';
        badge.className = 'text-[11px] font-bold text-amber-600 flex items-center gap-1.5';

        if (!navigator.geolocation) {
            badge.textContent = 'GPS Tidak Didukung';
            badge.className = 'text-[11px] font-bold text-rose-600';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;
                userAccuracy = pos.coords.accuracy;

                document.getElementById('display-lat').textContent = userLat.toFixed(6);
                document.getElementById('display-lng').textContent = userLng.toFixed(6);
                document.getElementById('display-accuracy').textContent = `± ${Math.round(userAccuracy)}m`;

                badge.textContent = 'GPS Terkunci';
                badge.className = 'text-[11px] font-bold text-emerald-600';
                evaluateDistance();
            },
            (err) => {
                badge.textContent = 'Izin Lokasi Ditolak';
                badge.className = 'text-[11px] font-bold text-rose-600';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
    }

    function evaluateDistance() {
        if (userLat === null || userLng === null) return;
        const distanceMeters = calculateHaversineDistance(userLat, userLng, officeTarget.lat, officeTarget.lng);
        const display = document.getElementById('display-distance');
        const alertBox = document.getElementById('geofence-alert');
        const statusText = document.getElementById('geofence-status-text');

        display.textContent = `${distanceMeters} Meter`;

        if (distanceMeters <= officeTarget.radius) {
            display.className = 'font-bold text-emerald-600';
            alertBox.className = 'p-3 rounded-2xl border border-emerald-200 bg-emerald-50 text-xs text-emerald-800';
            statusText.textContent = `✓ Posisi valid di zona radius ${officeTarget.name} (Maks ${officeTarget.radius}m).`;
        } else {
            display.className = 'font-bold text-rose-600';
            alertBox.className = 'p-3 rounded-2xl border border-rose-200 bg-rose-50 text-xs text-rose-800';
            statusText.textContent = `Perhatian: Anda berada di luar radius kantor (${distanceMeters}m). Radius maksimal ${officeTarget.radius}m.`;
        }
    }

    window.simulateOfficeLocation = function(lat, lng) {
        userLat = lat;
        userLng = lng;
        userAccuracy = 5;
        document.getElementById('display-lat').textContent = userLat.toFixed(6) + ' (Simulasi)';
        document.getElementById('display-lng').textContent = userLng.toFixed(6) + ' (Simulasi)';
        document.getElementById('display-accuracy').textContent = '± 5m (Simulasi)';
        const badge = document.getElementById('gps-status-badge');
        badge.textContent = 'GPS Terkunci (Titik Kantor)';
        badge.className = 'text-[11px] font-bold text-emerald-600';
        evaluateDistance();
    };

    function captureSelfie() {
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('snapshot-canvas');
        const preview = document.getElementById('snapshot-preview');
        const btnRetake = document.getElementById('btn-retake');

        if (capturedPhotoBase64 && !videoStream) return capturedPhotoBase64;
        if (!video || !video.videoWidth) return null;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');

        if (currentFacingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        capturedPhotoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        preview.src = capturedPhotoBase64;
        preview.classList.remove('hidden');
        btnRetake.classList.remove('hidden');

        return capturedPhotoBase64;
    }

    document.getElementById('btn-retake').addEventListener('click', () => {
        document.getElementById('snapshot-preview').classList.add('hidden');
        document.getElementById('btn-retake').classList.add('hidden');
        capturedPhotoBase64 = null;
    });

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

    window.handleAttendanceSubmit = function(event, type) {
        if (userLat === null || userLng === null) {
            alert('Lokasi GPS belum terdeteksi. Silakan klik "Simulasi GPS" jika menguji di browser komputer.');
            detectGPSLocation();
            return false;
        }

        const photo = captureSelfie();
        if (!photo) {
            alert('Silakan ambil foto selfie atau unggah gambar selfie terlebih dahulu.');
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

    document.addEventListener('DOMContentLoaded', () => {
        initCamera();
        detectGPSLocation();
    });
</script>
@endsection
