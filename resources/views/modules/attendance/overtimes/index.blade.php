@extends('layouts.app')

@section('header', 'Pengajuan & Persetujuan Lembur (Overtime)')

@section('actions')
    <button type="button" onclick="openOvertimeModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Ajukan Surat Lembur (SPL)</span>
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

    <!-- Tabel Pengajuan Lembur -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Pengajuan Surat Perintah Lembur (SPL)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Alur persetujuan jam kerja ekstra sebelum disinkronkan ke engine penggajian</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('overtimes.index') }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ route('overtimes.index', ['status' => 'PENDING']) }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ request('status') === 'PENDING' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600' }}">Pending</a>
                <a href="{{ route('overtimes.index', ['status' => 'APPROVED']) }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ request('status') === 'APPROVED' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">Disetujui</a>
                <a href="{{ route('overtimes.index', ['status' => 'REJECTED']) }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ request('status') === 'REJECTED' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600' }}">Ditolak</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Tgl Lembur</th>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Rentang Jam Lembur</th>
                        <th class="py-3 px-5">Total Jam</th>
                        <th class="py-3 px-5">Alasan / Tugas Lembur</th>
                        <th class="py-3 px-5">Status & Verifikator</th>
                        <th class="py-3 px-5 text-right">Aksi Approval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($overtimes as $ot)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                {{ $ot->date->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-5">
                                <a href="{{ route('employees.show', $ot->employee) }}" class="font-semibold text-slate-900 hover:text-indigo-600 hover:underline">
                                    {{ $ot->employee?->full_name ?? '-' }}
                                </a>
                                <div class="text-xs text-slate-400 font-mono">{{ $ot->employee?->nik ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-5 font-mono text-xs">
                                <span class="text-indigo-600 font-bold">{{ $ot->start_time->format('H:i') }}</span>
                                <span class="text-slate-400 mx-1">&rarr;</span>
                                <span class="text-slate-700 font-bold">{{ $ot->end_time->format('H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="font-extrabold text-slate-900 text-sm">
                                    {{ $ot->total_hours }} Jam
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700 max-w-xs">
                                {{ $ot->reason }}
                            </td>
                            <td class="py-3.5 px-5">
                                @php
                                    $badge = match($ot->status) {
                                        'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $ot->status }}
                                </span>
                                @if ($ot->approver)
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Oleh: {{ $ot->approver->full_name }}
                                    </div>
                                @endif
                                @if ($ot->rejection_note)
                                    <div class="text-[10px] text-rose-600 mt-0.5 italic">
                                        Catatan: {{ $ot->rejection_note }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
                                @if ($ot->status === 'PENDING')
                                    <form action="{{ route('overtimes.approve', $ot) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs">
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button" 
                                            onclick="openRejectModal({{ $ot->id }}, '{{ $ot->employee?->full_name }}')"
                                            class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg">
                                        Tolak
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">
                                Belum ada pengajuan lembur yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($overtimes->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $overtimes->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Ajukan Lembur -->
<div id="overtime-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 class="font-bold text-slate-900 text-base">Formulir Pengajuan Lembur (SPL)</h4>
            <button type="button" onclick="closeOvertimeModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">
                &times;
            </button>
        </div>

        <form action="{{ route('overtimes.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="ot-employee" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Karyawan Yang Lembur *</label>
                <select id="ot-employee" name="employee_id" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Pilih Karyawan —</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->full_name }} ({{ $emp->nik }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ot-date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Lembur *</label>
                <input type="date" id="ot-date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="ot-start" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Mulai *</label>
                    <input type="time" id="ot-start" name="start_time" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="ot-end" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jam Selesai *</label>
                    <input type="time" id="ot-end" name="end_time" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="ot-reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan / Pekerjaan Lembur *</label>
                <textarea id="ot-reason" name="reason" rows="3" required placeholder="Contoh: Deployment server aplikasi ke production & testing malam..." class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeOvertimeModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Lembur -->
<div id="reject-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl p-6 space-y-4">
        <h4 class="font-bold text-slate-900 text-base">Tolak Pengajuan Lembur</h4>
        <p id="reject-modal-desc" class="text-xs text-slate-500"></p>

        <form id="reject-form" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reject-note" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alasan Penolakan *</label>
                <textarea id="reject-note" name="rejection_note" rows="3" required placeholder="Contoh: Tidak sesuai kuota lembur bulanan / tugas dapat dikerjakan besok..." class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openOvertimeModal() {
        document.getElementById('overtime-modal').classList.remove('hidden');
    }

    function closeOvertimeModal() {
        document.getElementById('overtime-modal').classList.add('hidden');
    }

    function openRejectModal(id, name) {
        document.getElementById('reject-form').action = "/attendance/overtimes/" + id + "/reject";
        document.getElementById('reject-modal-desc').textContent = 'Masukkan alasan penolakan untuk pengajuan lembur ' + name + ':';
        document.getElementById('reject-modal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
</script>
@endsection
