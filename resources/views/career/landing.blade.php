<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Karir & Peluang Kerja - HRIS Enterprise Portal</title>
    <meta name="description" content="Temukan karir impian Anda di HRIS Enterprise. Eksplorasi lowongan kerja di bidang Engineering, Product, HR, dan Finance dengan budaya kerja fleksibel.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .glow-sphere {
            filter: blur(100px);
            opacity: 0.25;
            pointer-events: none;
            position: absolute;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 selection:bg-indigo-600 selection:text-white relative overflow-x-hidden">

    <!-- Ambient Gradient Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="glow-sphere w-[700px] h-[450px] -top-32 left-1/2 -translate-x-1/2 bg-gradient-to-tr from-indigo-200 via-cyan-100 to-purple-200"></div>
        <div class="glow-sphere w-[500px] h-[500px] top-[700px] -left-32 bg-blue-100"></div>
        <div class="glow-sphere w-[550px] h-[550px] top-[1400px] -right-32 bg-violet-100"></div>
    </div>

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('career.landing') }}" class="flex items-center space-x-2.5 sm:space-x-3.5 group shrink-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-cyan-500 flex items-center justify-center text-white font-black text-lg sm:text-xl shadow-md group-hover:scale-105 transition-transform">
                    H
                </div>
                <div>
                    <div class="flex items-center space-x-1.5 sm:space-x-2">
                        <span class="font-extrabold tracking-tight text-base sm:text-lg text-slate-900">HRIS Core</span>
                        <span class="text-[10px] sm:text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">Careers</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium tracking-wide hidden sm:block">Enterprise Talent Acquisition</span>
                </div>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
                <a href="#lowongan" class="hover:text-indigo-600 transition-colors">Lowongan Tersedia</a>
                <a href="#budaya" class="hover:text-indigo-600 transition-colors">Budaya & Benefit</a>
                <a href="#alur" class="hover:text-indigo-600 transition-colors">8 Tahapan Seleksi</a>
                <a href="#faq" class="hover:text-indigo-600 transition-colors">FAQ</a>
            </nav>

            <!-- Auth Action Buttons & Mobile Hamburger -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                @auth
                    @if(auth()->user()->isInternal())
                        <a href="{{ route('portal') }}" class="hidden sm:inline-flex px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:text-slate-900 hover:border-slate-300 transition-colors items-center space-x-1.5 shadow-xs">
                            <span>Portal HR</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif

                    <a href="{{ route('career.dashboard') }}" class="px-3 sm:px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="hidden xs:inline sm:inline">Dashboard Saya</span>
                        <span class="xs:hidden sm:hidden">Dashboard</span>
                    </a>

                    <form action="{{ route('career.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('career.login') }}" class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:text-indigo-600 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('career.register') }}" class="px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                        Daftar Akun
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" onclick="document.getElementById('mobile-career-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Dropdown Menu -->
        <div id="mobile-career-menu" class="hidden md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-xl px-4 py-4 space-y-2">
            <a href="#lowongan" onclick="document.getElementById('mobile-career-menu').classList.add('hidden')" class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-100">Lowongan Tersedia</a>
            <a href="#budaya" onclick="document.getElementById('mobile-career-menu').classList.add('hidden')" class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-100">Budaya & Benefit</a>
            <a href="#alur" onclick="document.getElementById('mobile-career-menu').classList.add('hidden')" class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-100">8 Tahapan Seleksi</a>
            <a href="#faq" onclick="document.getElementById('mobile-career-menu').classList.add('hidden')" class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-100">FAQ</a>
            @auth
                @if(auth()->user()->isInternal())
                    <a href="{{ route('portal') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-indigo-700 bg-indigo-50">Portal HR Staf &rarr;</a>
                @endif
            @endauth
        </div>
    </header>

    <!-- Main Hero Section -->
    <section class="relative pt-16 pb-16 lg:pt-24 lg:pb-24 overflow-hidden text-center">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Recruitment Pill Badge -->
            <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>We're Hiring • Bergabunglah Bersama Tim Unggulan</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 leading-tight mb-6">
                Bangun Masa Depan <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-indigo-600 via-sky-600 to-purple-600 bg-clip-text text-transparent">
                    Bersama Tim Berdampak Tinggi
                </span>
            </h1>

            <p class="text-base sm:text-xl text-slate-600 max-w-3xl mx-auto mb-10 leading-relaxed font-normal">
                Eksplorasi peluang karir terbaik di platform HRIS Enterprise. Nikmati fleksibilitas kerja, budaya kerja berbasis pertumbuhan, kompensasi transparan, dan kesempatan berkembang tanpa batas.
            </p>

            <!-- Search & Filter Card Form -->
            <div class="max-w-4xl mx-auto bg-white/95 backdrop-blur-2xl p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-xl shadow-slate-200/50">
                <form action="{{ route('career.landing') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3" id="career-search-form">
                    <!-- Keyword Input -->
                    <div class="md:col-span-5 relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari judul posisi atau keahlian..."
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        />
                    </div>

                    <!-- Department Select -->
                    <div class="md:col-span-4 relative">
                        <select
                            name="department_id"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        >
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ ($departmentId ?? '') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->job_postings_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Work Model Select -->
                    <div class="md:col-span-3">
                        <select
                            name="work_model"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600"
                        >
                            <option value="">Semua Tipe Kerja</option>
                            <option value="REMOTE" {{ ($workModel ?? '') == 'REMOTE' ? 'selected' : '' }}>Remote (WFH)</option>
                            <option value="HYBRID" {{ ($workModel ?? '') == 'HYBRID' ? 'selected' : '' }}>Hybrid</option>
                            <option value="ON_SITE" {{ ($workModel ?? '') == 'ON_SITE' ? 'selected' : '' }}>On-site (WFO)</option>
                        </select>
                    </div>

                    <!-- Submit / Reset Row -->
                    <div class="md:col-span-12 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 text-xs">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-slate-500">
                            <span class="font-semibold text-slate-400">Populer:</span>
                            <a href="{{ route('career.landing', ['search' => 'Backend']) }}" class="text-indigo-600 hover:underline font-semibold bg-indigo-50/60 px-2 py-0.5 rounded-md">Backend</a>
                            <a href="{{ route('career.landing', ['search' => 'Frontend']) }}" class="text-indigo-600 hover:underline font-semibold bg-indigo-50/60 px-2 py-0.5 rounded-md">Frontend</a>
                            <a href="{{ route('career.landing', ['search' => 'Recruiter']) }}" class="text-indigo-600 hover:underline font-semibold bg-indigo-50/60 px-2 py-0.5 rounded-md">HR Recruiter</a>
                            <a href="{{ route('career.landing', ['work_model' => 'REMOTE']) }}" class="text-cyan-600 hover:underline font-semibold bg-cyan-50/60 px-2 py-0.5 rounded-md">Remote</a>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                            @if(!empty($search) || !empty($departmentId) || !empty($workModel))
                                <a href="{{ route('career.landing') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800">Reset Filter</a>
                            @endif
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-xs text-center">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Key Metrics Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mt-12 pt-8 border-t border-slate-200 text-center">
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total_openings'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider mt-1 font-semibold">Posisi Terbuka</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-indigo-600 tracking-tight">{{ $stats['total_departments'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider mt-1 font-semibold">Departemen Hiring</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-cyan-600 tracking-tight">{{ $stats['remote_friendly_count'] ?? 0 }}</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider mt-1 font-semibold">Remote & Hybrid Roles</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-emerald-600 tracking-tight">{{ $stats['satisfaction_rate'] ?? 98 }}%</div>
                    <div class="text-xs text-slate-500 uppercase tracking-wider mt-1 font-semibold">Employee Happiness Index</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vacancy Listing Section -->
    <section id="lowongan" class="py-16 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Eksplorasi Karir</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Daftar Lowongan Pekerjaan Aktif</h2>
                </div>
                <div class="mt-4 md:mt-0 text-sm text-slate-500">
                    Menampilkan <span class="font-bold text-slate-900">{{ $jobs->total() }}</span> posisi siap dilamar
                </div>
            </div>

            <!-- Job Cards Grid -->
            @if($jobs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($jobs as $job)
                        <div class="bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-xl hover:shadow-indigo-50/50 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between group shadow-xs">
                            <div>
                                <!-- Tags Top -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                        {{ $job->department?->name ?? 'Umum' }}
                                    </span>

                                    @php
                                        $badgeClasses = match($job->work_model) {
                                            'REMOTE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'HYBRID' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ $badgeClasses }}">
                                        {{ $job->work_model_label }}
                                    </span>
                                </div>

                                <!-- Job Title -->
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1 mb-2">
                                    {{ $job->title }}
                                </h3>

                                <!-- Short Description -->
                                <p class="text-sm text-slate-600 line-clamp-2 mb-5 leading-relaxed">
                                    {{ $job->description }}
                                </p>

                                <!-- Meta Info -->
                                <div class="space-y-2 text-xs text-slate-600 mb-6">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span class="truncate">{{ $job->location }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>{{ $job->experience_level }} • {{ $job->employment_type_label }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Section -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Kompensasi</span>
                                    <span class="text-xs font-bold text-emerald-700">
                                        {{ $job->salary_formatted }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button
                                        type="button"
                                        onclick="openJobModal({{ json_encode($job) }}, '{{ $job->department?->name ?? 'Umum' }}')"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition-colors"
                                    >
                                        Detail
                                    </button>
                                    <a
                                        href="{{ route('career.jobs.show', $job->slug) }}"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all"
                                    >
                                        Lamar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $jobs->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Lowongan yang Cocok</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-4">Coba sesuaikan kata kunci pencarian atau reset filter departemen untuk melihat posisi lain.</p>
                    <a
                        href="{{ route('career.landing') }}"
                        class="inline-flex px-4 py-2 rounded-xl text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition-all"
                    >
                        Reset Filter Pencarian
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- The 8-Stage Selection Pipeline Section -->
    <section id="alur" class="py-20 bg-slate-50 border-t border-slate-200 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Alur Seleksi Terstruktur</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1">
                    Peta 8 Tahapan Seleksi Transparan
                </h2>
                <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                    Setiap kandidat dapat memantau status secara real-time di Candidate Dashboard, mulai dari pengiriman berkas hingga otomatis terdaftar sebagai pegawai Core HR.
                </p>
            </div>

            <!-- Pipeline Stepper Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stage 1 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center font-bold text-sm">01</span>
                        <span class="text-[11px] font-semibold text-slate-500">Tahap 1</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Applied & Screening CV</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pengumpulan berkas digital, screening portofolio & evaluasi otomatis kualifikasi awal oleh tim HR.
                    </p>
                </div>

                <!-- Stage 2 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center font-bold text-sm">02</span>
                        <span class="text-[11px] font-semibold text-cyan-700">Online Asesmen</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Shortlisted / Psikotes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Kandidat login ke Candidate Portal untuk mengerjakan ujian logika penalaran & analisis kepribadian secara online.
                    </p>
                </div>

                <!-- Stage 3 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold text-sm">03</span>
                        <span class="text-[11px] font-semibold text-emerald-700">Lulus Ujian</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Psychotest Passed</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Nilai psikotes di atas ambang batas (passing score). Notifikasi otomatis dikirimkan via WhatsApp & Email resmi.
                    </p>
                </div>

                <!-- Stage 4 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-sm">04</span>
                        <span class="text-[11px] font-semibold text-slate-500">Tahap 4</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Interview HR</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Wawancara mendalam mengenai riwayat pengalaman kerja, motivasi, kecocokan nilai budaya, dan ekspektasi karir.
                    </p>
                </div>

                <!-- Stage 5 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-sm">05</span>
                        <span class="text-[11px] font-semibold text-slate-500">Tahap 5</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Interview User (Manager)</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Uji kompetensi teknis, studi kasus pemecahan masalah nyata, dan evaluasi sinergi bersama Hiring Manager terkait.
                    </p>
                </div>

                <!-- Stage 6 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center font-bold text-sm">06</span>
                        <span class="text-[11px] font-semibold text-purple-700">Tahap Final</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Interview Direksi (BOD)</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sesi diskusi strategis bersama jajaran Direksi untuk menyelaraskan visi jangka panjang organisasi.
                    </p>
                </div>

                <!-- Stage 7 -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center font-bold text-sm">07</span>
                        <span class="text-[11px] font-semibold text-rose-700">Kesehatan</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Medical Check-Up (MCU)</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pemeriksaan medis menyeluruh melalui jaringan laboratorium rekanan resmi perusahaan tanpa biaya pelamar.
                    </p>
                </div>

                <!-- Stage 8 & Core HR Conversion -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-50 via-sky-50 to-white border-2 border-indigo-200 relative shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                            ✓
                        </span>
                        <span class="text-[11px] font-bold text-indigo-700">Core HR Engine</span>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Offering & Auto-Onboard</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Penandatanganan Offering Letter, pembuatan NIK otomatis, dan pencatatan master data pegawai aktif di Core HR.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Culture & Perks Section -->
    <section id="budaya" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Keuntungan & Fasilitas</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1">Mengapa Berkarier Bersama Kami?</h2>
                <p class="text-slate-600 text-sm mt-3">Kami berinvestasi pada kesejahteraan, pertumbuhan karir, dan kenyamanan seluruh insan perusahaan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 mb-4 font-bold text-lg">
                        🛡️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Asuransi Kesehatan Top-Tier</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Perlindungan rawat inap dan rawat jalan terkemuka untuk karyawan serta keluarga inti, termasuk pertanggungan kacamata dan gigi.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-200 flex items-center justify-center text-cyan-600 mb-4 font-bold text-lg">
                        ⏱️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Fleksibilitas Kerja Hybrid & Remote</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kami mengutamakan kualitas hasil kerja (output-driven) dengan fleksibilitas jam kerja dan dukungan kerja jarak jauh.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 mb-4 font-bold text-lg">
                        📚
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Annual Learning & Certification</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Tunjangan tahunan hingga Rp 8.000.000 untuk buku, kursus online, sertifikasi industri, dan tiket konferensi teknologi.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 mb-4 font-bold text-lg">
                        💻
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Fasilitas Hardware Premium</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pilihan MacBook Pro M-series atau ThinkPad enterprise terbaru beserta subsidi setup perlengkapan kerja di rumah.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 mb-4 font-bold text-lg">
                        🌱
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Mental Health & Wellness</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Akses gratis sesi konseling psikolog profesional tanpa batas, membership gym rekanan, dan program cuti ulang tahun.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-white hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 mb-4 font-bold text-lg">
                        ✨
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Performance Bonus & Equity</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Penghargaan finansial berbasis kinerja semesteran yang transparan serta kesempatan kepemilikan opsi saham karyawan (ESOP).
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Talent Pool Call To Action Banner -->
    <section class="py-16 bg-gradient-to-r from-indigo-600 via-indigo-700 to-cyan-600 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">
                Tidak Menemukan Posisi yang Sesuai Hari Ini?
            </h3>
            <p class="text-indigo-100 text-sm sm:text-base max-w-2xl mx-auto mb-8">
                Daftarkan akun kandidat Anda dan lengkapi profil CV Anda sekarang. Tim Talent Acquisition kami akan segera menghubungi Anda saat posisi yang relevan terbuka.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a
                    href="{{ route('career.register') }}"
                    class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-indigo-700 bg-white hover:bg-slate-50 shadow-lg shadow-black/10 transition-all"
                >
                    Daftar ke Talent Pool
                </a>
                <a
                    href="{{ route('career.login') }}"
                    class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-indigo-800/60 hover:bg-indigo-800/80 border border-indigo-400/40 transition-all"
                >
                    Masuk ke Candidate Portal
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section id="faq" class="py-20 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Pertanyaan Umum</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Frequently Asked Questions</h2>
            </div>

            <div class="space-y-4">
                <details class="group p-5 rounded-2xl bg-white border border-slate-200 open:border-indigo-400 shadow-xs transition-all">
                    <summary class="flex items-center justify-between font-bold text-slate-900 cursor-pointer list-none text-base">
                        <span>Bagaimana cara mengikuti ujian psikotes online?</span>
                        <span class="text-indigo-600 transition-transform group-open:rotate-180">▼</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Setelah berkas CV Anda lolos tahap awal (Shortlisted), Anda akan menerima pemberitahuan via WhatsApp dan Email untuk login ke Candidate Portal. Menu "Ujian Psikotes Online" akan aktif lengkap dengan timer hitung mundur dan panduan pengerjaan.
                    </p>
                </details>

                <details class="group p-5 rounded-2xl bg-white border border-slate-200 open:border-indigo-400 shadow-xs transition-all">
                    <summary class="flex items-center justify-between font-bold text-slate-900 cursor-pointer list-none text-base">
                        <span>Berapa lama proses seleksi rata-rata dari awal hingga penawaran?</span>
                        <span class="text-indigo-600 transition-transform group-open:rotate-180">▼</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Rata-rata proses seleksi membutuhkan waktu antara 7 hingga 14 hari kerja. Anda dapat memantau setiap pergeseran status secara transparan melalui linimasa di menu "Lamaran Saya".
                    </p>
                </details>

                <details class="group p-5 rounded-2xl bg-white border border-slate-200 open:border-indigo-400 shadow-xs transition-all">
                    <summary class="flex items-center justify-between font-bold text-slate-900 cursor-pointer list-none text-base">
                        <span>Apakah posisi Remote dapat dilamar dari luar kota atau luar pulau?</span>
                        <span class="text-indigo-600 transition-transform group-open:rotate-180">▼</span>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Tentu saja! Untuk posisi berlabel "Remote", Anda dapat bekerja dari seluruh wilayah Indonesia. Fasilitas laptop dan perlengkapan kerja akan dikirimkan langsung ke alamat domisili Anda setelah resmi diterima.
                    </p>
                </details>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-200 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">H</div>
                <span class="font-bold text-slate-700">HRIS Core Talent Acquisition Portal</span>
                <span>•</span>
                <span>Hak Cipta Dilindungi Undang-Undang</span>
            </div>
            <div class="flex items-center space-x-6 text-slate-600 font-medium">
                <a href="{{ route('career.landing') }}" class="hover:text-indigo-600">Portal Karir</a>
                <a href="{{ route('portal') }}" class="hover:text-indigo-600">HR Portal Internal</a>
                <a href="#faq" class="hover:text-indigo-600">Bantuan & FAQ</a>
            </div>
        </div>
    </footer>

    <!-- Job Quick-View Modal Dialog -->
    <div id="job-quick-modal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative shadow-2xl transition-all scale-95 duration-200" id="modal-box">
            <button type="button" onclick="closeJobModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-800 p-2 text-lg">
                ✕
            </button>

            <div class="flex items-center space-x-2 mb-3">
                <span id="modal-department" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Departemen
                </span>
                <span id="modal-model" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200">
                    Model Kerja
                </span>
            </div>

            <h3 id="modal-title" class="text-2xl font-bold text-slate-900 mb-2">Judul Posisi</h3>
            <p id="modal-location" class="text-xs text-slate-500 mb-6">Lokasi • Level Pengalaman</p>

            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 text-sm text-slate-700 mb-6 border-t border-b border-slate-100 py-4">
                <div>
                    <h4 class="font-bold text-indigo-700 mb-1 text-xs uppercase tracking-wider">Deskripsi Posisi</h4>
                    <p id="modal-description" class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"></p>
                </div>

                <div>
                    <h4 class="font-bold text-indigo-700 mb-1 text-xs uppercase tracking-wider">Persyaratan & Kualifikasi</h4>
                    <p id="modal-requirements" class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"></p>
                </div>

                <div>
                    <h4 class="font-bold text-indigo-700 mb-1 text-xs uppercase tracking-wider">Benefit & Fasilitas</h4>
                    <p id="modal-benefits" class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"></p>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between">
                <button type="button" onclick="closeJobModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Tutup
                </button>
                <a
                    id="modal-apply-btn"
                    href="#"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all"
                >
                    Lanjutkan Melamar Sekarang →
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Modal Javascript -->
    <script>
        function openJobModal(job, departmentName) {
            const modal = document.getElementById('job-quick-modal');
            const modalBox = document.getElementById('modal-box');

            document.getElementById('modal-department').innerText = departmentName || 'Umum';
            document.getElementById('modal-model').innerText = job.work_model || 'Full-time';
            document.getElementById('modal-title').innerText = job.title;
            document.getElementById('modal-location').innerText = (job.location || '') + ' • ' + (job.experience_level || '');
            document.getElementById('modal-description').innerText = job.description || '-';
            document.getElementById('modal-requirements').innerText = job.requirements || '-';
            document.getElementById('modal-benefits').innerText = job.benefits || '-';
            document.getElementById('modal-apply-btn').href = '/career/jobs/' + job.slug;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalBox.classList.remove('scale-95');
                modalBox.classList.add('scale-100');
            }, 10);
        }

        function closeJobModal() {
            const modal = document.getElementById('job-quick-modal');
            const modalBox = document.getElementById('modal-box');
            modalBox.classList.remove('scale-100');
            modalBox.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 150);
        }

        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeJobModal();
        });
    </script>
</body>
</html>
