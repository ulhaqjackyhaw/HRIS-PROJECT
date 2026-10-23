@extends('layouts.app')

@section('header', 'Roster & Jadwal Kerja Karyawan')

@section('actions')
    <!-- Tombol Rotasi Otomatis (Auto-Roster) -->
    <button type="button" onclick="openAutoRosterModal()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-xl border border-amber-200 shadow-xs transition-colors cursor-pointer">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>⚡ Rotasi Otomatis (Auto-Roster)</span>
    </button>

    <!-- Tombol Penetapan Massal (Mass Assignment Shift) -->
    <button type="button" onclick="openMassAssignModal()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-xl border border-indigo-200 shadow-xs transition-colors cursor-pointer">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span>+ Penetapan Massal (Grup / Departemen)</span>
    </button>

    <!-- Tombol Atur Jadwal Satuan Individu -->
    <button type="button" onclick="openScheduleModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>+ Atur Jadwal Satuan</span>
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

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="font-bold block text-rose-900">Gagal</span>
                    <span class="text-xs text-rose-700">{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold p-1">
                &times;
            </button>
        </div>
    @endif

    <!-- Indikator Hari Libur Nasional / Cuti Bersama Pada Tanggal Terpilih -->
    @if ($holiday)
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-rose-900 text-sm">Tanggal Ini Terdaftar Sebagai: {{ $holiday->name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white text-rose-700 border border-rose-200">
                            {{ $holiday->type_label }}
                        </span>
                    </div>
                    <p class="text-xs text-rose-700 mt-0.5">
                        Sistem mengenali tanggal ini sebagai hari libur terpusat. Karyawan yang tetap masuk bertugas akan otomatis dicatat sebagai <strong>Lembur Hari Libur (Holiday Overtime)</strong> setelah clock-out.
                    </p>
                </div>
            </div>
            <a href="{{ route('holidays.index', ['year' => Carbon\Carbon::parse($selectedDate)->year]) }}" class="px-3 py-1.5 bg-white hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                Buka Kalender Libur &rarr;
            </a>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-xs">
        <form action="{{ route('schedules.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label for="filter-date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Roster</label>
                <input type="date" id="filter-date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
            </div>

            <div>
                <label for="filter-dept" class="block text-xs font-semibold text-slate-700 mb-1">Departemen</label>
                <select id="filter-dept" name="department_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-shift" class="block text-xs font-semibold text-slate-700 mb-1">Shift / Status Kerja</label>
                <select id="filter-shift" name="shift_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">Semua Shift & Libur</option>
                    @foreach ($shifts as $sh)
                        <option value="{{ $sh->id }}" {{ $shiftId == $sh->id ? 'selected' : '' }}>
                            {{ $sh->name }} ({{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }})
                        </option>
                    @endforeach
                    <option value="off" {{ $shiftId === 'off' ? 'selected' : '' }}>Hanya Hari Libur / Day Off</option>
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Roster Jadwal Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">
                    Jadwal Kerja Tanggal {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Penugasan shift, jam kerja, dan lokasi geofence presensi per individu</p>
            </div>
            <span class="px-3 py-1 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full">
                {{ $schedules->total() }} Pegawai Terjadwal
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-slate-500 font-semibold text-xs">
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Departemen & Jabatan</th>
                        <th class="py-3 px-5">Shift Kerja</th>
                        <th class="py-3 px-5">Jam Kerja Shift</th>
                        <th class="py-3 px-5">Titik Kantor / Geofence</th>
                        <th class="py-3 px-5">Status Hari</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $sched)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <a href="{{ route('employees.show', $sched->employee) }}" class="font-bold text-slate-900 hover:text-indigo-600 hover:underline">
                                    {{ $sched->employee?->full_name ?? '-' }}
                                </a>
                                <div class="text-xs text-slate-400 font-mono">{{ $sched->employee?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                <span class="font-semibold text-slate-800">{{ $sched->employee?->department?->name ?? '-' }}</span>
                                <span class="text-slate-400 block text-[11px]">{{ $sched->employee?->position?->title ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($sched->is_day_off)
                                    <span class="text-slate-400 italic text-xs">Libur (Day Off)</span>
                                @else
                                    <span class="font-bold text-slate-800 text-xs block">{{ $sched->shift?->name ?? 'Default' }}</span>
                                    @if ($sched->shift?->code)
                                        <span class="text-[10px] text-slate-500 font-mono">Kode: {{ $sched->shift->code }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs text-slate-700">
                                @if ($sched->is_day_off)
                                    —
                                @else
                                    <span class="font-bold text-indigo-700">
                                        {{ substr($sched->shift?->start_time ?? '08:00', 0, 5) }} - {{ substr($sched->shift?->end_time ?? '17:00', 0, 5) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                @if ($sched->is_day_off)
                                    —
                                @else
                                    <span class="font-semibold block">{{ $sched->officeLocation?->name ?? $sched->employee?->work_location ?? 'Kantor Pusat' }}</span>
                                    @if ($sched->officeLocation)
                                        <span class="text-[10px] text-slate-400">Radius: {{ $sched->officeLocation->radius_meters }}m</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($sched->is_day_off)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Libur / Off
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Masuk Bekerja
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <form action="{{ route('schedules.destroy', $sched) }}" method="POST" class="inline" onsubmit="return confirm('Hapus entri jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-slate-400 text-sm">
                                Belum ada penugasan roster untuk tanggal {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.<br>
                                <span class="text-xs text-slate-500 mt-1 inline-block">Gunakan tombol <strong>"Penetapan Massal"</strong> atau <strong>"Auto-Roster"</strong> di atas untuk mengatur shift departemen sekaligus.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: PENETAPAN MASSAL JADWAL (MASS ASSIGNMENT SHIFT) -->
<!-- ========================================================================= -->
<div id="mass-assign-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-xl border border-slate-200 animate-in fade-in duration-150 max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Penetapan Massal Shift (Mass Assignment)</h4>
                    <p class="text-xs text-slate-500">Terapkan jadwal kerja ke seluruh grup, departemen, atau cabang sekaligus</p>
                </div>
            </div>
            <button type="button" onclick="closeMassAssignModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>
        </div>

        <form action="{{ route('schedules.mass-assign') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf

            <!-- Target Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Sasaran Karyawan *</label>
                <select name="target_type" id="mass-target-type" onchange="toggleMassTargetFields()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="department">Berdasarkan Departemen (Contoh: Produksi, IT, HC)</option>
                    <option value="all">Seluruh Karyawan Aktif (Semua Unit)</option>
                    <option value="position">Berdasarkan Formasi / Jabatan</option>
                    <option value="location">Berdasarkan Lokasi Kerja (Cabang / Site)</option>
                    <option value="selected_employees">Pilih Karyawan Tertentu Saja</option>
                </select>
            </div>

            <!-- Conditional Department -->
            <div id="mass-field-dept">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Departemen Sasaran *</label>
                <select name="department_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Conditional Position -->
            <div id="mass-field-pos" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Jabatan Sasaran *</label>
                <select name="position_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    @foreach ($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->title }} ({{ $pos->level ?? 'Staff' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Conditional Location -->
            <div id="mass-field-loc" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Lokasi Kerja / Cabang *</label>
                <select name="work_location" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="Head Office Jakarta">Head Office Jakarta</option>
                    <option value="Cabang Bandung">Cabang Bandung</option>
                    <option value="Cabang Surabaya">Cabang Surabaya</option>
                    <option value="Site Semarang">Site Semarang</option>
                </select>
            </div>

            <!-- Conditional Selected Employees -->
            <div id="mass-field-emps" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Pegawai (Tahan Ctrl / Cmd untuk pilih banyak)</label>
                <select name="employee_ids[]" multiple class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 h-28">
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->nik }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" required value="{{ $selectedDate }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Berakhir *</label>
                    <input type="date" name="end_date" required value="{{ Carbon\Carbon::parse($selectedDate)->addDays(6)->toDateString() }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                </div>
            </div>

            <!-- Shift & Office Location -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Shift Kerja *</label>
                    <select name="shift_id" id="mass-shift-id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        @foreach ($shifts as $sh)
                            <option value="{{ $sh->id }}">
                                {{ $sh->name }} ({{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Titik Kantor / Geofence</label>
                    <select name="office_location_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        <option value="">— Default Kantor Pusat / Masing-masing —</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} (Radius {{ $loc->radius_meters }}m)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Options: Day Off & Skip Holidays -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="mass-day-off" name="is_day_off" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <label for="mass-day-off" class="text-xs text-slate-700 font-semibold">
                        Tetapkan Sebagai Hari Libur / Off (Bukan Hari Kerja)
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="mass-skip-holidays" name="skip_national_holidays" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <label for="mass-skip-holidays" class="text-xs text-slate-700 font-semibold">
                        Otomatis Liburkan pada Hari Libur Nasional & Cuti Bersama yang Terdaftar
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeMassAssignModal()" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                    Terapkan Jadwal Massal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: ROTASI OTOMATIS (AUTO-ROSTER PATTERN GENERATOR) -->
<!-- ========================================================================= -->
<div id="auto-roster-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-xl border border-slate-200 animate-in fade-in duration-150 max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Rotasi Otomatis (Auto-Roster Generator)</h4>
                    <p class="text-xs text-slate-500">Membuat pola jadwal bergilir (misal: 3 hari kerja - 2 hari libur) otomatis</p>
                </div>
            </div>
            <button type="button" onclick="closeAutoRosterModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>
        </div>

        <form action="{{ route('schedules.auto-roster') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf

            <!-- Target Group -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Sasaran Karyawan *</label>
                    <select name="target_type" id="roster-target-type" onchange="toggleRosterTargetFields()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        <option value="department">Berdasarkan Departemen</option>
                        <option value="all">Seluruh Karyawan Aktif</option>
                        <option value="position">Berdasarkan Jabatan</option>
                        <option value="location">Berdasarkan Cabang / Lokasi</option>
                    </select>
                </div>

                <div id="roster-field-dept">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Departemen *</label>
                    <select name="department_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="roster-field-pos" class="hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan *</label>
                    <select name="position_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        @foreach ($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="roster-field-loc" class="hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi *</label>
                    <select name="work_location" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                        <option value="Head Office Jakarta">Head Office Jakarta</option>
                        <option value="Cabang Bandung">Cabang Bandung</option>
                        <option value="Cabang Surabaya">Cabang Surabaya</option>
                        <option value="Site Semarang">Site Semarang</option>
                    </select>
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai Periode Rotasi *</label>
                    <input type="date" name="start_date" required value="{{ $selectedDate }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai Periode Rotasi *</label>
                    <input type="date" name="end_date" required value="{{ Carbon\Carbon::parse($selectedDate)->addDays(29)->toDateString() }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                </div>
            </div>

            <!-- Pattern Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pola Rotasi Bergilir (Auto-Pattern) *</label>
                <select name="pattern_type" id="roster-pattern-type" onchange="toggleRosterShiftFields()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="3_2">3 Hari Kerja - 2 Hari Libur (Pola Bergilir 3 On / 2 Off)</option>
                    <option value="5_2">5 Hari Kerja - 2 Hari Libur (Pola 5 On / 2 Off)</option>
                    <option value="4_2">4 Hari Kerja - 2 Hari Libur (Pola 4 On / 2 Off)</option>
                    <option value="6_1">6 Hari Kerja - 1 Hari Libur (Pola 6 On / 1 Off)</option>
                    <option value="ROTATING_3_SHIFT">Rotasi 3 Shift (Pagi &rarr; Siang &rarr; Malam &rarr; Off)</option>
                </select>
            </div>

            <!-- Single Shift for On/Off Patterns -->
            <div id="roster-single-shift">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Kerja yang Digunakan *</label>
                <select name="work_shift_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    @foreach ($shifts as $sh)
                        <option value="{{ $sh->id }}">
                            {{ $sh->name }} ({{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3 Rotating Shifts Configuration -->
            <div id="roster-multi-shifts" class="hidden space-y-3 p-3.5 bg-amber-50/70 border border-amber-200 rounded-xl">
                <span class="text-xs font-bold text-amber-900 block">Konfigurasi Urutan 3 Shift Bergilir:</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">1. Shift Pagi</label>
                        <select name="morning_shift_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            @foreach ($shifts as $sh)
                                <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">2. Shift Siang</label>
                        <select name="afternoon_shift_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            @foreach ($shifts as $sh)
                                <option value="{{ $sh->id }}" {{ $loop->index == 1 ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">3. Shift Malam</label>
                        <select name="night_shift_id" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
                            @foreach ($shifts as $sh)
                                <option value="{{ $sh->id }}" {{ $loop->index >= 2 ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <span class="text-[11px] text-amber-700 block italic">Hari ke-4 otomatis ditetapkan sebagai Hari Libur (Day Off).</span>
            </div>

            <!-- Office Location -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Titik Kantor / Geofence</label>
                <select name="office_location_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">— Default Kantor Pusat / Masing-masing —</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} (Radius {{ $loc->radius_meters }}m)</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                <input type="checkbox" id="roster-skip-holidays" name="skip_national_holidays" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="roster-skip-holidays" class="text-xs text-slate-700 font-semibold">
                    Liburkan Otomatis pada Hari Libur Nasional & Cuti Bersama
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAutoRosterModal()" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                    Generate Auto-Roster
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: ATUR JADWAL SATUAN INDIVIDU -->
<!-- ========================================================================= -->
<div id="schedule-modal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl border border-slate-200">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h4 class="font-bold text-slate-900 text-base">Atur Jadwal Kerja Satuan</h4>
            <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>
        </div>

        <form action="{{ route('schedules.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="sched-employee" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Karyawan *</label>
                <select id="sched-employee" name="employee_id" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">— Pilih Pegawai —</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->full_name }} ({{ $emp->nik }}) &bull; {{ $emp->department?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sched-date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal *</label>
                <input type="date" id="sched-date" name="date" value="{{ $selectedDate }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
            </div>

            <div>
                <label for="sched-shift" class="block text-xs font-semibold text-slate-700 mb-1">Shift Kerja</label>
                <select id="sched-shift" name="shift_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">— Pilih Shift —</option>
                    @foreach ($shifts as $sh)
                        <option value="{{ $sh->id }}">
                            {{ $sh->name }} ({{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sched-loc" class="block text-xs font-semibold text-slate-700 mb-1">Titik Kantor / Geofence</label>
                <select id="sched-loc" name="office_location_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-xl focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600">
                    <option value="">— Default Kantor Pusat / Masing-masing —</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">
                            {{ $loc->name }} (Radius {{ $loc->radius_meters }}m)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="sched-day-off" name="is_day_off" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="sched-day-off" class="text-xs text-slate-700 font-semibold">Tetapkan Sebagai Hari Libur (Day Off)</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Single Modal
    function openScheduleModal() {
        document.getElementById('schedule-modal').classList.remove('hidden');
    }
    function closeScheduleModal() {
        document.getElementById('schedule-modal').classList.add('hidden');
    }

    // Mass Assign Modal
    function openMassAssignModal() {
        document.getElementById('mass-assign-modal').classList.remove('hidden');
    }
    function closeMassAssignModal() {
        document.getElementById('mass-assign-modal').classList.add('hidden');
    }

    function toggleMassTargetFields() {
        const val = document.getElementById('mass-target-type').value;
        document.getElementById('mass-field-dept').classList.toggle('hidden', val !== 'department');
        document.getElementById('mass-field-pos').classList.toggle('hidden', val !== 'position');
        document.getElementById('mass-field-loc').classList.toggle('hidden', val !== 'location');
        document.getElementById('mass-field-emps').classList.toggle('hidden', val !== 'selected_employees');
    }

    // Auto-Roster Modal
    function openAutoRosterModal() {
        document.getElementById('auto-roster-modal').classList.remove('hidden');
    }
    function closeAutoRosterModal() {
        document.getElementById('auto-roster-modal').classList.add('hidden');
    }

    function toggleRosterTargetFields() {
        const val = document.getElementById('roster-target-type').value;
        document.getElementById('roster-field-dept').classList.toggle('hidden', val !== 'department');
        document.getElementById('roster-field-pos').classList.toggle('hidden', val !== 'position');
        document.getElementById('roster-field-loc').classList.toggle('hidden', val !== 'location');
    }

    function toggleRosterShiftFields() {
        const val = document.getElementById('roster-pattern-type').value;
        const isMulti = (val === 'ROTATING_3_SHIFT');
        document.getElementById('roster-single-shift').classList.toggle('hidden', isMulti);
        document.getElementById('roster-multi-shifts').classList.toggle('hidden', !isMulti);
    }

    // Escape listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeScheduleModal();
            closeMassAssignModal();
            closeAutoRosterModal();
        }
    });
</script>
@endsection
