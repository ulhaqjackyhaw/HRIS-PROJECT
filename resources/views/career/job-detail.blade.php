<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $job->title }} - HRIS Careers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-indigo-600 selection:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <a href="{{ route('career.landing') }}" class="flex items-center space-x-2.5 sm:space-x-3.5">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-lg sm:text-xl shadow-md">
                    H
                </div>
                <div>
                    <span class="font-extrabold tracking-tight text-base sm:text-lg text-slate-900">HRIS Core</span>
                    <span class="text-[10px] sm:text-xs text-slate-500 block font-medium">Careers Portal</span>
                </div>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-4">
                <a href="{{ route('career.landing') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                    ← <span class="hidden sm:inline">Kembali ke </span>Lowongan
                </a>
                @guest
                    <a href="{{ route('career.register', ['job' => $job->slug]) }}" class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                        Daftar & Lamar
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <!-- Main Detail Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-10 mb-10 shadow-sm">
            <!-- Top Badges -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                    {{ $job->department?->name ?? 'Umum' }}
                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200/80">
                    {{ $job->work_model_label }}
                </span>
                <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                    {{ $job->salary_formatted }}
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-3">{{ $job->title }}</h1>
            <p class="text-sm text-slate-500 mb-8">{{ $job->location }} • {{ $job->experience_level }} • {{ $job->employment_type_label }}</p>

            <div class="space-y-8 text-slate-700">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Tentang Peran Ini</h3>
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $job->description }}</p>
                </div>

                @if($job->requirements)
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Kualifikasi & Persyaratan</h3>
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $job->requirements }}</p>
                    </div>
                @endif

                @if($job->benefits)
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Fasilitas & Keuntungan</h3>
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $job->benefits }}</p>
                    </div>
                @endif
            </div>

            <!-- Apply Action Card -->
            <div class="mt-12 pt-8 border-t border-slate-100">
                @auth
                    @php
                        $alreadyApplied = \App\Models\JobApplication::where('job_posting_id', $job->id)->where('user_id', auth()->id())->exists();
                        $profile = $profile ?? auth()->user()?->candidateProfile;
                    @endphp

                    @if($alreadyApplied)
                        <div class="p-5 sm:p-6 rounded-2xl bg-indigo-50 border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="text-sm font-bold text-indigo-950">Anda telah mengirimkan lamaran untuk posisi ini.</div>
                                <div class="text-xs text-indigo-700 mt-0.5">Pantau linimasa tahapan seleksi Anda di Candidate Dashboard.</div>
                            </div>
                            <a href="{{ route('career.dashboard') }}" class="w-full sm:w-auto text-center px-6 py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                                Lihat Status Lamaran →
                            </a>
                        </div>
                    @elseif(! ($profile && $profile->is_completed))
                        <!-- Jika data inti belum lengkap: diarahkan untuk melengkapi data -->
                        <div class="p-5 sm:p-8 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="flex items-start space-x-3.5 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center font-bold text-lg sm:text-xl shrink-0">
                                    !
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-amber-950 mb-1">Lengkapi Data Inti Profil Anda</h3>
                                    <p class="text-xs text-amber-800 leading-relaxed max-w-xl">
                                        Data identitas inti dan berkas CV Anda belum lengkap. Silakan lengkapi formulir data inti satu kali agar Anda dapat langsung melamar ke seluruh posisi pekerjaan.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('career.profile', ['job' => $job->slug]) }}" class="w-full sm:w-auto shrink-0 px-6 py-3.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-md shadow-amber-600/20 transition-all flex items-center justify-center space-x-2 text-center">
                                <span>Lengkapi Data Sekarang</span>
                                <span>→</span>
                            </a>
                        </div>
                    @else
                        <!-- Data profil inti sudah lengkap: LANGSUNG KIRIM (1-Click Apply) -->
                        <div class="p-5 sm:p-8 rounded-2xl bg-gradient-to-r from-indigo-50/80 via-sky-50/60 to-white border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-xs">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">✓ Profil Lengkap & Siap Kirim</span>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">Kirim Lamaran untuk Posisi Ini</h3>
                                <p class="text-xs text-slate-600">
                                    Melamar sebagai <strong class="text-slate-900">{{ $profile->full_name ?? auth()->user()->name }}</strong> ({{ auth()->user()->email }}) • WhatsApp: <strong class="text-slate-800">{{ $profile->phone_wa ?? auth()->user()->phone }}</strong>
                                </p>
                            </div>

                            <form action="{{ route('career.jobs.apply', $job->slug) }}" method="POST" class="w-full sm:w-auto shrink-0">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2"
                                >
                                    <span>Kirim Lamaran Sekarang</span>
                                    <span>→</span>
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Tertarik dengan posisi ini?</div>
                            <div class="text-xs text-slate-500">Buat akun pelamar atau masuk untuk melamar posisi ini.</div>
                        </div>

                        <div class="flex items-center space-x-3 w-full sm:w-auto">
                            <a href="{{ route('career.login', ['job' => $job->slug]) }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-all">
                                Masuk Akun
                            </a>
                            <a href="{{ route('career.register', ['job' => $job->slug]) }}" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                                Daftar & Lamar
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Related Jobs -->
        @if($relatedJobs->count() > 0)
            <div class="mt-12">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Posisi Lain di Departemen Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedJobs as $rel)
                        <a href="{{ route('career.jobs.show', $rel->slug) }}" class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all block group">
                            <span class="text-xs text-indigo-600 font-semibold block mb-1">{{ $rel->work_model_label }}</span>
                            <h4 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">{{ $rel->title }}</h4>
                            <span class="text-xs text-slate-500 block mt-2">{{ $rel->location }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
</body>
</html>
