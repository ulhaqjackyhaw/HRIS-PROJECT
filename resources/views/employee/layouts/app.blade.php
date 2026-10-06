<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Portal Karyawan' }} - HRIS ESS Enterprise</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-800 flex flex-col selection:bg-amber-600 selection:text-white pb-20 md:pb-8">

    <!-- Top Header Bar (Modern ESS Header) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left: ESS Brand & Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('employee.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform">
                            ESS
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 text-base tracking-tight block leading-tight">HRIS Portal Karyawan</span>
                            <span class="text-[10px] text-amber-700 font-bold uppercase tracking-wider block">Employee Self-Service</span>
                        </div>
                    </a>
                </div>

                <!-- Center: Desktop Horizontal Tab Navigation -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('employee.dashboard') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.dashboard') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Beranda
                    </a>
                    <a href="{{ route('employee.attendance.check-in') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.attendance.check-in') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Presensi Selfie
                    </a>
                    <a href="{{ route('employee.attendance.history') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.attendance.history') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Riwayat Presensi
                    </a>
                    <a href="{{ route('employee.leaves.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.leaves.*') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Cuti & Izin
                    </a>
                    <a href="{{ route('employee.overtimes.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.overtimes.*') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Lembur
                    </a>
                    <a href="{{ route('employee.schedules.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.schedules.*') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Jadwal Shift
                    </a>
                    <a href="{{ route('employee.profile') }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('employee.profile') ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Profil Saya
                    </a>
                </nav>

                <!-- Right: User Avatar & Logout -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-amber-700 font-semibold uppercase tracking-wider">Karyawan Aktif</span>
                    </div>

                    <a href="{{ route('employee.profile') }}" 
                       title="Lihat Profil" 
                       class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-orange-500 text-white font-black text-xs flex items-center justify-center border-2 border-white shadow-sm hover:scale-105 transition-transform">
                        {{ strtoupper(substr(auth()->user()->name ?? 'EM', 0, 2)) }}
                    </a>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar dari Portal" 
                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Shell -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Flash Error / Info Notification -->
        @if(session('error') || $errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1 shadow-xs">
                @if(session('error'))
                    <div class="font-medium flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @foreach($errors->all() as $err)
                    <div class="font-medium flex items-center gap-2">
                        <span>•</span>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation Bar (App-Like Bottom Bar) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200 shadow-xl px-2 py-1.5 flex items-center justify-around" style="touch-action: manipulation;">
        
        <!-- 1. Home -->
        <a href="{{ route('employee.dashboard') }}" 
           class="flex flex-col items-center justify-center w-14 py-1 rounded-xl text-[10px] font-semibold transition-all {{ request()->routeIs('employee.dashboard') ? 'text-amber-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('employee.dashboard') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Beranda</span>
        </a>

        <!-- 2. Presensi (Center Floating Style) -->
        <a href="{{ route('employee.attendance.check-in') }}" 
           class="flex flex-col items-center justify-center -mt-4 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-lg shadow-amber-500/30 group-active:scale-90 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-bold text-amber-700 mt-1">Presensi</span>
        </a>

        <!-- 3. Cuti -->
        <a href="{{ route('employee.leaves.index') }}" 
           class="flex flex-col items-center justify-center w-14 py-1 rounded-xl text-[10px] font-semibold transition-all {{ request()->routeIs('employee.leaves.*') ? 'text-amber-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('employee.leaves.*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Cuti</span>
        </a>

        <!-- 4. Lembur -->
        <a href="{{ route('employee.overtimes.index') }}" 
           class="flex flex-col items-center justify-center w-14 py-1 rounded-xl text-[10px] font-semibold transition-all {{ request()->routeIs('employee.overtimes.*') ? 'text-amber-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('employee.overtimes.*') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span>Lembur</span>
        </a>

        <!-- 5. Profil -->
        <a href="{{ route('employee.profile') }}" 
           class="flex flex-col items-center justify-center w-14 py-1 rounded-xl text-[10px] font-semibold transition-all {{ request()->routeIs('employee.profile') ? 'text-amber-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('employee.profile') ? 'stroke-[2.5]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profil</span>
        </a>
    </nav>

</body>
</html>
