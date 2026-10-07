@extends('employee.layouts.app', ['title' => 'Cuti & Izin Kerja'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Cuti & Izin</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>🏖️ Pengajuan Cuti & Izin Pribadi</span>
            </h1>
        </div>

        <button type="button" 
                onclick="document.getElementById('modal-leave-request').classList.remove('hidden')" 
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
                style="min-height: 44px; touch-action: manipulation;">
            <span>+</span>
            <span>Ajukan Cuti Baru</span>
        </button>
    </div>

    <!-- Saldo & Ringkasan Kuota Cuti Karyawan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Sisa Cuti Tahunan -->
        <div class="p-5 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/15 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-100 block">Sisa Cuti Tahunan</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-3xl sm:text-4xl font-black">{{ $employee->remaining_annual_leave ?? 12 }}</span>
                <span class="text-sm font-semibold text-amber-100">Hari</span>
            </div>
            <p class="text-[11px] text-amber-100/90 mt-2 font-medium">Jatah cuti aktif hingga akhir tahun berjalan.</p>
        </div>

        <!-- Total Hari Cuti Terpakai -->
        @php
            $usedDays = $leaves->where('status', 'APPROVED')->sum('total_days');
            $pendingCount = $leaves->where('status', 'PENDING')->count();
        @endphp
        <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-black text-xl shrink-0">
                ✓
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Cuti Disetujui</span>
                <span class="text-2xl font-black text-slate-900">{{ $usedDays }} Hari</span>
                <span class="text-[11px] text-slate-500 font-medium block">Telah digunakan tahun ini</span>
            </div>
        </div>

        <!-- Pengajuan Menunggu Approval -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-black text-xl shrink-0">
                ⏳
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Menunggu Approval</span>
                <span class="text-2xl font-black text-slate-900">{{ $pendingCount }}</span>
                <span class="text-[11px] text-amber-700 font-medium block">Permohonan dalam proses</span>
            </div>
        </div>
    </div>

    <!-- Riwayat Permohonan Cuti -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-900 text-base">Riwayat Permohonan Cuti & Izin</h2>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $leaves->total() }} Pengajuan</span>
        </div>

        @if($leaves->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3">
                    🏖️
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Pengajuan Cuti</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Anda belum pernah mengajukan cuti atau izin kerja. Klik tombol di bawah untuk membuat pengajuan baru.
                </p>
                <button type="button" 
                        onclick="document.getElementById('modal-leave-request').classList.remove('hidden')" 
                        class="mt-4 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    + Ajukan Sekarang
                </button>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Tipe Cuti</th>
                            <th class="py-3 px-4">Tanggal Mulai - Selesai</th>
                            <th class="py-3 px-4">Durasi</th>
                            <th class="py-3 px-4">Alasan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Tanggal Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $leave->leaveType->name ?? 'Cuti Tahunan' }}
                                </td>
                                <td class="py-3.5 px-4 font-medium">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMM Y') }} &ndash; {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMM Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    {{ $leave->total_days }} Hari
                                </td>
                                <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                    {{ $leave->reason }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($leave->status === 'APPROVED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($leave->status === 'REJECTED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Atasan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                    {{ $leave->created_at->isoFormat('D MMM Y HH:mm') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-slate-100">
                @foreach($leaves as $leave)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 text-xs">
                                {{ $leave->leaveType->name ?? 'Cuti Tahunan' }}
                            </span>
                            @if($leave->status === 'APPROVED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Disetujui
                                </span>
                            @elseif($leave->status === 'REJECTED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600">
                            <strong>{{ $leave->total_days }} Hari:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMM Y') }}
                        </p>
                        <p class="text-xs text-slate-500 italic bg-slate-50 p-2.5 rounded-xl">
                            "{{ $leave->reason }}"
                        </p>
                    </div>
                @endforeach
            </div>

            @if($leaves->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $leaves->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

<!-- Modal Form Pengajuan Cuti Baru -->
<div id="modal-leave-request" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <span>📝 Form Pengajuan Cuti</span>
            </h3>
            <button type="button" 
                    onclick="document.getElementById('modal-leave-request').classList.add('hidden')" 
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('employee.leaves.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            
            <!-- Jenis Cuti -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Cuti / Izin <span class="text-rose-500">*</span></label>
                <select name="leave_type_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">-- Pilih Jenis Cuti --</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }} {{ $type->deducts_annual_leave ? '(Mengurangi Cuti Tahunan)' : '(Tanpa Potong Jatah)' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Rentang Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Mulai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" min="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Sampai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" min="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Alasan Cuti -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Cuti / Keterangan <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required placeholder="Jelaskan keperluan cuti atau izin kerja Anda..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" 
                        onclick="document.getElementById('modal-leave-request').classList.add('hidden')" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-extrabold shadow-md shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
                        style="min-height: 44px; touch-action: manipulation;">
                    Kirim Permohonan Cuti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
