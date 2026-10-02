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
                                    $typeBadge = match($leave->type) {
                                        'ANNUAL' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'SICK' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'SPECIAL' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'MATERNITY' => 'bg-pink-50 text-pink-700 border-pink-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    $typeLabel = match($leave->type) {
                                        'ANNUAL' => 'Cuti Tahunan',
                                        'SICK' => 'Izin Sakit',
                                        'SPECIAL' => 'Cuti Khusus',
                                        'MATERNITY' => 'Cuti Melahirkan',
                                        'UNPAID' => 'Tanpa Gaji',
                                        default => $leave->type,
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $typeBadge }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="font-medium text-slate-900 text-xs">
                                    {{ $leave->start_date->format('d M Y') }} s.d {{ $leave->end_date->format('d M Y') }}
                                </div>
                                <span class="text-[10px] text-slate-400">Diajukan: {{ $leave->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-bold font-mono">
                                    {{ $leave->total_days }} Hari
                                </span>
                            </td>
                            <td class="py-3.5 px-5 max-w-xs">
                                <p class="text-xs text-slate-700 line-clamp-2" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
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
                                        <form method="POST" action="{{ route('leaves.approve', $leave) }}" onsubmit="return confirm('Setujui pengajuan cuti ini? Sistem akan otomatis mencatat status LEAVE di tabel presensi.');">
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
                <h3 class="font-bold text-slate-900 text-base">Form Pengajuan Cuti / Izin</h3>
                <p class="text-xs text-slate-400">Masukkan detail permohonan cuti kerja karyawan</p>
            </div>
            <button type="button" onclick="closeLeaveModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('leaves.store') }}" class="space-y-4">
            @csrf

            <!-- Karyawan -->
            <div>
                <label for="employee_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Karyawan Pemohon <span class="text-rose-500">*</span>
                </label>
                <select id="employee_id" name="employee_id" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ ($currentUserEmployee && $currentUserEmployee->id === $emp->id) ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->nik }} - {{ $emp->department?->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipe Cuti -->
            <div>
                <label for="type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jenis Cuti / Izin <span class="text-rose-500">*</span>
                </label>
                <select id="type" name="type" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="ANNUAL">Cuti Tahunan (Annual Leave)</option>
                    <option value="SICK">Izin Sakit dengan Surat Dokter (Sick Leave)</option>
                    <option value="SPECIAL">Cuti Khusus (Menikah, Khitanan, Duka Cita)</option>
                    <option value="MATERNITY">Cuti Melahirkan (Maternity Leave)</option>
                    <option value="UNPAID">Izin Tanpa Gaji (Unpaid Leave)</option>
                    <option value="OTHER">Lainnya / Dispensasi</option>
                </select>
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

            <!-- Alasan -->
            <div>
                <label for="reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alasan / Keterangan Keperluan <span class="text-rose-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="3" required placeholder="Tuliskan alasan permohonan cuti secara jelas..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 rounded-xl transition-colors shadow-xs cursor-pointer">
                    Kirim Pengajuan Cuti
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
</script>
@endsection
