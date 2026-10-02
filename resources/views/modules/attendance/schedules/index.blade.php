@extends('layouts.app')

@section('header', 'Roster & Jadwal Kerja Karyawan')

@section('actions')
    <button type="button" onclick="openScheduleModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Atur Jadwal / Roster Pegawai</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center justify-between text-sm">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('schedules.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Penjadwalan</label>
                <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="department_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Departemen</label>
                <select id="department_id" name="department_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Roster Jadwal Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">
                    Jadwal Kerja Tanggal {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Penugasan shift dan lokasi presensi harian per individu</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-full">
                {{ $schedules->total() }} Jadwal Ditetapkan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Departemen</th>
                        <th class="py-3 px-5">Shift Kerja</th>
                        <th class="py-3 px-5">Titik Kantor / Geofence</th>
                        <th class="py-3 px-5">Status Hari</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $sched)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <a href="{{ route('employees.show', $sched->employee) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                    {{ $sched->employee?->full_name ?? '-' }}
                                </a>
                                <div class="text-xs text-slate-400 font-mono">{{ $sched->employee?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-slate-700">
                                {{ $sched->employee?->department?->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($sched->is_day_off)
                                    <span class="text-slate-400 italic text-xs">Libur (Day Off)</span>
                                @else
                                    <span class="font-semibold text-slate-900 block text-xs">{{ $sched->shift?->name ?? 'Default' }}</span>
                                    <span class="text-[11px] text-indigo-600 font-mono">
                                        {{ $sched->shift?->start_time ?? '08:00' }} - {{ $sched->shift?->end_time ?? '17:00' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                @if ($sched->is_day_off)
                                    —
                                @else
                                    <span class="font-semibold block">{{ $sched->officeLocation?->name ?? 'Kantor Pusat' }}</span>
                                    <span class="text-[10px] text-slate-400">Radius: {{ $sched->officeLocation?->radius_meters ?? 100 }}m</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($sched->is_day_off)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Hari Libur / Off
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Masuk Kerja
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <form action="{{ route('schedules.destroy', $sched) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal ini?')">
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
                                Belum ada roster jadwal untuk tanggal ini. Klik tombol di atas untuk menetapkan jadwal.
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

<!-- Modal Atur Jadwal Pegawai -->
<div id="schedule-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 class="font-bold text-slate-900 text-base">Atur Jadwal Kerja Karyawan</h4>
            <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">
                &times;
            </button>
        </div>

        <form action="{{ route('schedules.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="sched-employee" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pilih Karyawan *</label>
                <select id="sched-employee" name="employee_id" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Pilih Pegawai —</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->full_name }} ({{ $emp->nik }}) &bull; {{ $emp->department?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sched-date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal *</label>
                <input type="date" id="sched-date" name="date" value="{{ $selectedDate }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="sched-shift" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Shift Kerja</label>
                <select id="sched-shift" name="shift_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Pilih Shift —</option>
                    @foreach ($shifts as $sh)
                        <option value="{{ $sh->id }}">
                            {{ $sh->name }} ({{ substr($sh->start_time, 0, 5) }} - {{ substr($sh->end_time, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sched-loc" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Titik Kantor / Geofence</label>
                <select id="sched-loc" name="office_location_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Pilih Titik Kantor —</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}">
                            {{ $loc->name }} (Radius {{ $loc->radius_meters }}m)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="sched-day-off" name="is_day_off" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="sched-day-off" class="text-xs text-slate-700 font-medium">Tetapkan Sebagai Hari Libur (Day Off)</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openScheduleModal() {
        document.getElementById('schedule-modal').classList.remove('hidden');
    }

    function closeScheduleModal() {
        document.getElementById('schedule-modal').classList.add('hidden');
    }
</script>
@endsection
