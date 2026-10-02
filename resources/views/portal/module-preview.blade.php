<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $domain['name'] }} - HRIS Core Enterprise</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Glow Effects -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Navigation Header -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('portal') }}" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-extrabold shadow-md shadow-indigo-500/20">
                        HR
                    </div>
                    <div>
                        <span class="font-extrabold text-white text-base tracking-wide block leading-tight">HRIS Core Enterprise</span>
                        <span class="text-[11px] text-indigo-400 font-semibold tracking-wider uppercase">Spesifikasi Modul</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('portal') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors">
                    &larr; Kembali ke Portal
                </a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-colors">
                    Buka Core HR
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10 space-y-8">

        <!-- Module Header Card -->
        <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden backdrop-blur-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr {{ $domain['gradient'] }} text-white flex items-center justify-center font-extrabold text-2xl shadow-xl shrink-0">
                        {{ strtoupper(substr($domain['slug'], 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $domain['category'] }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                {{ $domain['status_label'] }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-white mt-1">{{ $domain['name'] }}</h1>
                        <p class="text-slate-400 text-sm mt-2 max-w-2xl leading-relaxed">{{ $domain['description'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Column Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Architectural Scope & Domain Folder (1 col) -->
            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-4">
                <h3 class="font-bold text-white text-base border-b border-slate-800 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span>Struktur Folder Modul</span>
                </h3>

                <div class="bg-slate-950 p-4 rounded-xl font-mono text-xs text-indigo-300 leading-relaxed border border-slate-800">
                    <span class="text-slate-500">app/Domains/ (Modules/)</span><br>
                    &boxur;&boxh;&boxh; <strong class="text-white">{{ ucfirst($domain['slug']) }}/</strong><br>
                    &nbsp;&nbsp;&nbsp;&boxvr;&boxh;&boxh; Models/<br>
                    &nbsp;&nbsp;&nbsp;&boxvr;&boxh;&boxh; Controllers/<br>
                    &nbsp;&nbsp;&nbsp;&boxvr;&boxh;&boxh; Services/<br>
                    &nbsp;&nbsp;&nbsp;&boxvr;&boxh;&boxh; Repositories/<br>
                    &nbsp;&nbsp;&nbsp;&boxur;&boxh;&boxh; Routes/web.php
                </div>

                <p class="text-xs text-slate-400 leading-relaxed">
                    Setiap modul dirancang independen dengan arsitektur Domain-Driven Modular, menghindari <em>god-table</em> dan siap dipisah menjadi Microservices jika diperlukan di masa depan.
                </p>
            </div>

            <!-- Feature Roadmap (2 cols) -->
            <div class="md:col-span-2 bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-4">
                <h3 class="font-bold text-white text-base border-b border-slate-800 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Kapabilitas & Fitur yang Direncanakan</span>
                    </div>
                    <span class="text-xs text-slate-500 font-mono">{{ count($domain['features']) }} Fitur Inti</span>
                </h3>

                <div class="space-y-3">
                    @foreach ($domain['features'] as $index => $feature)
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
                            <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-white">{{ $feature }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Terintegrasi dengan basis data Core HR yang telah terpasang di PostgreSQL.</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-800">
                    <span class="text-xs text-slate-400">Status Fondasi Database:</span>
                    <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Kompatibel dengan Core HR Schema
                    </span>
                </div>
            </div>

        </div>

        <!-- Navigation Bar to Other Modules -->
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Navigasi Domain Lainnya:</h4>
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-500 transition-colors">
                    Core HR (Aktif)
                </a>
                @foreach ($domains as $s => $d)
                    @if ($s !== 'core-hr')
                        <a href="{{ route('modules.show', $s) }}" class="px-3.5 py-2 rounded-xl {{ $s === $domain['slug'] ? 'bg-slate-700 text-white font-bold border border-slate-600' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-medium border border-slate-700/60' }} transition-colors">
                            {{ $d['name'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} HRIS Core Corporation &bull; Enterprise Human Capital Management Architecture
        </div>
    </footer>
</body>
</html>
