@extends('layouts.app', ['title' => 'ATS Pipeline Seleksi Pelamar'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 p-6 rounded-3xl shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-cyan-700 uppercase tracking-wider mb-1">
                <span>ATS Recruitment</span>
                <span>•</span>
                <span>Selection Pipeline</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pipeline Seleksi & Pelamar Kerja</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pantau perjalanan seleksi pelamar di 8 tahapan terstruktur (State Machine).
            </p>
        </div>

        <div class="flex items-center space-x-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-200">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'kanban']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode !== 'table' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                ⊞ Kanban Board
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $viewMode === 'table' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                ☰ Tabel Data
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
        <form action="{{ route('recruitment.applications.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <div class="sm:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama kandidat, email, atau no WhatsApp..."
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-indigo-600"
                />
            </div>

            <div>
                <select name="job_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600">
                    <option value="">-- Semua Posisi Lowongan --</option>
                    @foreach($jobPostings as $jp)
                        <option value="{{ $jp->id }}" {{ $jobId == $jp->id ? 'selected' : '' }}>{{ $jp->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="stage" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:bg-white focus:border-indigo-600">
                    <option value="">-- Semua Tahapan --</option>
                    @foreach(\App\Models\JobApplication::STAGES as $sKey => $sVal)
                        <option value="{{ $sKey }}" {{ $stage === $sKey ? 'selected' : '' }}>{{ $sVal['label'] }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white rounded-xl transition-colors shadow-xs">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Stage Badges Filter -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-thin">
        <a href="{{ route('recruitment.applications.index', array_filter(['view' => $viewMode, 'job_id' => $jobId])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !$stage ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:text-slate-900' }}">
            Semua ({{ $allApplications->count() }})
        </a>
        @foreach(\App\Models\JobApplication::STAGES as $sKey => $sVal)
            <a href="{{ route('recruitment.applications.index', array_filter(['view' => $viewMode, 'job_id' => $jobId, 'stage' => $sKey])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap flex items-center space-x-1.5 {{ $stage === $sKey ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:text-slate-900' }}">
                <span>{{ $sVal['label'] }}</span>
                <span class="px-1.5 py-0.2 rounded-md {{ $stage === $sKey ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }} text-[10px] font-bold">{{ $stageCounts[$sKey] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    @if($viewMode === 'table')
        <!-- ==========================================
             TABULAR VIEW
             ========================================== -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs overflow-hidden">
            @if($allApplications->isEmpty())
                <div class="py-12 text-center text-xs text-slate-500">
                    Tidak ada pelamar yang cocok dengan kriteria pencarian ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3">Kandidat</th>
                                <th class="pb-3">Posisi Dilamar</th>
                                <th class="pb-3">Tahap Saat Ini</th>
                                <th class="pb-3">Skor Psikotes</th>
                                <th class="pb-3">Tanggal Melamar</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($allApplications as $app)
                                @php
                                    $testResult = $app->psychotestResults->first();
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 pr-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $app->applicant_name }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center space-x-2 mt-0.5">
                                            <span>{{ $app->applicant_email }}</span>
                                            <span>•</span>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->applicant_phone) }}" target="_blank" class="text-emerald-700 hover:underline font-semibold">
                                                WA: {{ $app->applicant_phone }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <div class="font-semibold text-slate-800">{{ $app->jobPosting->title ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $app->jobPosting->department->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $app->current_stage === 'HIRED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($app->current_stage === 'REJECTED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200') }}">
                                            {{ $app->stage_label }}
                                        </span>
                                    </td>
                                    <td class="py-4 pr-4">
                                        @if($testResult)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $testResult->passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                {{ $testResult->score }}% ({{ $testResult->passed ? 'Lulus' : 'Gagal' }})
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[10px]">Belum tes</span>
                                        @endif
                                    </td>
                                    <td class="py-4 pr-4 text-slate-500 text-[11px]">
                                        {{ $app->applied_at?->format('d M Y, H:i') ?? '-' }}
                                    </td>
                                    <td class="py-4 text-right">
                                        <a href="{{ route('recruitment.applications.show', $app->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-xs transition-all">
                                            Review 360° &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <!-- ==========================================
             KANBAN BOARD VIEW (8-STAGE SELECTION PIPELINE)
             ========================================== -->
        <div class="overflow-x-auto pb-6">
            <div class="flex items-start space-x-4 min-w-[1700px]">
                @foreach(\App\Models\JobApplication::STAGES as $stageKey => $stageInfo)
                    @php
                        $stageApps = $allApplications->where('current_stage', $stageKey);
                    @endphp
                    <div class="w-80 shrink-0 bg-slate-100/80 border border-slate-200/90 rounded-3xl p-4 shadow-xs flex flex-col max-h-[85vh]">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold flex items-center justify-center text-[10px] shadow-xs">
                                    {{ $stageInfo['order'] > 0 ? $stageInfo['order'] : '✕' }}
                                </span>
                                <h3 class="text-xs font-bold text-slate-900 truncate max-w-[180px]" title="{{ $stageInfo['label'] }}">
                                    {{ $stageInfo['label'] }}
                                </h3>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-slate-600 border border-slate-200">
                                {{ $stageApps->count() }}
                            </span>
                        </div>

                        <!-- Column Cards Container -->
                        <div class="space-y-3 overflow-y-auto flex-1 pr-1 scrollbar-thin">
                            @forelse($stageApps as $app)
                                @php
                                    $testResult = $app->psychotestResults->first();
                                @endphp
                                <div class="p-4 rounded-2xl bg-white border border-slate-200/90 hover:border-indigo-400 transition-all shadow-xs group">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                                {{ $app->applicant_name }}
                                            </h4>
                                            <span class="text-[10px] text-slate-500 line-clamp-1">
                                                {{ $app->jobPosting->title ?? '-' }}
                                            </span>
                                        </div>
                                        @if($testResult)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $testResult->passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }} shrink-0">
                                                {{ $testResult->score }}%
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-[10px] text-slate-500 space-y-1 my-2.5 pb-2.5 border-b border-slate-100">
                                        <div class="flex items-center justify-between">
                                            <span>WhatsApp:</span>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->applicant_phone) }}" target="_blank" class="text-emerald-700 hover:underline font-semibold">
                                                {{ $app->applicant_phone }}
                                            </a>
                                        </div>
                                        @if($app->interview_scheduled_at)
                                            <div class="flex items-center justify-between text-amber-700 font-semibold">
                                                <span>Interview:</span>
                                                <span>{{ $app->interview_scheduled_at->format('d M, H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-[9px] text-slate-400">
                                            {{ $app->applied_at?->diffForHumans() ?? '-' }}
                                        </span>
                                        <a href="{{ route('recruitment.applications.show', $app->id) }}" class="px-3 py-1 rounded-lg text-[10px] font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition-colors shadow-xs">
                                            Review 360° &rarr;
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-[11px] text-slate-400 border border-dashed border-slate-200 rounded-2xl bg-white/50">
                                    Kosong
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
