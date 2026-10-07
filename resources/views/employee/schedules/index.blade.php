@extends('employee.layouts.app', ['title' => 'Jadwal Shift Kerja'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header Breadcrumb & Roster Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <nav class="flex items-center text-xs text-slate-500 mb-1 gap-1.5 font-medium">
                <a href="{{ route('employee.dashboard') }}" class="hover:text-amber-600">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-semibold">Jadwal Shift</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>🗓️ Roster Jadwal Shift Kerja</span>
            </h1>
        </div>

        <div class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-xs">
            Minggu Ini: {{ $startOfWeek->isoFormat('D MMM') }} &ndash; {{ $endOfWeek->isoFormat('D MMM Y') }}
        </div>
    </div>

    <!-- Informasi Shift & Jam Operasional -->
    <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-black text-lg flex items-center justify-center shadow-md shadow-amber-500/20 shrink-0">
                ⏰
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-900">Shift Rutin & Penugasan</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Departemen {{ $employee->department->name ?? 'Operasional' }} &bull; {{ $employee->full_name ?? auth()->user()->name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('employee.attendance.check-in') }}" 
               class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                <span>📸 Presensi Sesuai Jadwal</span>
            </a>
        </div>
    </div>

    <!-- Weekly Shift Calendar Grid -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base">Jadwal 7 Hari Minggu Berjalan</h3>
            <span class="text-xs font-semibold text-slate-400">Total: 7 Hari Kalender</span>
        </div>

        <div class="divide-y divide-slate-100">
            @for ($i = 0; $i < 7; $i++)
                @php
                    $currentDate = $startOfWeek->copy()->addDays($i);
                    $dateStr = $currentDate->toDateString();
                    $daySchedule = $schedules->firstWhere('date', $dateStr);
                    $isToday = $currentDate->isToday();
                    $isWeekend = $currentDate->isWeekend();
                @endphp
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $isToday ? 'bg-amber-50/40 border-l-4 border-l-amber-500' : 'hover:bg-slate-50/50' }} transition-colors">
                    
                    <!-- Tanggal & Hari -->
                    <div class="flex items-center space-x-3.5">
                        <div class="w-11 h-11 rounded-2xl flex flex-col items-center justify-center font-bold shrink-0 {{ $isToday ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : ($isWeekend ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700') }}">
                            <span class="text-[9px] uppercase tracking-wider font-extrabold">{{ $currentDate->isoFormat('ddd') }}</span>
                            <span class="text-sm font-black leading-none">{{ $currentDate->format('d') }}</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900 text-sm">
                                    {{ $currentDate->isoFormat('dddd, D MMMM Y') }}
                                </span>
                                @if($isToday)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white uppercase tracking-wider">
                                        Hari Ini
                                    </span>
                                @endif
                                @if($isWeekend)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        Libur Akhir Pekan
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-500 mt-0.5 block">
                                Lokasi: {{ $daySchedule?->officeLocation?->name ?? 'Kantor Pusat Jakarta' }}
                            </span>
                        </div>
                    </div>

                    <!-- Detail Jam Shift -->
                    <div class="flex items-center gap-3">
                        @if($daySchedule && $daySchedule->shift)
                            <div class="bg-white p-2.5 sm:px-4 rounded-xl border border-slate-200 text-right">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Shift</span>
                                <span class="text-xs font-extrabold text-slate-900">{{ $daySchedule->shift->name }}</span>
                                <span class="text-xs font-mono font-bold text-amber-600 block">
                                    {{ $daySchedule->shift->start_time }} &ndash; {{ $daySchedule->shift->end_time }}
                                </span>
                            </div>
                        @else
                            <div class="bg-slate-50 p-2.5 sm:px-4 rounded-xl border border-slate-200/80 text-right">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Jadwal Standar</span>
                                <span class="text-xs font-bold text-slate-800">Reguler Office</span>
                                <span class="text-xs font-mono text-slate-600 block">08:00 &ndash; 17:00</span>
                            </div>
                        @endif
                    </div>

                </div>
            @endfor
        </div>
    </div>

</div>
@endsection
