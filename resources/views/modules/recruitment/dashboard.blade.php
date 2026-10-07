@extends('layouts.app', ['title' => 'Dashboard Recruitment & ATS'])

@section('content')
<div class="space-y-8">
    <!-- Top Greeting & Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-white via-cyan-50/40 to-indigo-50/30 border border-slate-200/90 p-6 sm:p-8 rounded-3xl shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-cyan-700 uppercase tracking-wider mb-1">
                <span>ATS Recruitment Hub</span>
                <span>•</span>
                <span>Talent Acquisition Engine</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Rekrutmen & ATS</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                Pantau seluruh alur penerimaan talenta mulai dari publikasi lowongan, screening berkas CV, ujian psikotes online, wawancara bertingkat, hingga konversi otomatis karyawan ke Core HR.
            </p>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
            <a href="{{ route('recruitment.applications.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-all flex items-center space-x-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Pipeline ATS</span>
            </a>
            <a href="{{ route('recruitment.jobs.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 shadow-md shadow-cyan-600/20 transition-all flex items-center space-x-2">
                <span>+ Terbitkan Lowongan</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Lowongan Aktif -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-cyan-400 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lowongan Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-200 text-cyan-600 flex items-center justify-center font-bold text-sm">
                    💼
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $activeJobsCount }}</div>
            <div class="text-xs text-slate-500 flex items-center justify-between">
                <span>Dari total {{ $totalJobsCount }} posisi</span>
                <a href="{{ route('recruitment.jobs.index') }}" class="text-cyan-700 hover:underline font-semibold">Kelola →</a>
            </div>
        </div>

        <!-- Total Lamaran Masuk -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-indigo-400 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Lamaran</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    📄
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $totalApplicationsCount }}</div>
            <div class="text-xs text-slate-500 flex items-center justify-between">
                <span>Semua berkas kandidat masuk</span>
                <a href="{{ route('recruitment.applications.index') }}" class="text-indigo-600 hover:underline font-semibold">Lihat →</a>
            </div>
        </div>

        <!-- Kandidat Dalam Proses -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-amber-400 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dalam Proses Seleksi</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold text-sm">
                    ⏳
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $inProgressCount }}</div>
            <div class="text-xs text-slate-500">
                Tahap screening, psikotes & interview
            </div>
        </div>

        <!-- Hired / Auto-Onboarded -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden group hover:border-emerald-400 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lolos & Hired</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    🎉
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $hiredCount }}</div>
            <div class="text-xs text-slate-500 flex items-center justify-between">
                <span>Dikonversi ke Core HR</span>
                <a href="{{ route('employees.index') }}" class="text-emerald-700 hover:underline font-semibold">Data Karyawan →</a>
            </div>
        </div>
    </div>

    <!-- 8-Stage Selection Funnel Bar -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Pipeline 8 Tahapan Seleksi (State Machine)</h2>
                <p class="text-xs text-slate-500">Distribusi jumlah kandidat pada masing-masing fase rekrutmen aktif.</p>
            </div>
            <a href="{{ route('recruitment.applications.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                Buka Kanban Board →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            @foreach(\App\Models\JobApplication::STAGES as $stageKey => $stageInfo)
                @if($stageKey !== 'REJECTED')
                    <a href="{{ route('recruitment.applications.index', ['stage' => $stageKey]) }}" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-400 hover:bg-white transition-all text-center block group shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 block mb-1">Tahap {{ $stageInfo['order'] }}</span>
                        <div class="text-xl font-black text-slate-900 group-hover:text-indigo-600 transition-colors">
                            {{ $stageCounts[$stageKey] ?? 0 }}
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600 block mt-1 truncate" title="{{ $stageInfo['label'] }}">
                            {{ $stageInfo['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Two-Column Grid: Recent Applications & Active Jobs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Applications Table (2 Columns Span) -->
        <div class="lg:col-span-2 p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Lamaran Terbaru Masuk</h3>
                    <p class="text-xs text-slate-500">Kandidat yang baru mengirimkan formulir lamaran.</p>
                </div>
                <a href="{{ route('recruitment.applications.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Lihat Semua ({{ $totalApplicationsCount }}) &rarr;
                </a>
            </div>

            @if($recentApplications->isEmpty())
                <div class="p-8 text-center border border-dashed border-slate-200 rounded-2xl text-xs text-slate-500">
                    Belum ada berkas lamaran yang masuk.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3">Pelamar</th>
                                <th class="pb-3">Posisi Dilamar</th>
                                <th class="pb-3">Tahap</th>
                                <th class="pb-3">Waktu</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($recentApplications as $app)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3.5 pr-4">
                                        <div class="font-bold text-slate-900">{{ $app->applicant_name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $app->applicant_email }}</div>
                                    </td>
                                    <td class="py-3.5 pr-4">
                                        <div class="font-semibold text-slate-800">{{ $app->jobPosting->title ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $app->jobPosting->department->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 pr-4">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $app->current_stage === 'HIRED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($app->current_stage === 'REJECTED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200') }}">
                                            {{ $app->stage_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 pr-4 text-slate-500 text-[11px]">
                                        {{ $app->applied_at?->diffForHumans() ?? '-' }}
                                    </td>
                                    <td class="py-3.5 text-right">
                                        <a href="{{ route('recruitment.applications.show', $app->id) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-xs transition-colors">
                                            Review 360°
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right Side: Active Jobs & Psychotest Summary -->
        <div class="space-y-6">
            <!-- Active Jobs Card -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Lowongan Aktif</h3>
                    <a href="{{ route('recruitment.jobs.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline">Semua &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse($activeJobs as $job)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 line-clamp-1">{{ $job->title }}</h4>
                                <span class="text-[10px] text-slate-500">{{ $job->department->name ?? '-' }} • {{ $job->work_model_label }}</span>
                            </div>
                            <span class="px-2 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 text-[10px] font-bold shrink-0">
                                {{ $job->applications_count }} Pelamar
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 py-4 text-center">Belum ada lowongan berstatus PUBLISHED.</div>
                    @endforelse
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100">
                    <a href="{{ route('recruitment.jobs.create') }}" class="w-full block py-2.5 text-center text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                        + Tambah Lowongan Pekerjaan
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-50 via-sky-50 to-white border border-indigo-200 shadow-xs space-y-3">
                <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">Integrasi Terpadu</span>
                <h4 class="text-sm font-bold text-slate-900">Portal Kandidat Publik</h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Lihat pengalaman tampilan sisi kandidat saat mencari lowongan, melamar kerja, hingga mengerjakan psikotes online.
                </p>
                <a href="{{ route('career.landing') }}" target="_blank" class="inline-flex items-center space-x-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-xl transition-all shadow-xs">
                    <span>Buka /career</span>
                    <span>↗</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
