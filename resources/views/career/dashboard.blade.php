<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Candidate Dashboard - HRIS Careers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-indigo-600 selection:text-white">

    <!-- Subtle Background Glow -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-indigo-100/60 blur-3xl rounded-full"></div>
    </div>

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('career.landing') }}" class="flex items-center space-x-3.5 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-md">
                    H
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold tracking-tight text-lg text-slate-900">HRIS Core</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">Candidate Portal</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium">Dashboard Pelacak Status Seleksi</span>
                </div>
            </a>

            <div class="flex items-center space-x-4">
                <a href="{{ route('career.landing') }}" class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                    Eksplorasi Lowongan
                </a>

                <div class="h-4 w-px bg-slate-200"></div>

                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-900">{{ $user->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ $user->email }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <form action="{{ route('career.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-3 shadow-xs">
                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-8 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-sm flex items-center space-x-3 shadow-xs">
                <svg class="w-5 h-5 shrink-0 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif

        <!-- Welcome & Profile Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-8 mb-8 sm:mb-10 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-3.5 sm:space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl shadow-md shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 truncate">{{ $user->name }}</h1>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Kandidat Aktif</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate">
                        {{ $user->email }} • WhatsApp: {{ $user->phone ?? 'Belum diisi' }}
                    </p>
                </div>
            </div>

            @php
                $profile = $user->candidateProfile;
                $isCompleted = $profile?->is_completed ?? false;
            @endphp

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full md:w-auto">
                <a href="{{ route('career.profile') }}" class="w-full sm:w-auto justify-center px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-all flex items-center space-x-2 text-center">
                    <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>{{ $isCompleted ? 'Edit Data Diri & CV' : 'Lengkapi Data Diri & CV*' }}</span>
                </a>

                <a href="{{ route('career.landing') }}" class="w-full sm:w-auto justify-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center space-x-2 text-center">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Lamar Posisi Lain</span>
                </a>
            </div>
        </div>

        <!-- Section: My Applications (Pelacak Timeline Status Seleksi) -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Status Lamaran Pekerjaan Saya</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pantau linimasa 8 tahapan seleksi secara transparan dan real-time.</p>
                </div>
                <div class="text-xs text-slate-500">
                    Total: <span class="font-bold text-slate-900">{{ $applications->count() }}</span> lamaran
                </div>
            </div>

            @if($applications->count() > 0)
                <div class="space-y-6">
                    @foreach($applications as $app)
                        @php
                            $stages = \App\Models\JobApplication::STAGES;
                            $currentStageKey = $app->current_stage;
                            $currentOrder = $app->stage_order;
                        @endphp
                        <div class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-8 shadow-sm">
                            <!-- Header Lamaran -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                                <div>
                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1.5">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                            {{ $app->jobPosting?->department?->name ?? 'Umum' }}
                                        </span>
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200/80">
                                            {{ $app->jobPosting?->work_model_label ?? 'Full-time' }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-slate-900">{{ $app->jobPosting?->title ?? 'Posisi Pekerjaan' }}</h3>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Dilamar pada: {{ $app->applied_at ? $app->applied_at->format('d M Y, H:i') : $app->created_at->format('d M Y') }}
                                    </p>
                                </div>

                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1 font-semibold">Status Saat Ini</span>
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold inline-block
                                        {{ $currentStageKey === 'REJECTED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                        {{ in_array($currentStageKey, ['HIRED', 'OFFERING']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                        {{ !in_array($currentStageKey, ['REJECTED', 'HIRED', 'OFFERING']) ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}">
                                        {{ $app->stage_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- 8-Stage Selection Pipeline Tracker (Visual Stepper) -->
                            <div class="py-6 sm:py-8">
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 sm:mb-6">Linimasa Tahapan Seleksi (8-Stage Pipeline)</h4>

                                <div class="flex overflow-x-auto pb-3 gap-2.5 sm:grid sm:grid-cols-4 lg:grid-cols-8 sm:gap-3 sm:overflow-visible">
                                    @php
                                        $pipelineKeys = ['APPLIED', 'SHORTLISTED', 'PSYCHOTEST_PASSED', 'INTERVIEW_HR', 'INTERVIEW_USER', 'INTERVIEW_BOD', 'MCU', 'OFFERING'];
                                    @endphp

                                    @foreach($pipelineKeys as $idx => $stageKey)
                                        @php
                                            $stageInfo = $stages[$stageKey];
                                            $stepNum = $idx + 1;
                                            $isCompleted = ($currentOrder > $stageInfo['order']) && ($currentStageKey !== 'REJECTED');
                                            $isCurrent = ($currentStageKey === $stageKey);
                                            $isPending = ($currentOrder < $stageInfo['order']);
                                        @endphp

                                        <div class="min-w-[130px] sm:min-w-0 p-3 sm:p-3.5 rounded-2xl border transition-all text-center flex flex-col justify-between shrink-0 sm:shrink
                                            {{ $isCurrent ? 'bg-indigo-50 border-2 border-indigo-600 text-indigo-950 shadow-sm' : '' }}
                                            {{ $isCompleted ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : '' }}
                                            {{ $isPending ? 'bg-slate-50/60 border-slate-200 text-slate-400' : '' }}">
                                            <div>
                                                <div class="w-7 h-7 mx-auto rounded-full flex items-center justify-center font-bold text-xs mb-2
                                                    {{ $isCurrent ? 'bg-indigo-600 text-white shadow-xs' : '' }}
                                                    {{ $isCompleted ? 'bg-emerald-500 text-white shadow-xs' : '' }}
                                                    {{ $isPending ? 'bg-slate-200 text-slate-600' : '' }}">
                                                    {{ $isCompleted ? '✓' : $stepNum }}
                                                </div>
                                                <div class="text-xs font-bold leading-tight mb-1 break-words" title="{{ $stageInfo['label'] }}">
                                                    {{ $stageInfo['label'] }}
                                                </div>
                                            </div>

                                            <div class="text-[10px] mt-2 font-medium">
                                                @if($isCurrent)
                                                    <span class="text-indigo-700 font-bold">Sedang Berjalan</span>
                                                @elseif($isCompleted)
                                                    <span class="text-emerald-700 font-semibold">Selesai</span>
                                                @else
                                                    <span>Tahap {{ $stepNum }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Stage Action Card -->
                            <div class="mt-2 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                                <div class="text-xs text-slate-600">
                                    <span class="font-bold text-slate-900 block mb-0.5">Keterangan Tahap:</span>
                                    <span>{{ $app->stage_description }}</span>
                                </div>

                                @if(in_array($currentStageKey, ['SHORTLISTED', 'PSYCHOTEST_PASSED']))
                                    <div class="w-full sm:w-auto shrink-0">
                                        <a href="{{ route('career.psychotests.index', $app->id) }}" class="w-full sm:w-auto text-center justify-center px-5 py-3 rounded-xl text-xs font-bold text-white {{ $currentStageKey === 'SHORTLISTED' ? 'bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 shadow-md shadow-indigo-600/20' : 'bg-slate-800 hover:bg-slate-700' }} transition-all inline-flex items-center space-x-2">
                                            <span>{{ $currentStageKey === 'SHORTLISTED' ? '⚡ Kerjakan Psikotes Online (Kraepelin & Kepribadian)' : '📊 Lihat Skor Psikotes' }}</span>
                                            <span>→</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Belum ada lamaran -->
                <div class="text-center py-16 bg-white border border-dashed border-slate-200 rounded-3xl shadow-xs">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Lamaran yang Dikirim</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Anda telah memiliki akun kandidat! Jelajahi posisi yang sesuai dengan keahlian Anda dan kirimkan lamaran.</p>
                    <a
                        href="{{ route('career.landing') }}"
                        class="inline-flex px-6 py-3 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all"
                    >
                        Jelajahi Lowongan Pekerjaan →
                    </a>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
