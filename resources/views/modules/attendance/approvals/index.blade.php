@extends('layouts.app')

@section('header', 'Persetujuan Manajer (MSS Approvals)')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Manager Self-Service (MSS)
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                Pusat Persetujuan Cuti & Lembur Tim
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Tinjau dan verifikasi permohonan ketidakhadiran dan surat perintah lembur bawahan langsung.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200">
                Total Menunggu: <strong class="text-amber-600 font-mono">{{ $pendingLeaves->count() + $pendingOvertimes->count() }}</strong>
            </span>
        </div>
    </div>

    <!-- Alert Otomasi Status Kalender Kehadiran -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs space-y-1">
            <strong class="font-bold block">Sinkronisasi Otomatis ke Presensi & Payroll:</strong>
            <p class="text-amber-800 leading-relaxed">
                Saat atasan menyetujui (<span class="font-semibold text-emerald-700">Approve</span>), sistem otomatis:
                <br>1. Mengisi baris tabel presensi (<code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono font-bold">attendances</code>) dengan status <code class="bg-amber-100 text-amber-900 px-1 py-0.5 rounded font-mono font-bold">LEAVE</code> pada seluruh hari kerja dalam rentang cuti.
                <br>2. Mengurangi sisa kuota cuti tahunan karyawan secara otomatis jika jenis cuti memotong kuota.
            </p>
        </div>
    </div>

    <!-- Section 1: Pending Permohonan Cuti -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h3 class="font-bold text-slate-900 text-base">Permohonan Cuti & Izin Menunggu Persetujuan</h3>
            </div>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
                {{ $pendingLeaves->count() }} Pengajuan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan Pemohon</th>
                        <th class="py-3 px-5">Jenis Izin</th>
                        <th class="py-3 px-5">Rentang Tanggal</th>
                        <th class="py-3 px-5 text-center">Durasi Kerja</th>
                        <th class="py-3 px-5">Alasan / Bukti</th>
                        <th class="py-3 px-5 text-right">Keputusan Atasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pendingLeaves as $leave)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $leave->employee->full_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    NIK: <span class="font-mono text-slate-600">{{ $leave->employee->nik }}</span> &bull; {{ $leave->employee->department?->name ?? '-' }}
                                </div>
                                <div class="text-[11px] text-indigo-600 mt-0.5 font-medium">
                                    Sisa Cuti: {{ $leave->employee->remaining_annual_leave }} hari
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ ($leave->leaveType && !$leave->leaveType->is_paid) ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                    {{ $leave->leaveType?->name ?? $leave->type }}
                                </span>
                                @if ($leave->leaveType && $leave->leaveType->deducts_annual_quota)
                                    <span class="block text-[10px] text-amber-600 font-semibold mt-1">
                                        ⚡ Memotong Kuota Tahunan
                                    </span>
                                @endif
                                @if ($leave->leaveType && !$leave->leaveType->is_paid)
                                    <span class="block text-[10px] text-rose-600 font-semibold mt-1">
                                        ⚠️ Unpaid (Potong Gaji)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-medium text-slate-900 text-xs">
                                    {{ $leave->start_date->format('d M Y') }} s.d {{ $leave->end_date->format('d M Y') }}
                                </div>
                                <span class="text-[10px] text-slate-400">Diajukan: {{ $leave->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                                    {{ $leave->total_days }} Hari Kerja
                                </span>
                            </td>
                            <td class="py-3.5 px-5 max-w-xs">
                                <p class="text-xs text-slate-700 line-clamp-2" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
                                @if ($leave->attachment_path)
                                    <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 font-medium mt-1 underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <span>Lihat Dokumen Bukti</span>
                                    </a>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Approve Form -->
                                    <form method="POST" action="{{ route('approvals.leave.approve', $leave) }}" onsubmit="return confirm('Setujui pengajuan cuti ini? Log presensi otomatis berubah menjadi LEAVE.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all shadow-xs cursor-pointer">
                                            Setujui (Approve)
                                        </button>
                                    </form>

                                    <!-- Reject Form Trigger -->
                                    <button type="button" onclick="openRejectModal({{ $leave->id }})" class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all cursor-pointer">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-sm">
                                Tidak ada pengajuan cuti yang memerlukan persetujuan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Pending Permohonan Lembur (SPL) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                <h3 class="font-bold text-slate-900 text-base">Permohonan Lembur (SPL) Menunggu Persetujuan</h3>
            </div>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800">
                {{ $pendingOvertimes->count() }} Pengajuan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Tanggal Lembur</th>
                        <th class="py-3 px-5 text-center">Durasi</th>
                        <th class="py-3 px-5">Uraian Tugas Lembur</th>
                        <th class="py-3 px-5 text-right">Keputusan Atasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pendingOvertimes as $ot)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $ot->employee->full_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    NIK: <span class="font-mono text-slate-600">{{ $ot->employee->nik }}</span> &bull; {{ $ot->employee->department?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-medium text-slate-900 text-xs">
                                    {{ $ot->date->format('d M Y') }}
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono">{{ substr($ot->start_time, 0, 5) }} - {{ substr($ot->end_time, 0, 5) }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold font-mono border border-purple-200">
                                    {{ $ot->total_hours }} Jam
                                </span>
                            </td>
                            <td class="py-3.5 px-5 max-w-xs">
                                <p class="text-xs text-slate-700 line-clamp-2">{{ $ot->reason }}</p>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="{{ route('overtimes.approve', $ot) }}" onsubmit="return confirm('Setujui SPL lembur ini? Jam lembur akan masuk ke rekap payroll.');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-white bg-purple-600 hover:bg-purple-500 rounded-xl transition-all shadow-xs cursor-pointer">
                                            Setujui Lembur
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('overtimes.reject', $ot) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all cursor-pointer">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-sm">
                                Tidak ada pengajuan lembur yang memerlukan persetujuan saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Form Penolakan Cuti -->
<div id="modal-reject" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Alasan Penolakan Cuti</h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none">&times;</button>
        </div>

        <form id="form-reject" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_note" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan untuk Karyawan <span class="text-rose-500">*</span>
                </label>
                <textarea id="rejection_note" name="rejection_note" rows="3" required placeholder="Contoh: Jadwal operasional sedang padat atau kebutuhan tim mendesak..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-colors shadow-xs">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(leaveId) {
        const form = document.getElementById('form-reject');
        form.action = `/attendance/approvals/leaves/${leaveId}/reject`;
        document.getElementById('modal-reject').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('modal-reject').classList.add('hidden');
    }
</script>
@endsection
