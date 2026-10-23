@extends('employee.layouts.app', ['title' => 'Riwayat Presensi'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Riwayat Presensi</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>📅 Rekap Kehadiran Pribadi</span>
            </h1>
        </div>

        <!-- Filter Bulan -->
        <form method="GET" action="{{ route('employee.attendance.history') }}" class="flex items-center gap-2">
            <input type="month" 
                   name="month" 
                   value="{{ $month }}" 
                   class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-xs focus:ring-2 focus:ring-amber-500">
            <button type="submit" 
                    class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm shadow-amber-500/20 cursor-pointer">
                Filter
            </button>
        </form>
    </div>

    <!-- 3 KPI Cards Ringkasan Bulan Ini -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Hadir -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-black text-xl shrink-0">
                ✓
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Hadir</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['total_present'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-500 font-medium block">Hari kerja tercatat</span>
            </div>
        </div>

        <!-- Tepat Waktu -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-black text-xl shrink-0">
                ⏱️
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tepat Waktu</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['on_time'] ?? 0 }}</span>
                <span class="text-[11px] text-blue-600 font-medium block">On-Time Clock In</span>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-black text-xl shrink-0">
                ⚠️
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Terlambat</span>
                <span class="text-2xl font-black text-slate-900">{{ $stats['late'] ?? 0 }}</span>
                <span class="text-[11px] text-amber-600 font-medium block">Tercatat terlambat</span>
            </div>
        </div>
    </div>

    <!-- Tabel / List Riwayat Presensi -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-extrabold text-slate-900 text-base">Detail Kehadiran Harian</h2>
            <span class="text-xs font-semibold text-slate-500">
                Periode: {{ \Carbon\Carbon::parse($month.'-01')->isoFormat('MMMM Y') }}
            </span>
        </div>

        @if($attendances->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-3">
                    📋
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Catatan Presensi</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tidak ditemukan data kehadiran untuk periode ini. Mulai lakukan presensi harian Anda melalui menu Presensi Selfie.
                </p>
                <a href="{{ route('employee.attendance.check-in') }}" 
                   class="inline-block mt-4 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs">
                    Presensi Hari Ini
                </a>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Shift</th>
                            <th class="py-3 px-4">Clock In</th>
                            <th class="py-3 px-4">Clock Out</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($attendances as $att)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::parse($att->date)->isoFormat('dddd, D MMMM Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    {{ $att->shift->name ?? 'Reguler Office' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">
                                    {{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-rose-600">
                                    {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($att->status === 'ON_TIME')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Tepat Waktu
                                        </span>
                                    @elseif($att->status === 'LATE')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            {{ $att->status ?? 'Alpha' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-600">
                                    {{ $att->late_minutes ? $att->late_minutes . ' menit' : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-slate-100">
                @foreach($attendances as $att)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-xs">
                                {{ \Carbon\Carbon::parse($att->date)->isoFormat('D MMM Y') }}
                            </span>
                            @if($att->status === 'ON_TIME')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Tepat Waktu
                                </span>
                            @elseif($att->status === 'LATE')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Terlambat {{ $att->late_minutes }}m
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    {{ $att->status ?? 'Alpha' }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl">
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Masuk</span>
                                <span class="font-mono font-bold text-emerald-600">
                                    {{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Pulang</span>
                                <span class="font-mono font-bold text-rose-600">
                                    {{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $attendances->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
