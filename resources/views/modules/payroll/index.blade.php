@extends('layouts.app')

@section('header', 'Modul Payroll & Kompensasi')

@section('actions')
    <button type="button" 
            onclick="openPeriodModal()" 
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors shadow-xs cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Buat Periode Payroll</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Periode -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Periode</span>
                <span class="text-2xl font-extrabold text-slate-800 mt-1 block">{{ $kpi['total_periods'] }}</span>
                <span class="text-[11px] text-slate-500">{{ $kpi['draft_periods'] }} Draft / Perlu Proses</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                📅
            </div>
        </div>

        <!-- Cakupan Karyawan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Struktur Gaji Siap</span>
                <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $kpi['configured_salaries'] }} / {{ $kpi['total_employees'] }}</span>
                <span class="text-[11px] text-emerald-600 font-medium">Pegawai Terkonfigurasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                👥
            </div>
        </div>

        <!-- Akumulasi Net Payout -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between col-span-2 sm:col-span-2">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Take Home Pay Tercairkan</span>
                <span class="text-2xl font-extrabold text-indigo-700 mt-1 block font-mono">
                    Rp {{ number_format($kpi['total_paid_net'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-500">Seluruh periode penggajian terverifikasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                💰
            </div>
        </div>
    </div>

    <!-- Banner Penjelasan Integrasi Otomatis -->
    <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200/80 text-indigo-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs space-y-1">
            <strong class="font-bold block">Payroll Engine Terintegrasi Penuh (Core HR & Attendance):</strong>
            <p class="text-indigo-800 leading-relaxed">
                Modul ini secara otomatis menyerap data <strong>kehadiran aktual</strong>, <strong>jam lembur disetujui atasan (SPL)</strong>, <strong>menit keterlambatan</strong>, serta <strong>potongan izin Unpaid Leave</strong>. 
                Perhitungan PPh 21 telah menggunakan skema <strong>Tarif Efektif Rata-Rata (TER) PP 58/2023</strong> dan pemotongan <strong>BPJS Ketenagakerjaan (3%)</strong> &amp; <strong>BPJS Kesehatan (1%)</strong>.
            </p>
        </div>
    </div>

    <!-- Tabel Daftar Periode Penggajian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Riwayat Periode Penggajian (Cut-Off Batas Waktu)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar siklus payroll bulanan, status pemrosesan, dan ringkasan nilai transfer</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full">
                {{ $periods->total() }} Periode
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Nama Periode</th>
                        <th class="py-3 px-5">Rentang Cut-Off</th>
                        <th class="py-3 px-5">Tanggal Cair</th>
                        <th class="py-3 px-5 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Total Bruto</th>
                        <th class="py-3 px-5 text-right">Total Potongan</th>
                        <th class="py-3 px-5 text-right font-bold">Total THP (Net)</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($periods as $period)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <a href="{{ route('payroll.periods.show', $period) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors text-sm">
                                    {{ $period->name }}
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $period->payslips_count }} Slip Gaji Karyawan
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-xs text-slate-700">
                                <div>{{ $period->start_date->format('d M Y') }} &rarr; {{ $period->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-5 text-xs font-medium text-slate-800">
                                {{ $period->payout_date->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @php
                                    $badge = match($period->status) {
                                        'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'PAID' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'PROCESSING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $period->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right text-xs font-mono text-slate-700">
                                Rp {{ number_format($period->total_gross_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right text-xs font-mono text-rose-600">
                                -Rp {{ number_format($period->total_deduction_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right text-xs font-mono font-bold text-emerald-700">
                                Rp {{ number_format($period->total_net_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition-colors">
                                    <span>Buka Matriks</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-sm">
                                Belum ada periode penggajian dibuat. Klik "Buat Periode Payroll" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($periods->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $periods->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Form Buat Periode Payroll -->
<div id="modal-period" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Buat Periode Penggajian Baru</h3>
                <p class="text-xs text-slate-400">Tentukan rentang cut-off absensi dan tanggal pencairan</p>
            </div>
            <button type="button" onclick="closePeriodModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('payroll.periods.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Periode <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" required placeholder="Misal: Payroll Oktober 2026" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Cut-Off Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Cut-Off Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="payout_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tanggal Pencairan (Transfer) <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="payout_date" name="payout_date" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePeriodModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors shadow-xs cursor-pointer">
                    Simpan Periode
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPeriodModal() {
        document.getElementById('modal-period').classList.remove('hidden');
    }
    function closePeriodModal() {
        document.getElementById('modal-period').classList.add('hidden');
    }
</script>
@endsection
