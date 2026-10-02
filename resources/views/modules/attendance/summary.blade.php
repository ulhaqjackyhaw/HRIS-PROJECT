@extends('layouts.app')

@section('header', 'Rekapitulasi Presensi & Lembur')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                HR & Payroll Engine Read-Model
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                Rekapitulasi Presensi & Lembur Bulanan
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Agregasi total kehadiran, cuti, keterlambatan, dan jam lembur terverifikasi per karyawan.
            </p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('attendance.summary') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label for="month" class="sr-only">Bulan</label>
                <input type="month" 
                       id="month" 
                       name="month" 
                       value="{{ $month }}" 
                       onchange="this.form.submit()" 
                       class="px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-xs">
            </div>

            <div>
                <label for="department_id" class="sr-only">Departemen</label>
                <select id="department_id" 
                        name="department_id" 
                        onchange="this.form.submit()" 
                        class="px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-xs">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if (request()->hasAny(['department_id']))
                <a href="{{ route('attendance.summary', ['month' => $month]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-colors" title="Reset Filter">
                    &times; Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Metrik KPI Card Bulanan -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Karyawan -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Karyawan</span>
                <span class="text-2xl font-extrabold text-slate-800 mt-1 block">{{ $kpi['total_employees'] }}</span>
                <span class="text-[11px] text-slate-500">Total data tercakup</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                👥
            </div>
        </div>

        <!-- Total Hadir -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Hadir Tepat</span>
                <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $kpi['total_present'] }}</span>
                <span class="text-[11px] text-emerald-600 font-medium">Hari Kerja</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>

        <!-- Total Terlambat -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Terlambat</span>
                <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $kpi['total_late'] }}</span>
                <span class="text-[11px] text-amber-600 font-medium">Frekuensi telat</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                ⏱️
            </div>
        </div>

        <!-- Total Cuti / Izin -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Cuti & Izin</span>
                <span class="text-2xl font-extrabold text-blue-600 mt-1 block">{{ $kpi['total_leave'] }}</span>
                <span class="text-[11px] text-blue-600 font-medium">Hari Disetujui</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                🏖️
            </div>
        </div>

        <!-- Total Lembur -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between col-span-2 sm:col-span-2 lg:col-span-1">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Lembur (SPL)</span>
                <span class="text-2xl font-extrabold text-purple-600 mt-1 block">{{ number_format($kpi['total_overtime_hours'], 1) }}</span>
                <span class="text-[11px] text-purple-600 font-medium">Akumulasi Jam</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                ⚡
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                    Daftar Rekap Periode {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Data siap disinkronkan ke Modul Payroll & PPh 21/TER</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                {{ $employees->count() }} Pegawai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Karyawan</th>
                        <th class="py-3 px-5 text-center text-emerald-700">Hadir</th>
                        <th class="py-3 px-5 text-center text-amber-700">Telat</th>
                        <th class="py-3 px-5 text-center text-blue-700">Cuti/Izin</th>
                        <th class="py-3 px-5 text-center text-rose-700">Alpa</th>
                        <th class="py-3 px-5 text-center">Total Telat (Mnt)</th>
                        <th class="py-3 px-5 text-center text-purple-700">Lembur (Jam)</th>
                        <th class="py-3 px-5 text-center">Jam Kerja Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900 text-sm">{{ $emp['name'] }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    NIK: <span class="font-mono text-slate-600">{{ $emp['nik'] }}</span> &bull; {{ $emp['department'] }} ({{ $emp['position'] }})
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-emerald-600">
                                {{ $emp['present_count'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-amber-600">
                                {{ $emp['late_count'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-blue-600">
                                {{ $emp['leave_count'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold {{ $emp['absent_count'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ $emp['absent_count'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-mono text-xs {{ $emp['total_late_minutes'] > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">
                                {{ $emp['total_late_minutes'] }} mnt
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-purple-700">
                                {{ number_format($emp['total_overtime_hours'], 1) }} jam
                            </td>
                            <td class="py-3.5 px-5 text-center font-mono text-xs text-slate-700">
                                {{ number_format($emp['total_work_minutes'] / 60, 1) }} jam
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-sm">
                                Tidak ada data karyawan atau presensi untuk bulan yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
