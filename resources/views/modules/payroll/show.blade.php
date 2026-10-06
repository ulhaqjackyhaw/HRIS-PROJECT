@extends('layouts.app')

@section('header', 'Matriks Penggajian Periode')

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('payroll.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 rounded-xl transition-colors border border-slate-200">
            &larr; Kembali
        </a>

        <!-- Trigger Hitung / Refresh Payroll -->
        <form method="POST" action="{{ route('payroll.periods.generate', $period) }}" onsubmit="return confirm('Jalankan kalkulasi payroll otomatis untuk semua karyawan aktif di periode ini? Sistem akan menyerap data absensi, lembur, dan klaim terbaru.');">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-xs cursor-pointer">
                <span>⚡ Hitung / Refresh Payroll</span>
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Banner Periode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                Siklus Payroll Terverifikasi
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $period->name }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Rentang Cut-Off: <strong class="text-slate-700 font-mono">{{ $period->start_date->format('d M Y') }}</strong> s/d <strong class="text-slate-700 font-mono">{{ $period->end_date->format('d M Y') }}</strong> &bull; Tanggal Transfer: <strong class="text-slate-700 font-mono">{{ $period->payout_date->format('d M Y') }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-3">
            @php
                $statusBadge = match($period->status) {
                    'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'PAID' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'PROCESSING' => 'bg-amber-50 text-amber-700 border-amber-200',
                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadge }}">
                Status: {{ $period->status }}
            </span>
        </div>
    </div>

    @if ($employeesWithoutSalary > 0)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="text-xs">
                <strong>Perhatian:</strong> Terdapat {{ $employeesWithoutSalary }} karyawan aktif yang belum memiliki konfigurasi Struktur Gaji. Karyawan tersebut dilewati saat proses kalkulasi.
            </div>
        </div>
    @endif

    <!-- Kartu Ringkasan Finansial Periode Ini -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Total Bruto (Gross) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Pendapatan Bruto</span>
                <span class="text-2xl font-extrabold text-slate-800 mt-1 block font-mono">
                    Rp {{ number_format($period->total_gross_amount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-500">Gaji pokok + tunjangan + lembur</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg">
                💵
            </div>
        </div>

        <!-- Total Potongan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider block">Total Potongan (Deductions)</span>
                <span class="text-2xl font-extrabold text-rose-600 mt-1 block font-mono">
                    -Rp {{ number_format($period->total_deduction_amount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-rose-600 font-medium">PPh 21 TER + BPJS + Denda Telat/Alpa</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg">
                📉
            </div>
        </div>

        <!-- Total Bersih (THP) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Total Take Home Pay (THP)</span>
                <span class="text-2xl font-extrabold text-emerald-600 mt-1 block font-mono">
                    Rp {{ number_format($period->total_net_amount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-emerald-600 font-medium">Jumlah dana yang ditransfer ke rekening</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                🏦
            </div>
        </div>
    </div>

    <!-- Tabel Lembar Slip Gaji Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">
                    Daftar Slip Gaji Pegawai ({{ $period->payslips->count() }} Terkalkulasi)
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian pendapatan kotor, potongan terperinci, dan take home pay siap cair</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan & Posisi</th>
                        <th class="py-3 px-5 text-center">Rekap Kehadiran</th>
                        <th class="py-3 px-5 text-right">Gaji Bruto</th>
                        <th class="py-3 px-5 text-right">Total Potongan</th>
                        <th class="py-3 px-5 text-right">Reimbursement</th>
                        <th class="py-3 px-5 text-right font-bold text-emerald-700">Gaji Bersih (THP)</th>
                        <th class="py-3 px-5 text-right">Dokumen Slip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($period->payslips as $slip)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $slip->employee->full_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    NIK: <span class="font-mono text-slate-600">{{ $slip->employee->nik }}</span> &bull; {{ $slip->employee->department?->name ?? '-' }} ({{ $slip->employee->position?->title ?? '-' }})
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-center text-xs">
                                <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                                        {{ $slip->present_days }}H Masuk
                                    </span>
                                    @if ($slip->overtime_hours > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold border border-purple-200">
                                            {{ $slip->overtime_hours }}J Lembur
                                        </span>
                                    @endif
                                    @if ($slip->late_minutes > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold border border-amber-200">
                                            {{ $slip->late_minutes }}M Telat
                                        </span>
                                    @endif
                                    @if ($slip->unpaid_leave_days > 0 || $slip->absent_days > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-semibold border border-rose-200">
                                            {{ $slip->unpaid_leave_days + $slip->absent_days }}H Unpaid
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-xs text-slate-800">
                                Rp {{ number_format($slip->gross_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-xs text-rose-600">
                                -Rp {{ number_format($slip->total_deductions, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-xs text-indigo-600">
                                @if ($slip->total_reimbursements > 0)
                                    +Rp {{ number_format($slip->total_reimbursements, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono text-sm font-extrabold text-emerald-700">
                                Rp {{ number_format($slip->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('payroll.payslips.show', $slip) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Lihat Slip</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                <p>Belum ada data slip gaji pada periode ini.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan klik tombol <strong>"⚡ Hitung / Refresh Payroll"</strong> di atas untuk menjalankan kalkulasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
