@extends('layouts.app')

@section('header', 'Pengajuan Cuti & Izin')

@section('actions')
    <button type="button" 
            onclick="openLeaveModal()" 
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-500 rounded-xl transition-colors shadow-xs cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Ajukan Cuti / Izin</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Pengajuan</span>
                <span class="text-2xl font-extrabold text-slate-800 mt-1 block">{{ $kpi['total'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                📋
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Menunggu Review</span>
                <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $kpi['pending'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Disetujui</span>
                <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $kpi['approved'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Ditolak</span>
                <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ $kpi['rejected'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                ✕
            </div>
        </div>
    </div>

    <!-- Alert Otomasi Status Kalender Presensi -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs space-y-1">
            <strong class="font-bold block">Sinkronisasi Otomatis ke Kalender Kehadiran:</strong>
            <p class="text-amber-800 leading-relaxed">
                Saat atasan/HR menyetujui (<span class="font-semibold text-emerald-700">Approve</span>) permohonan cuti, sistem secara otomatis mengisi log presensi pada seluruh rentang hari kerja tersebut dengan status <code class="bg-amber-100 text-amber-800 px-1 py-0.5 rounded font-mono font-bold">LEAVE</code>. Karyawan tidak akan terhitung alpa/mangkir pada kalkulasi penggajian.
            </p>
        </div>
    </div>

    <!-- Tabel Daftar Pengajuan Cuti -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Riwayat Permohonan Cuti & Izin</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar permohonan cuti, dispensasi, izin sakit, dan verifikasi atasan</p>
            </div>

            <!-- Filter Status Tab -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-semibold text-slate-600">
                <a href="{{ route('leaves.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">
                    Semua
                </a>
                <a href="{{ route('leaves.index', ['status' => 'PENDING']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'PENDING' ? 'bg-white text-amber-600 shadow-xs' : 'hover:text-slate-900' }}">
                    Pending
                </a>
                <a href="{{ route('leaves.index', ['status' => 'APPROVED']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'APPROVED' ? 'bg-white text-emerald-600 shadow-xs' : 'hover:text-slate-900' }}">
                    Disetujui
                </a>
                <a href="{{ route('leaves.index', ['status' => 'REJECTED']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'REJECTED' ? 'bg-white text-rose-600 shadow-xs' : 'hover:text-slate-900' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5">Tipe Cuti</th>
                        <th class="py-3 px-5">Rentang Tanggal</th>
                        <th class="py-3 px-5 text-center">Durasi</th>
                        <th class="py-3 px-5">Alasan / Keterangan</th>
                        <th class="py-3 px-5 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($leaves as $leave)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $leave->employee->full_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    NIK: <span class="font-mono text-slate-600">{{ $leave->employee->nik }}</span> &bull; {{ $leave->employee->department?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                @php
                                    $isPaid = $leave->leaveType ? $leave->leaveType->is_paid : ($leave->type !== 'UNPAID');
                                    $deductsQuota = $leave->leaveType ? $leave->leaveType->deducts_annual_quota : ($leave->type === 'ANNUAL');
                                    $typeTitle = $leave->leaveType?->name ?? match($leave->type) {
                                        'ANNUAL' => 'Cuti Tahunan',
                                        'SICK', 'SICK_CERT' => 'Izin Sakit',
                                        'SPECIAL' => 'Cuti Khusus',
                                        'MATERNITY' => 'Cuti Melahirkan',
                                        'MARRIAGE' => 'Izin Menikah',
                                        'PATERNITY' => 'Cuti Suami',
                                        'BEREAVEMENT' => 'Izin Duka Cita',
                                        'HAJJ' => 'Ibadah Haji',
                                        'UNPAID' => 'Tanpa Gaji (Unpaid)',
                                        default => $leave->type,
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $isPaid ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $typeTitle }}
                                </span>
                                @if ($deductsQuota)
                                    <span class="block text-[10px] text-amber-600 font-semibold mt-1">
                                        ⚡ Potong Kuota Cuti
                                    </span>
                                @endif
                                @if (! $isPaid)
                                    <span class="block text-[10px] text-rose-600 font-semibold mt-1">
                                        ⚠️ Unpaid (Potong Upah)
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
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold font-mono">
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
                                @if ($leave->rejection_note)
                                    <p class="text-[11px] text-rose-600 mt-1 font-medium">
                                        Catatan Tolak: {{ $leave->rejection_note }}
                                    </p>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if ($leave->status === 'PENDING')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu
                                    </span>
                                @elseif ($leave->status === 'APPROVED')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if ($leave->status === 'PENDING')
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('leaves.approve', $leave) }}" onsubmit="return confirm('Setujui pengajuan cuti ini? Sistem akan otomatis mencatat status LEAVE di kalender presensi dan memotong saldo kuota cuti tahunan jika berlaku.');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg transition-colors cursor-pointer shadow-2xs">
                                                Setujui
                                            </button>
                                        </form>

                                        <!-- Reject Button -->
                                        <button type="button" onclick="openRejectModal({{ $leave->id }})" class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors cursor-pointer">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400">
                                        {{ $leave->approver ? $leave->approver->full_name : 'Sistem' }}
                                        <div class="text-[10px] text-slate-400">{{ $leave->action_at?->format('d/m/Y') }}</div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-sm">
                                Tidak ada data permohonan cuti yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaves->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Form Pengajuan Cuti -->
<div id="modal-leave" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl p-6 space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Form Pengajuan Cuti / Izin Karyawan</h3>
                <p class="text-xs text-slate-400">Permohonan izin kerja sesuai regulasi UU Ketenagakerjaan</p>
            </div>
            <button type="button" onclick="closeLeaveModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Karyawan -->
            <div>
                <label for="employee_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Karyawan Pemohon <span class="text-rose-500">*</span>
                </label>
                <select id="employee_id" name="employee_id" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" data-quota="{{ $emp->remaining_annual_leave }}" {{ ($currentUserEmployee && $currentUserEmployee->id === $emp->id) ? 'selected' : '' }}>
                            {{ $emp->full_name }} (NIK: {{ $emp->nik }} &bull; Sisa Cuti: {{ $emp->remaining_annual_leave }} hari)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipe Cuti -->
            <div>
                <label for="leave_type_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jenis Izin / Cuti <span class="text-rose-500">*</span>
                </label>
                <select id="leave_type_id" name="leave_type_id" required onchange="handleLeaveTypeChange(this)" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach ($leaveTypes as $type)
                        <option value="{{ $type->id }}" 
                                data-deducts="{{ $type->deducts_annual_quota ? '1' : '0' }}" 
                                data-paid="{{ $type->is_paid ? '1' : '0' }}"
                                data-requires-attachment="{{ $type->requires_attachment ? '1' : '0' }}"
                                data-max-days="{{ $type->max_days ?? '' }}">
                            {{ $type->name }} 
                            @if ($type->deducts_annual_quota) (Potong Kuota Cuti) @endif
                            @if (! $type->is_paid) (Tanpa Upah / Unpaid) @endif
                            @if ($type->max_days) - Maks {{ $type->max_days }} hari @endif
                        </option>
                    @endforeach
                </select>

                <!-- Helper Banner Dinamis -->
                <div id="leave-type-hint" class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-start gap-2">
                    <span id="hint-icon">ℹ️</span>
                    <span id="hint-text">Pilih jenis cuti untuk melihat kebijakan pemotongan kuota dan upah.</span>
                </div>
            </div>

            <!-- Rentang Tanggal -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mulai Tanggal <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Sampai Tanggal <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Upload Dokumen Bukti (Surat Dokter / Undangan) -->
            <div id="attachment-field-container">
                <label for="attachment" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Dokumen Lampiran Pendukung <span id="attachment-required-badge" class="hidden text-rose-500 font-bold">* (Wajib)</span>
                </label>
                <input type="file" 
                       id="attachment" 
                       name="attachment" 
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Format PDF, JPG, PNG (Maksimal 5MB). Wajib untuk izin sakit dokter & perizinan khusus.</p>
            </div>

            <!-- Alasan -->
            <div>
                <label for="reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alasan / Keterangan Keperluan <span class="text-rose-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="3" required placeholder="Tuliskan alasan permohonan cuti secara jelas..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 rounded-xl transition-colors shadow-xs cursor-pointer">
                    Kirim Permohonan Izin
                </button>
            </div>
        </form>
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
                <textarea id="rejection_note" name="rejection_note" rows="3" required placeholder="Contoh: Jadwal project rilis mendekati deadline..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
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
    function openLeaveModal() {
        document.getElementById('modal-leave').classList.remove('hidden');
        const select = document.getElementById('leave_type_id');
        if (select) {
            handleLeaveTypeChange(select);
        }
    }
    function closeLeaveModal() {
        document.getElementById('modal-leave').classList.add('hidden');
    }
    function openRejectModal(leaveId) {
        const form = document.getElementById('form-reject');
        form.action = `/attendance/leaves/${leaveId}/reject`;
        document.getElementById('modal-reject').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('modal-reject').classList.add('hidden');
    }

    function handleLeaveTypeChange(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        if (!selected) return;

        const deducts = selected.getAttribute('data-deducts') === '1';
        const isPaid = selected.getAttribute('data-paid') === '1';
        const requiresAttachment = selected.getAttribute('data-requires-attachment') === '1';
        const maxDays = selected.getAttribute('data-max-days');

        const badge = document.getElementById('attachment-required-badge');
        const fileInput = document.getElementById('attachment');
        const hintText = document.getElementById('hint-text');
        const hintIcon = document.getElementById('hint-icon');

        if (requiresAttachment) {
            badge?.classList.remove('hidden');
            fileInput?.setAttribute('required', 'required');
        } else {
            badge?.classList.add('hidden');
            fileInput?.removeAttribute('required');
        }

        let notes = [];
        if (deducts) {
            notes.push('⚠️ Mengurangi sisa kuota cuti tahunan berjalan');
        } else {
            notes.push('✓ Tidak mengurangi kuota cuti tahunan');
        }

        if (isPaid) {
            notes.push('Gaji tetap dibayar penuh (Paid Leave)');
        } else {
            notes.push('⛔ Pemotongan upah harian pada Payroll (Unpaid Leave)');
        }

        if (maxDays) {
            notes.push(`Maksimal ${maxDays} hari per pengajuan`);
        }

        if (requiresAttachment) {
            notes.push('Wajib melampirkan file dokumen/surat bukti');
        }

        if (hintText) {
            hintText.innerText = notes.join(' • ');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('leave_type_id');
        if (select) {
            handleLeaveTypeChange(select);
        }
    });
</script>
@endsection
