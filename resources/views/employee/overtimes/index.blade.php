@extends('employee.layouts.app', ['title' => 'Pengajuan Lembur'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Lembur</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>⚡ Pengajuan Lembur Mandiri</span>
            </h1>
        </div>

        <button type="button" 
                onclick="document.getElementById('modal-overtime-request').classList.remove('hidden')" 
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
                style="min-height: 44px; touch-action: manipulation;">
            <span>+</span>
            <span>Ajukan Lembur Baru</span>
        </button>
    </div>

    <!-- Ringkasan Jam Lembur -->
    @php
        $totalApprovedHours = $overtimes->where('status', 'APPROVED')->sum('total_hours');
        $pendingOvertimes = $overtimes->where('status', 'PENDING')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Jam Lembur Disetujui -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-black text-xl shrink-0">
                ⚡
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Jam Lembur</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalApprovedHours, 1) }} Jam</span>
                <span class="text-[11px] text-slate-500 font-medium block">Telah diverifikasi & disetujui</span>
            </div>
        </div>

        <!-- Pengajuan Menunggu -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-black text-xl shrink-0">
                ⏳
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Menunggu Persetujuan</span>
                <span class="text-2xl font-black text-slate-900">{{ $pendingOvertimes }}</span>
                <span class="text-[11px] text-blue-600 font-medium block">Dalam evaluasi supervisor</span>
            </div>
        </div>

        <!-- Total Seluruh Pengajuan -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-black text-xl shrink-0">
                📋
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Pengajuan</span>
                <span class="text-2xl font-black text-slate-900">{{ $overtimes->total() }}</span>
                <span class="text-[11px] text-emerald-600 font-medium block">Riwayat lembur diajukan</span>
            </div>
        </div>
    </div>

    <!-- Riwayat Pengajuan Lembur -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-900 text-base">Riwayat Surat Perintah Kerja Lembur</h2>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $overtimes->total() }} Data</span>
        </div>

        @if($overtimes->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3">
                    ⚡
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Pengajuan Lembur</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Anda belum memiliki catatan pengajuan lembur. Klik tombol di bawah jika Anda bekerja di luar jam reguler shift.
                </p>
                <button type="button" 
                        onclick="document.getElementById('modal-overtime-request').classList.remove('hidden')" 
                        class="mt-4 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    + Ajukan Lembur Sekarang
                </button>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Tanggal Lembur</th>
                            <th class="py-3 px-4">Rentang Waktu</th>
                            <th class="py-3 px-4">Durasi Jam</th>
                            <th class="py-3 px-4">Uraian Tugas Lembur</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Tanggal Input</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($overtimes as $ot)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ \Carbon\Carbon::parse($ot->date)->isoFormat('dddd, D MMM Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium">
                                    {{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-amber-700">
                                    {{ $ot->total_hours }} Jam
                                </td>
                                <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                    {{ $ot->reason }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($ot->status === 'APPROVED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($ot->status === 'REJECTED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Approval
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                    {{ $ot->created_at->isoFormat('D MMM Y HH:mm') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-slate-100">
                @foreach($overtimes as $ot)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 text-xs">
                                {{ \Carbon\Carbon::parse($ot->date)->isoFormat('D MMM Y') }}
                            </span>
                            @if($ot->status === 'APPROVED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Disetujui
                                </span>
                            @elseif($ot->status === 'REJECTED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl">
                            <span class="font-mono">
                                {{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}
                            </span>
                            <span class="font-bold text-amber-700">{{ $ot->total_hours }} Jam</span>
                        </div>
                        <p class="text-xs text-slate-500 italic">
                            "{{ $ot->reason }}"
                        </p>
                    </div>
                @endforeach
            </div>

            @if($overtimes->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $overtimes->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- Modal Form Pengajuan Lembur Baru -->
<div id="modal-overtime-request" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <span>⚡ Form Pengajuan Lembur</span>
            </h3>
            <button type="button" 
                    onclick="document.getElementById('modal-overtime-request').classList.add('hidden')" 
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('employee.overtimes.store') }}" method="POST" class="p-5 space-y-4">
            @csrf

            <!-- Tanggal Lembur -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Lembur <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <!-- Jam Mulai & Jam Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Mulai Pukul (HH:mm) <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" value="18:00" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Selesai Pukul (HH:mm) <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" value="21:00" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Alasan / Uraian Tugas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tugas / Proyek yang Dikerjakan <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required placeholder="Jelaskan rincian pekerjaan atau task lembur yang diselesaikan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" 
                        onclick="document.getElementById('modal-overtime-request').classList.add('hidden')" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
                        style="min-height: 44px; touch-action: manipulation;">
                    Kirim Permohonan Lembur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
