@extends('layouts.app')

@section('header', 'Master Lokasi Kantor & Geofencing')

@section('actions')
    <button type="button" onclick="openLocationModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Lokasi Kantor</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center justify-between text-sm">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Tabel Master Lokasi Kantor -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Titik Presensi / Geofence</h3>
                <p class="text-xs text-slate-400 mt-0.5">Koordinat pusat dan toleransi radius meter untuk validasi anti-fake GPS</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-full">
                {{ $locations->count() }} Lokasi Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Nama Lokasi / Kantor</th>
                        <th class="py-3 px-5">Koordinat (Latitude, Longitude)</th>
                        <th class="py-3 px-5">Radius Geofence</th>
                        <th class="py-3 px-5">Status</th>
                        <th class="py-3 px-5">Karyawan Terjadwal</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($locations as $loc)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <span class="font-bold text-slate-900 block">{{ $loc->name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">ID: #LOC-{{ $loc->id }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs">
                                <span class="text-slate-800">{{ $loc->latitude }}, {{ $loc->longitude }}</span>
                                <a href="https://maps.google.com/?q={{ $loc->latitude }},{{ $loc->longitude }}" target="_blank" class="text-indigo-600 hover:underline block text-[10px] font-sans mt-0.5">
                                    Buka Google Maps &rarr;
                                </a>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold">
                                    {{ $loc->radius_meters }} Meter
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($loc->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-500">
                                {{ $loc->schedules_count }} Penugasan
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-2">
                                <button type="button" 
                                        onclick="editLocation({{ json_encode($loc) }})"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    Edit
                                </button>
                                <form action="{{ route('locations.destroy', $loc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus lokasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-sm">
                                Belum ada titik kantor yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Edit Lokasi -->
<div id="location-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 id="location-modal-title" class="font-bold text-slate-900 text-base">Tambah Titik Kantor</h4>
            <button type="button" onclick="closeLocationModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">
                &times;
            </button>
        </div>

        <form id="location-form" action="{{ route('locations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="method-container"></div>

            <div>
                <label for="loc-name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lokasi / Cabang *</label>
                <input type="text" id="loc-name" name="name" required placeholder="Contoh: Kantor Pusat Jakarta" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="loc-lat" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Latitude *</label>
                    <input type="number" step="0.0000001" id="loc-lat" name="latitude" required placeholder="-6.2088" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="loc-lng" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Longitude *</label>
                    <input type="number" step="0.0000001" id="loc-lng" name="longitude" required placeholder="106.8456" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <button type="button" onclick="autofillCurrentGPS()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <span>Gunakan Koordinat GPS Saya Saat Ini</span>
                </button>
            </div>

            <div>
                <label for="loc-radius" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Radius Toleransi Geofence (Meter) *</label>
                <input type="number" id="loc-radius" name="radius_meters" value="100" min="10" max="5000" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Pegawai yang absen melebihi radius ini akan ditolak.</p>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="loc-active" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="loc-active" class="text-xs text-slate-700 font-medium">Titik Lokasi Aktif Digunakan</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeLocationModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    Simpan Titik Kantor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLocationModal() {
        document.getElementById('location-modal-title').textContent = 'Tambah Titik Kantor';
        document.getElementById('location-form').action = "{{ route('locations.store') }}";
        document.getElementById('method-container').innerHTML = '';
        document.getElementById('loc-name').value = '';
        document.getElementById('loc-lat').value = '';
        document.getElementById('loc-lng').value = '';
        document.getElementById('loc-radius').value = '100';
        document.getElementById('loc-active').checked = true;
        document.getElementById('location-modal').classList.remove('hidden');
    }

    function editLocation(loc) {
        document.getElementById('location-modal-title').textContent = 'Edit Titik Kantor: ' + loc.name;
        document.getElementById('location-form').action = "/attendance/locations/" + loc.id;
        document.getElementById('method-container').innerHTML = '@method("PUT")';
        document.getElementById('loc-name').value = loc.name;
        document.getElementById('loc-lat').value = loc.latitude;
        document.getElementById('loc-lng').value = loc.longitude;
        document.getElementById('loc-radius').value = loc.radius_meters;
        document.getElementById('loc-active').checked = loc.is_active;
        document.getElementById('location-modal').classList.remove('hidden');
    }

    function closeLocationModal() {
        document.getElementById('location-modal').classList.add('hidden');
    }

    function autofillCurrentGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('loc-lat').value = pos.coords.latitude.toFixed(7);
                    document.getElementById('loc-lng').value = pos.coords.longitude.toFixed(7);
                },
                (err) => alert('Gagal mendeteksi lokasi: ' + err.message),
                { enableHighAccuracy: true }
            );
        } else {
            alert('Browser tidak mendukung Geolocation.');
        }
    }
</script>
@endsection
