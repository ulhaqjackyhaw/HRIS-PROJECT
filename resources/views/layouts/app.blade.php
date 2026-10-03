<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'HRIS Core' }} - Human Resource Information System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-100 flex flex-col md:flex-row">

@php
    $isAttendance = request()->is('attendance*');
    $moduleName = $isAttendance ? 'Time & Attendance' : 'Core HR';
    $moduleSubtitle = $isAttendance ? 'Waktu & Presensi' : 'Kepegawaian & Master Data';
    $activeThemeColor = $isAttendance ? 'amber' : 'indigo';
@endphp

    <!-- Mobile Navigation Toggle Bar -->
    <div class="md:hidden bg-slate-900 text-white flex items-center justify-between p-4 border-b border-slate-800">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg {{ $isAttendance ? 'bg-amber-600' : 'bg-indigo-600' }} flex items-center justify-center font-bold text-white shadow-md">
                {{ $isAttendance ? 'AT' : 'HR' }}
            </div>
            <div>
                <span class="font-bold tracking-tight text-sm block leading-tight">{{ $moduleName }}</span>
                <span class="text-[10px] text-slate-400">HRIS Enterprise</span>
            </div>
        </div>
        <button type="button" id="mobile-menu-btn" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar-nav" class="hidden md:flex flex-col w-full md:w-64 bg-slate-900 text-slate-300 min-h-screen shrink-0 border-r border-slate-800 transition-all">
        <!-- Logo Header -->
        <div class="h-16 flex items-center px-5 border-b border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-9 h-9 rounded-xl {{ $isAttendance ? 'bg-gradient-to-tr from-amber-600 to-orange-500 shadow-amber-500/30' : 'bg-gradient-to-tr from-indigo-600 to-violet-500 shadow-indigo-500/30' }} flex items-center justify-center text-white font-extrabold shadow-lg shrink-0">
                    {{ $isAttendance ? 'AT' : 'HR' }}
                </div>
                <div class="min-w-0">
                    <span class="font-bold text-white text-base tracking-wide block leading-none truncate">{{ $moduleName }}</span>
                    <span class="text-[10px] {{ $isAttendance ? 'text-amber-400' : 'text-indigo-400' }} font-medium tracking-wider uppercase truncate block mt-0.5">{{ $moduleSubtitle }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
            <!-- App Switcher to Domain Hub -->
            <div class="mb-4">
                <a href="{{ route('portal') }}" class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-xs font-semibold text-slate-300 hover:text-white border border-slate-700/60 transition-colors group">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400 group-hover:rotate-90 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span>Pusat Modul Hub</span>
                    </span>
                    <span class="text-[10px] {{ $isAttendance ? 'bg-amber-500/20 text-amber-300' : 'bg-indigo-500/20 text-indigo-300' }} px-2 py-0.5 rounded-full font-medium">Ganti &rarr;</span>
                </a>
            </div>

            @if ($isAttendance)
                {{-- ============================================================== --}}
                {{-- ATTENDANCE MODULE SPECIFIC SIDEBAR --}}
                {{-- ============================================================== --}}
                <p class="px-3 text-xs font-semibold text-amber-400/80 uppercase tracking-wider mb-2">Self-Service ESS</p>

                <a href="{{ route('attendance.check-in') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('attendance.check-in*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Presensi Selfie & GPS</span>
                </a>

                <a href="{{ route('leaves.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('leaves.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Pengajuan Cuti & Izin</span>
                </a>

                <a href="{{ route('approvals.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('approvals.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Persetujuan Manajer (MSS)</span>
                </a>

                <p class="px-3 text-xs font-semibold text-amber-400/80 uppercase tracking-wider mt-5 mb-2">Monitoring & Audit</p>

                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('attendance.index') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Monitoring Presensi</span>
                </a>

                <a href="{{ route('attendance.summary') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('attendance.summary') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Rekapitulasi Presensi HR</span>
                </a>

                <p class="px-3 text-xs font-semibold text-amber-400/80 uppercase tracking-wider mt-5 mb-2">Pengaturan Shift & Jadwal</p>

                <a href="{{ route('shifts.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('shifts.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Master Shift Kerja</span>
                </a>

                <a href="{{ route('locations.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('locations.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Lokasi & Geofence</span>
                </a>

                <a href="{{ route('schedules.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('schedules.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Roster & Jadwal</span>
                </a>

                <p class="px-3 text-xs font-semibold text-amber-400/80 uppercase tracking-wider mt-5 mb-2">Lembur & SPL</p>

                <a href="{{ route('overtimes.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('overtimes.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Lembur & Approval</span>
                </a>

                <!-- Quick Switcher to Core HR -->
                <div class="pt-4 mt-6 border-t border-slate-800/80">
                    <p class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Pintas Modul Lain</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-indigo-300 hover:bg-slate-800/60 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span>Core HR & Kepegawaian</span>
                        </span>
                        <span class="text-[10px] text-slate-500">&rarr;</span>
                    </a>
                </div>

            @else
                {{-- ============================================================== --}}
                {{-- CORE HR MODULE SPECIFIC SIDEBAR --}}
                {{-- ============================================================== --}}
                <p class="px-3 text-xs font-semibold text-indigo-400/80 uppercase tracking-wider mb-2">Ikhtisar Domain</p>

                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard Core HR</span>
                </a>

                <p class="px-3 text-xs font-semibold text-indigo-400/80 uppercase tracking-wider mt-5 mb-2">Manajemen SDM</p>

                <a href="{{ route('employees.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('employees.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Data Karyawan</span>
                </a>

                <p class="px-3 text-xs font-semibold text-indigo-400/80 uppercase tracking-wider mt-5 mb-2">Master Organisasi</p>

                <a href="{{ route('departments.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('departments.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Departemen / Unit</span>
                </a>

                <a href="{{ route('positions.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('positions.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Jabatan & Posisi</span>
                </a>

                <!-- Quick Switcher to Attendance -->
                <div class="pt-4 mt-6 border-t border-slate-800/80">
                    <p class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Pintas Modul Lain</p>
                    <a href="{{ route('attendance.check-in') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Time & Attendance</span>
                        </span>
                        <span class="text-[10px] text-slate-500">&rarr;</span>
                    </a>
                </div>
            @endif
        </nav>

        <!-- Sidebar Footer with User Profile & Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center justify-between px-2 py-1.5">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-9 h-9 rounded-full {{ $isAttendance ? 'bg-amber-600' : 'bg-indigo-600' }} text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@hris.corp' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar / Logout" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $isAttendance ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $isAttendance ? 'bg-amber-500' : 'bg-indigo-500' }}"></span>
                    {{ $moduleName }}
                </span>
                <span class="text-slate-300">/</span>
                <h1 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight">@yield('header', 'Overview')</h1>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('portal') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors border border-indigo-200 cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Portal Modul</span>
                </a>

                @yield('actions')

                <!-- Quick Logout Button on Header -->
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors border border-slate-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        <main class="flex-1 p-6 sm:p-8 max-w-7xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 p-4 bg-rose-50 border border-rose-200/80 text-rose-800 rounded-2xl shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200/80 text-rose-800 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2 mb-2 font-semibold text-sm text-rose-900">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Terdapat kesalahan pada input formulir:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Toggle Script for Mobile -->
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar-nav');
            sidebar.classList.toggle('hidden');
        });
    </script>
</body>
</html>
