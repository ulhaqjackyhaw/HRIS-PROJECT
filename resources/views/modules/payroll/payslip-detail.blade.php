@extends('layouts.app')

@section('header', 'Slip Gaji Karyawan')

@section('actions')
    <div class="flex items-center gap-2 print:hidden">
        <a href="{{ route('payroll.periods.show', $payslip->payroll_period_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 rounded-xl transition-colors border border-slate-200">
            &larr; Kembali ke Matriks
        </a>
        <button type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold text-white bg-slate-900 hover:bg-black rounded-xl shadow-xs transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak / Unduh PDF</span>
        </button>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto py-2 print:max-w-none print:m-0 print:p-0">

    <!-- Lembar Slip Gaji Resmi -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 space-y-8 print:border-none print:shadow-none print:p-0 print:rounded-none">

        <!-- Header Perusahaan & Judul Dokumen -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-6 border-b-2 border-slate-900">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0">
                    HR
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">PT HRIS ENTERPRISE INDONESIA</h1>
                    <p class="text-xs text-slate-500 font-mono tracking-wider uppercase mt-0.5">SLIP GAJI RESMI (CONFIDENTIAL)</p>
                </div>
            </div>

            <div class="text-left sm:text-right text-xs">
                <span class="text-slate-400 block">Periode Penggajian:</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $payslip->payrollPeriod->name }}</span>
                <span class="text-slate-500 mt-0.5 block">Cut-Off: {{ $payslip->payrollPeriod->start_date->format('d/m/Y') }} - {{ $payslip->payrollPeriod->end_date->format('d/m/Y') }}</span>
                <span class="text-slate-500 block">Tgl Cair: {{ $payslip->payrollPeriod->payout_date->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Identitas & Profil Karyawan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 text-xs">
            <div class="space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Lengkap:</span>
                    <strong class="text-slate-900 font-semibold">{{ $payslip->employee->full_name }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nomor Induk Karyawan (NIK):</span>
                    <span class="font-mono text-slate-700 font-semibold">{{ $payslip->employee->nik }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Status Kepegawaian:</span>
                    <span class="text-slate-700 font-medium">{{ $payslip->employee->employment_status }}</span>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Departemen / Unit:</span>
                    <strong class="text-slate-900 font-semibold">{{ $payslip->employee->department?->name ?? '-' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jabatan:</span>
                    <span class="text-slate-700 font-semibold">{{ $payslip->employee->position?->title ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kategori PTKP Pajak:</span>
                    <span class="font-mono text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                        {{ $payslip->employee->ptkp_status ?? 'TK/0' }} (TER)
                    </span>
                </div>
            </div>
        </div>

        <!-- Rekapitulasi Presensi Periode Ini -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-slate-100/70 rounded-xl text-xs text-slate-600">
            <span class="font-bold text-slate-800">Catatan Waktu Kerja:</span>
            <div class="flex items-center gap-4 flex-wrap font-medium">
                <span>Hadir: <strong class="text-emerald-700 font-bold">{{ $payslip->present_days }}</strong> Hari</span>
                <span>Lembur SPL: <strong class="text-purple-700 font-bold">{{ $payslip->overtime_hours }}</strong> Jam</span>
                <span>Keterlambatan: <strong class="text-amber-700 font-bold">{{ $payslip->late_minutes }}</strong> Menit</span>
                <span>Alpa / Unpaid: <strong class="text-rose-700 font-bold">{{ $payslip->absent_days + $payslip->unpaid_leave_days }}</strong> Hari</span>
            </div>
        </div>

        <!-- Tabel Komponen Gaji: Penerimaan vs Potongan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">

            <!-- Kolom 1: Penerimaan (Earnings) -->
            <div class="space-y-4">
                <div class="pb-2 border-b-2 border-emerald-500 flex items-center justify-between">
                    <h3 class="font-bold text-emerald-800 text-xs sm:text-sm uppercase tracking-wider">PENERIMAAN (EARNINGS)</h3>
                    <span class="text-[11px] text-emerald-600 font-medium">Penghasilan Bruto</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    @php
                        $earnings = $payslip->items->where('type', 'EARNING');
                    @endphp
                    @foreach ($earnings as $item)
                        <div class="flex justify-between items-center py-1 border-b border-slate-100">
                            <span class="text-slate-600">{{ $item->name }}</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    @php
                        $reimbursements = $payslip->items->where('type', 'REIMBURSEMENT');
                    @endphp
                    @if ($reimbursements->isNotEmpty())
                        <div class="pt-2 text-[11px] font-bold text-indigo-700 uppercase tracking-wider">
                            Klaim Pengeluaran (Reimbursement)
                        </div>
                        @foreach ($reimbursements as $item)
                            <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                <span class="text-slate-600">{{ $item->name }}</span>
                                <span class="font-mono font-semibold text-indigo-700">+Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="pt-2 flex justify-between items-center font-bold text-xs bg-emerald-50/50 p-2.5 rounded-xl border border-emerald-100">
                    <span class="text-emerald-900">Total Penerimaan Kotor:</span>
                    <span class="font-mono text-emerald-800 text-sm">Rp {{ number_format($payslip->gross_salary + $payslip->total_reimbursements, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Kolom 2: Potongan (Deductions) -->
            <div class="space-y-4">
                <div class="pb-2 border-b-2 border-rose-500 flex items-center justify-between">
                    <h3 class="font-bold text-rose-800 text-xs sm:text-sm uppercase tracking-wider">POTONGAN (DEDUCTIONS)</h3>
                    <span class="text-[11px] text-rose-600 font-medium">Pajak, BPJS, & Denda</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    @php
                        $deductions = $payslip->items->where('type', 'DEDUCTION');
                    @endphp
                    @forelse ($deductions as $item)
                        <div class="flex justify-between items-center py-1 border-b border-slate-100">
                            <span class="text-slate-600">{{ $item->name }}</span>
                            <span class="font-mono font-semibold text-rose-600">-Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-slate-400 py-2 text-center">Tidak ada potongan pada periode ini.</div>
                    @endforelse
                </div>

                <div class="pt-2 flex justify-between items-center font-bold text-xs bg-rose-50/50 p-2.5 rounded-xl border border-rose-100">
                    <span class="text-rose-900">Total Potongan Resmi:</span>
                    <span class="font-mono text-rose-700 text-sm">-Rp {{ number_format($payslip->total_deductions, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        <!-- Take Home Pay (Gaji Bersih Ditransfer) -->
        <div class="pt-6 border-t-2 border-dashed border-slate-300">
            <div class="bg-indigo-900 text-white rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                <div>
                    <span class="text-xs text-indigo-300 uppercase tracking-widest font-semibold block">Gaji Bersih Ditransfer (Take Home Pay)</span>
                    <h2 class="text-3xl font-black text-white mt-1 font-mono tracking-tight">
                        Rp {{ number_format($payslip->net_salary, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="text-left sm:text-right text-xs text-indigo-200 border-t sm:border-t-0 sm:border-l border-indigo-800 pt-3 sm:pt-0 sm:pl-6">
                    <span class="text-indigo-400 block font-sans">Rekening Payroll Tujuan:</span>
                    <span class="font-bold text-white text-sm font-mono block mt-0.5">
                        {{ $payslip->employee->bank_name ?? 'Bank Mandiri' }} &bull; {{ $payslip->employee->bank_account_number ?? '1270009876543' }}
                    </span>
                    <span class="text-indigo-300 block text-[11px]">a.n. {{ $payslip->employee->bank_account_holder ?? $payslip->employee->full_name }}</span>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Resmi Pengesahan -->
        <div class="pt-10 grid grid-cols-2 gap-8 text-center text-xs text-slate-600">
            <div class="space-y-16">
                <p>Penerima,</p>
                <div>
                    <strong class="text-slate-900 underline block font-semibold">{{ $payslip->employee->full_name }}</strong>
                    <span class="text-slate-400">NIK: {{ $payslip->employee->nik }}</span>
                </div>
            </div>

            <div class="space-y-16">
                <p>Human Capital & Finance Dept,</p>
                <div>
                    <strong class="text-slate-900 underline block font-semibold">Bambang Pratama, S.Psi., M.M.</strong>
                    <span class="text-slate-400">Human Capital Manager</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center text-[10px] text-slate-400 font-mono">
            Dokumen ini dicetak secara otomatis melalui HRIS Enterprise Suite pada {{ now()->format('d/m/Y H:i:s') }} dan sah tanpa stempel basah.
        </div>

    </div>

</div>
@endsection
