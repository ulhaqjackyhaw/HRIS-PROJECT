<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal Modul - HRIS Core Enterprise Suite</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Glow Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-violet-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-extrabold shadow-md shadow-indigo-500/20">
                    HR
                </div>
                <div>
                    <span class="font-extrabold text-white text-base tracking-wide block leading-tight">HRIS Core Enterprise</span>
                    <span class="text-[11px] text-indigo-400 font-semibold tracking-wider uppercase">Domain Modular Portal</span>
                </div>
            </div>

            <!-- User Menu & Logout -->
            <div class="flex items-center space-x-4">
                <div class="hidden sm:flex items-center space-x-3 px-3 py-1.5 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <span class="text-xs font-semibold text-slate-200">{{ auth()->user()->name ?? 'Administrator' }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-xl transition-colors border border-slate-800 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10 space-y-10">

        <!-- Hero Title & Architecture Summary -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 pb-6 border-b border-slate-800/80">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-medium mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    Architecture: Domain-Driven Modular Suite
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Pusat Modul Operasional SDM
                </h1>
                <p class="text-slate-400 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed">
                    Selamat datang kembali, <strong class="text-white">{{ auth()->user()->name }}</strong>. Pilih modul domain kerja yang ingin Anda kelola hari ini.
                </p>
            </div>

            <!-- Quick Search Input -->
            <div class="w-full md:w-80">
                <div class="relative">
                    <input type="text" 
                           id="module-search" 
                           placeholder="Cari domain atau modul..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-900/90 border border-slate-800 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- System Architecture Directory Tree Banner -->
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-slate-800 text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Struktur Domain Modul Terstandarisasi</h3>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono">app/Domains/ (Modules/) &bull; Siap diekspansi bertahap tanpa god-table</p>
                </div>
            </div>

            <!-- Stats Pill -->
            <div class="flex items-center gap-4 text-xs font-mono">
                <div class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-slate-300">
                    <span class="text-slate-400 font-sans">Karyawan:</span> <strong class="text-indigo-400">{{ $stats['total_employees'] }}</strong>
                </div>
                <div class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-slate-300">
                    <span class="text-slate-400 font-sans">Departemen:</span> <strong class="text-indigo-400">{{ $stats['total_departments'] }}</strong>
                </div>
                <div class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-slate-300">
                    <span class="text-slate-400 font-sans">Jabatan:</span> <strong class="text-indigo-400">{{ $stats['total_positions'] }}</strong>
                </div>
            </div>
        </div>

        <!-- 8 DOMAIN MODULES GRID -->
        <div id="modules-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            @foreach ($domains as $slug => $domain)
                @php
                    $isActive = ($domain['status'] === 'active');
                @endphp

                <div class="module-card group bg-slate-900/80 hover:bg-slate-900 border {{ $isActive ? 'border-indigo-500/50 hover:border-indigo-500 ring-1 ring-indigo-500/20' : 'border-slate-800 hover:border-slate-700' }} rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/5 relative overflow-hidden"
                     data-title="{{ strtolower($domain['name'] . ' ' . $domain['description'] . ' ' . implode(' ', $domain['features'])) }}">

                    @if ($isActive)
                        <div class="absolute top-0 right-0 w-28 h-28 bg-indigo-500/10 rounded-bl-full pointer-events-none"></div>
                    @endif

                    <!-- Card Header -->
                    <div>
                        <!-- Top Meta -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr {{ $domain['gradient'] }} text-white flex items-center justify-center font-extrabold text-lg shadow-md group-hover:scale-105 transition-transform">
                                @if ($slug === 'core-hr')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @elseif ($slug === 'recruitment')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif ($slug === 'attendance')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif ($slug === 'payroll')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif ($slug === 'performance')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @elseif ($slug === 'engagement')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif ($slug === 'helpdesk')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                @endif
                            </div>

                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $isActive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($domain['status'] === 'in_progress' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700') }}">
                                @if ($isActive)
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
                                @endif
                                {{ $domain['status_label'] }}
                            </span>
                        </div>

                        <!-- Category -->
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">
                            {{ $domain['category'] }}
                        </span>

                        <!-- Title -->
                        <h2 class="text-lg font-bold text-white mt-1 group-hover:text-indigo-300 transition-colors">
                            {{ $domain['name'] }}
                        </h2>

                        <!-- Description -->
                        <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                            {{ $domain['description'] }}
                        </p>

                        <!-- Key Feature List -->
                        <div class="mt-4 pt-4 border-t border-slate-800/80 space-y-1.5">
                            @foreach (array_slice($domain['features'], 0, 3) as $feat)
                                <div class="flex items-center gap-2 text-xs text-slate-300">
                                    <svg class="w-3.5 h-3.5 {{ $isActive ? 'text-indigo-400' : 'text-slate-500' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="truncate">{{ $feat }}</span>
                                </div>
                            @endforeach
                            @if (count($domain['features']) > 3)
                                <div class="text-[11px] text-slate-500 pl-5">
                                    +{{ count($domain['features']) - 3 }} fitur kapabilitas lainnya
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6 pt-4 border-t border-slate-800/80">
                        @if ($isActive)
                            <a href="{{ route('dashboard') }}" 
                               class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 flex items-center justify-center gap-2 transition-all cursor-pointer">
                                <span>Buka Modul Core HR</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('modules.show', $slug) }}" 
                               class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700/60 flex items-center justify-center gap-1.5 transition-all">
                                <span>Lihat Spesifikasi & Roadmap</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} HRIS Core Corporation &bull; Enterprise Human Capital Management Architecture
        </div>
    </footer>

    <!-- Interactive Client Filtering Script -->
    <script>
        document.getElementById('module-search')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.module-card');

            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                if (title.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
