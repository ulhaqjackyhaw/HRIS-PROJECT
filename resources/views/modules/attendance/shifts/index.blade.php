@extends('layouts.app')

@section('header', 'Master Shift & Kebijakan Jam Kerja')

@section('actions')
    <button type="button" onclick="openShiftModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Shift Baru</span>
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

    <!-- Tabel Master Shift -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Shift Kerja Perusahaan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Konfigurasi jam masuk, jam pulang, toleransi keterlambatan, dan shift malam</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-full">
                {{ $shifts->count() }} Shift Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Kode Shift</th>
                        <th class="py-3 px-5">Nama Shift</th>
                        <th class="py-3 px-5">Jam Masuk &rarr; Jam Pulang</th>
                        <th class="py-3 px-5">Toleransi Telat</th>
                        <th class="py-3 px-5">Shift Malam (Overnight)</th>
                        <th class="py-3 px-5">Penggunaan Jadwal</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($shifts as $shift)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                                {{ $shift->code }}
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                {{ $shift->name }}
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs">
                                <span class="text-indigo-600 font-bold">{{ substr($shift->start_time, 0, 5) }}</span>
                                <span class="text-slate-400 mx-1">&rarr;</span>
                                <span class="text-slate-700 font-bold">{{ substr($shift->end_time, 0, 5) }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-semibold">
                                    {{ $shift->late_tolerance_minutes }} Menit
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($shift->is_overnight)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        Lintas Hari (Overnight)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Reguler Hari Sama
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-500">
                                {{ $shift->schedules_count }} Jadwal Aktif
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-2">
                                <button type="button" 
                                        onclick="editShift({{ json_encode($shift) }})"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    Edit
                                </button>
                                @if ($shift->schedules_count === 0)
                                    <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="inline" onsubmit="return confirm('Hapus shift ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">
                                Belum ada shift yang didaftarkan. Klik tombol di atas untuk menambah shift.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Edit Shift -->
<div id="shift-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 id="shift-modal-title" class="font-bold text-slate-900 text-base">Tambah Shift Baru</h4>
            <button type="button" onclick="closeShiftModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">
                &times;
            </button>
        </div>

        <form id="shift-form" action="{{ route('shifts.store') }}" method="POST" class="space-y-4">
            @csrf
            <div id="method-container"></div>

            <div>
                <label for="shift-code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kode Shift *</label>
                <input type="text" id="shift-code" name="code" required placeholder="Contoh: SHIFT-PAGI" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="shift-name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Shift *</label>
                <input type="text" id="shift-name" name="name" required placeholder="Contoh: Shift Pagi Operasional" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="shift-start" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Masuk *</label>
                    <input type="time" id="shift-start" name="start_time" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="shift-end" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Pulang *</label>
                    <input type="time" id="shift-end" name="end_time" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="shift-tolerance" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Toleransi Keterlambatan (Menit) *</label>
                <input type="number" id="shift-tolerance" name="late_tolerance_minutes" value="15" min="0" max="120" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="shift-overnight" name="is_overnight" value="1" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <label for="shift-overnight" class="text-xs text-slate-700 font-medium">Shift Malam / Lintas Hari (Overnight)</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeShiftModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    Simpan Shift
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openShiftModal() {
        document.getElementById('shift-modal-title').textContent = 'Tambah Shift Baru';
        document.getElementById('shift-form').action = "{{ route('shifts.store') }}";
        document.getElementById('method-container').innerHTML = '';
        document.getElementById('shift-code').value = '';
        document.getElementById('shift-name').value = '';
        document.getElementById('shift-start').value = '08:00';
        document.getElementById('shift-end').value = '17:00';
        document.getElementById('shift-tolerance').value = '15';
        document.getElementById('shift-overnight').checked = false;
        document.getElementById('shift-modal').classList.remove('hidden');
    }

    function editShift(shift) {
        document.getElementById('shift-modal-title').textContent = 'Edit Shift: ' + shift.name;
        document.getElementById('shift-form').action = "/attendance/shifts/" + shift.id;
        document.getElementById('method-container').innerHTML = '@method("PUT")';
        document.getElementById('shift-code').value = shift.code;
        document.getElementById('shift-name').value = shift.name;
        document.getElementById('shift-start').value = shift.start_time.substring(0, 5);
        document.getElementById('shift-end').value = shift.end_time.substring(0, 5);
        document.getElementById('shift-tolerance').value = shift.late_tolerance_minutes;
        document.getElementById('shift-overnight').checked = shift.is_overnight;
        document.getElementById('shift-modal').classList.remove('hidden');
    }

    function closeShiftModal() {
        document.getElementById('shift-modal').classList.add('hidden');
    }
</script>
@endsection
