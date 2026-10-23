@extends('layouts.app')

@section('header', 'Kalender Hari Libur Nasional & Cuti Bersama')

@section('actions')
    <!-- Tombol Sinkronisasi Standar Libur Nasional SKB 3 Menteri -->
    <form action="{{ route('holidays.seed') }}" method="POST" class="inline" onsubmit="return confirm('Sinkronkan seluruh daftar Hari Libur Nasional dan Cuti Bersama resmi SKB 3 Menteri untuk tahun {{ $year }}?')">
        @csrf
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-xl border border-amber-200 shadow-xs transition-colors cursor-pointer">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Sinkron Libur Nasional {{ $year }}</span>
        </button>
    </form>

    <button type="button" onclick="openHolidayModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>+ Tambah Hari Libur / Cuti</span>
    </button>
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

    <!-- Banner Info Otomasi Kalender -->
    <div class="p-5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-slate-900 text-sm md:text-base">Pengelolaan Kalender Libur & Cuti Massal Terpusat</h2>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                    Semua tanggal yang tercatat di bawah secara otomatis dikenali oleh modul presensi & roster sebagai hari libur. Karyawan yang tetap masuk bekerja pada tanggal libur akan otomatis dicatat sebagai <strong>Lembur Hari Libur (Holiday Overtime)</strong> yang memerlukan persetujuan atasan.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-amber-200 text-amber-900 text-xs font-bold shadow-xs">
                Tahun Aktif: {{ $year }}
            </span>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-xs text-rose-700 font-bold uppercase tracking-wider block">Hari Libur Nasional</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $nationalCount }} Hari</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Berlaku untuk seluruh kantor korporat</span>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-xs text-blue-700 font-bold uppercase tracking-wider block">Cuti Bersama (Mass Leave)</span>
            <span class="text-2xl font-extrabold text-blue-700 mt-1 block">{{ $massLeaveCount }} Hari</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Sesuai ketetapan pemerintah SKB</span>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider block">Libur Perusahaan / Regional</span>
            <span class="text-2xl font-extrabold text-emerald-700 mt-1 block">{{ $companyCount }} Hari</span>
            <span class="text-[11px] text-slate-500 mt-1 block">HUT instansi / libur lokal cabang</span>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Total Tanggal Libur</span>
            <span class="text-2xl font-extrabold text-indigo-700 mt-1 block">{{ $holidays->count() }} Hari</span>
            <span class="text-[11px] text-slate-500 mt-1 block">Tercatat di sistem kalender {{ $year }}</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('holidays.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label for="filter-year" class="block text-xs font-semibold text-slate-700 mb-1">Tahun Kalender</label>
                <select id="filter-year" name="year" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    @for ($y = Carbon\Carbon::now()->year - 2; $y <= Carbon\Carbon::now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="filter-type" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Hari Libur</label>
                <select id="filter-type" name="type" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">Semua Jenis</option>
                    <option value="NATIONAL" {{ $type === 'NATIONAL' ? 'selected' : '' }}>Hari Libur Nasional</option>
                    <option value="MASS_LEAVE" {{ $type === 'MASS_LEAVE' ? 'selected' : '' }}>Cuti Bersama (Mass Leave)</option>
                    <option value="COMPANY" {{ $type === 'COMPANY' ? 'selected' : '' }}>Libur Perusahaan</option>
                    <option value="REGIONAL" {{ $type === 'REGIONAL' ? 'selected' : '' }}>Libur Regional / Cabang</option>
                </select>
            </div>

            <div>
                <label for="filter-location" class="block text-xs font-semibold text-slate-700 mb-1">Cakupan Cabang / Lokasi</label>
                <select id="filter-location" name="office_location_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('holidays.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Hari Libur & Cuti Bersama -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Hari Libur & Cuti Bersama Tahun {{ $year }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Penetapan terpusat berlaku untuk seluruh roster shift kerja dan sistem presensi GPS</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full">
                {{ $holidays->count() }} Hari Libur Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                        <th class="py-3 px-5">Tanggal</th>
                        <th class="py-3 px-5">Hari</th>
                        <th class="py-3 px-5">Nama Hari Libur / Cuti</th>
                        <th class="py-3 px-5">Jenis Ketetapan</th>
                        <th class="py-3 px-5">Cakupan Cabang</th>
                        <th class="py-3 px-5">Potong Cuti Tahunan</th>
                        <th class="py-3 px-5">Keterangan</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($holidays as $h)
                        @php
                            $badge = match($h->type) {
                                'NATIONAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'MASS_LEAVE' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'COMPANY' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'REGIONAL' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-slate-900 font-mono text-xs whitespace-nowrap">
                                {{ $h->date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-600 font-semibold whitespace-nowrap">
                                {{ $h->date->translatedFormat('l') }}
                            </td>
                            <td class="py-3.5 px-5 font-bold text-slate-800">
                                {{ $h->name }}
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badge }}">
                                    {{ $h->type_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                @if ($h->officeLocation)
                                    <span class="font-semibold text-amber-800">{{ $h->officeLocation->name }}</span>
                                @else
                                    <span class="font-semibold text-slate-600">Seluruh Kantor (Nasional)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs">
                                @if ($h->reduces_annual_leave)
                                    <span class="inline-flex items-center gap-1 font-semibold text-amber-700">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Memotong Kuota
                                    </span>
                                @else
                                    <span class="text-slate-400">Tidak</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-500 max-w-xs truncate">
                                {{ $h->description ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="editHoliday({{ $h->id }}, '{{ addslashes($h->name) }}', '{{ $h->date->format('Y-m-d') }}', '{{ $h->type }}', '{{ $h->office_location_id }}', {{ $h->reduces_annual_leave ? 'true' : 'false' }}, '{{ addslashes($h->description ?? '') }}')" 
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold px-2 py-1 rounded hover:bg-indigo-50 transition-colors cursor-pointer">
                                        Edit
                                    </button>

                                    <form action="{{ route('holidays.destroy', $h) }}" method="POST" onsubmit="return confirm('Hapus hari libur {{ $h->name }} dari kalender?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-slate-400 text-sm">
                                Belum ada hari libur atau cuti bersama yang dicatat untuk tahun {{ $year }}.<br>
                                <span class="text-xs text-indigo-600 mt-1 inline-block">Klik tombol <strong>"Sinkron Libur Nasional {{ $year }}"</strong> di atas untuk memuat kalender resmi SKB 3 Menteri secara instan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Edit Hari Libur -->
<div id="holiday-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl border border-slate-200">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h4 id="holiday-modal-title" class="font-bold text-slate-900 text-base">Tambah Hari Libur / Cuti Bersama</h4>
            <button type="button" onclick="closeHolidayModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>
        </div>

        <form id="holiday-form" action="{{ route('holidays.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="holiday-form-method" value="POST">

            <div>
                <label for="holiday-name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Hari Libur / Cuti *</label>
                <input type="text" id="holiday-name" name="name" required placeholder="Contoh: Hari Raya Idul Fitri 1447 H" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="holiday-date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" id="holiday-date" name="date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl">
                </div>

                <div>
                    <label for="holiday-type" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Penetapan *</label>
                    <select id="holiday-type" name="type" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl">
                        <option value="NATIONAL">Hari Libur Nasional</option>
                        <option value="MASS_LEAVE">Cuti Bersama (Mass Leave)</option>
                        <option value="COMPANY">Libur Khusus Perusahaan</option>
                        <option value="REGIONAL">Libur Regional / Cabang</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="holiday-location" class="block text-xs font-semibold text-slate-700 mb-1">Cakupan Kantor / Lokasi</label>
                <select id="holiday-location" name="office_location_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl">
                    <option value="">— Berlaku Nasional (Seluruh Kantor) —</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="holiday-reduces" name="reduces_annual_leave" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="holiday-reduces" class="text-xs text-slate-700 font-medium">
                    Potong Kuota Cuti Tahunan Karyawan (Umumnya untuk Cuti Bersama)
                </label>
            </div>

            <div>
                <label for="holiday-desc" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Dasar SK</label>
                <textarea id="holiday-desc" name="description" rows="2" placeholder="Contoh: SKB 3 Menteri No. ... / Keputusan Direksi..." class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 rounded-xl"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeHolidayModal()" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                    Simpan Kalender
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openHolidayModal() {
        document.getElementById('holiday-modal-title').innerText = 'Tambah Hari Libur / Cuti Bersama';
        document.getElementById('holiday-form').action = "{{ route('holidays.store') }}";
        document.getElementById('holiday-form-method').value = 'POST';
        document.getElementById('holiday-name').value = '';
        document.getElementById('holiday-date').value = "{{ date('Y-m-d') }}";
        document.getElementById('holiday-type').value = 'NATIONAL';
        document.getElementById('holiday-location').value = '';
        document.getElementById('holiday-reduces').checked = false;
        document.getElementById('holiday-desc').value = '';
        document.getElementById('holiday-modal').classList.remove('hidden');
    }

    function editHoliday(id, name, date, type, locationId, reduces, desc) {
        document.getElementById('holiday-modal-title').innerText = 'Edit Hari Libur / Cuti';
        document.getElementById('holiday-form').action = "/attendance/holidays/" + id;
        document.getElementById('holiday-form-method').value = 'PUT';
        document.getElementById('holiday-name').value = name;
        document.getElementById('holiday-date').value = date;
        document.getElementById('holiday-type').value = type;
        document.getElementById('holiday-location').value = locationId || '';
        document.getElementById('holiday-reduces').checked = reduces;
        document.getElementById('holiday-desc').value = desc;
        document.getElementById('holiday-modal').classList.remove('hidden');
    }

    function closeHolidayModal() {
        document.getElementById('holiday-modal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeHolidayModal();
    });
</script>
@endsection
