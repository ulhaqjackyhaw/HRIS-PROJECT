@extends('layouts.app', ['title' => 'Monitoring Psikotes Online'])

@section('content')
<div class="space-y-8">
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 p-6 sm:p-8 rounded-3xl shadow-xs">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold text-cyan-700 uppercase tracking-wider mb-1">
                <span>ATS Recruitment Hub</span>
                <span>•</span>
                <span>Online Psychometrics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Monitoring Tes Psikotes Online</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Pantau bank tes psikometri, ambang batas kelulusan (passing score), serta riwayat skor ujian yang telah diselesaikan oleh kandidat pelamar.
            </p>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
            <a href="{{ route('recruitment.applications.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-all flex items-center space-x-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Pipeline ATS</span>
            </a>
            <a href="{{ route('recruitment.dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-all flex items-center space-x-2">
                <span>← Dashboard ATS</span>
            </a>
        </div>
    </div>

    <!-- Active Psychotest Banks Section -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-extrabold text-slate-900 flex items-center space-x-2">
                <span>🧩</span>
                <span>Bank Soal & Modul Tes Aktif ({{ $psychotests->count() }})</span>
            </h2>
            <span class="text-xs text-slate-500">Modul otomatis terintegrasi dengan tahap seleksi kandidat</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($psychotests as $test)
                @php
                    $qCount = is_array($test->questions_data) ? count($test->questions_data) : 0;
                @endphp
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 hover:border-cyan-400 shadow-xs transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center space-x-1.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $test->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $test->is_active ? '● Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $test->isKraepelin() ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                    {{ $test->isKraepelin() ? '⚡ KRAEPELIN' : ($test->isLikertPersonality() ? '🧠 LIKERT 1-5' : 'UMUM') }}
                                </span>
                            </div>
                            <span class="text-xs font-bold text-cyan-700 bg-cyan-50 px-3 py-1 rounded-full border border-cyan-200">
                                Passing: {{ $test->passing_score }}%
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 mb-2">{{ $test->title }}</h3>
                        <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed">
                            {{ $test->description ?? 'Paket asesmen logika kognitif dan kepribadian profesional.' }}
                        </p>

                        <div class="grid grid-cols-3 gap-2 py-3 border-y border-slate-100 text-center">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Durasi</div>
                                <div class="text-xs font-extrabold text-slate-900 mt-0.5">{{ $test->duration_minutes }} Menit</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Jumlah Soal</div>
                                <div class="text-xs font-extrabold text-cyan-700 mt-0.5">{{ $qCount }} Butir</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Peserta</div>
                                <div class="text-xs font-extrabold text-indigo-600 mt-0.5">{{ $test->results_count }} Selesai</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between text-xs text-slate-500">
                        <span>Status Sistem:</span>
                        <span class="font-bold text-emerald-700">Auto-Scoring Ready</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 rounded-3xl bg-white border border-dashed border-slate-200 text-center">
                    <p class="text-sm text-slate-500">Belum ada modul psikotes yang dibuat.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Candidate Results Table Section (Grouped by Candidate) -->
    <div class="bg-white border border-slate-200/90 rounded-3xl shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-extrabold text-slate-900 flex items-center space-x-2">
                        <span>📊</span>
                        <span>Riwayat Hasil Tes Psikotes Pelamar</span>
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                        {{ $candidateApplications->total() }} Kandidat
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Daftar kandidat yang telah menyelesaikan asesmen psikometri beserta rincian modul dan skor kelulusan.</p>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('recruitment.psychotests.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kandidat / lowongan..."
                        class="w-64 pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:border-cyan-600 focus:ring-1 focus:ring-cyan-600 focus:outline-none transition-colors"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-cyan-700 hover:bg-cyan-800 transition-colors shadow-2xs cursor-pointer">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('recruitment.psychotests.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4">Lowongan Kerja</th>
                        <th class="px-6 py-4">Modul Psikotes & Skor</th>
                        <th class="px-6 py-4 text-center">Ringkasan Evaluasi</th>
                        <th class="px-6 py-4">Waktu Ujian</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($candidateApplications as $app)
                        @php
                            $results = $app->psychotestResults;
                            $totalCompleted = $results->count();
                            $avgScore = $totalCompleted > 0 ? round($results->avg('total_score'), 1) : 0;
                            $hasRetake = $results->contains('can_retake', true);
                            $allPassed = $totalCompleted > 0 && $results->every(function ($r) {
                                return $r->result_status === 'PASSED' || $r->total_score >= ($r->psychotest?->passing_score ?? 70);
                            });
                            $latestCompleted = $results->sortByDesc('completed_at')->first()?->completed_at;

                            // Initials for avatar
                            $initials = collect(explode(' ', $app->applicant_name ?? 'Kandidat'))
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->take(2)
                                ->join('');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors align-top">
                            <!-- Kandidat Info -->
                            <td class="px-6 py-5">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-200 text-cyan-800 font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 text-sm leading-snug truncate">
                                            {{ $app->applicant_name ?? 'Kandidat #'.$app->id }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                                            {{ $app->applicant_email ?? '-' }}
                                        </div>
                                        @if($app->applicant_phone)
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                📞 {{ $app->applicant_phone }}
                                            </div>
                                        @endif
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $app->stage_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Lowongan Kerja -->
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-800 leading-snug">
                                    {{ $app->jobPosting?->title ?? 'Posisi Terhapus' }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium mt-1">
                                    {{ $app->jobPosting?->department?->name ?? 'Dept. Umum' }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-2">
                                    Melamar: {{ $app->applied_at ? $app->applied_at->format('d M Y') : '-' }}
                                </div>
                            </td>

                            <!-- Modul Psikotes & Skor (Individual Test Cards) -->
                            <td class="px-6 py-5">
                                <div class="space-y-2.5 min-w-[340px] max-w-[460px]">
                                    @foreach($results as $result)
                                        @php
                                            $score = $result->total_score;
                                            $passingScore = $result->psychotest?->passing_score ?? 70;
                                            $passed = $result->result_status === 'PASSED' || $score >= $passingScore;
                                            $isKraepelin = $result->psychotest?->isKraepelin() || (($result->answers_submitted['type'] ?? '') === 'KRAEPELIN');
                                            $isLikert = $result->psychotest?->isLikertPersonality() || (($result->answers_submitted['type'] ?? '') === 'LIKERT_PERSONALITY');
                                        @endphp
                                        <div class="p-3 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 shadow-2xs transition-all space-y-2">
                                            <!-- Card Header: Title & Score -->
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <div class="flex items-center space-x-1.5 mb-0.5">
                                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $isKraepelin ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : ($isLikert ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                            {{ $isKraepelin ? '⚡ Kraepelin' : ($isLikert ? '🧠 Likert' : 'Logika/Umum') }}
                                                        </span>
                                                        <span class="text-[10px] text-slate-400 font-semibold">
                                                            (Pass: {{ $passingScore }}%)
                                                        </span>
                                                    </div>
                                                    <h4 class="font-bold text-slate-900 text-xs leading-snug truncate" title="{{ $result->psychotest?->title ?? 'Modul Psikotes' }}">
                                                        {{ $result->psychotest?->title ?? 'Modul Psikotes' }}
                                                    </h4>
                                                </div>

                                                <div class="flex flex-col items-end shrink-0">
                                                    <div class="inline-flex items-baseline space-x-0.5">
                                                        <span class="text-sm font-extrabold {{ $passed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $score }}</span>
                                                        <span class="text-[10px] text-slate-400">/ 100</span>
                                                    </div>
                                                    <div class="mt-0.5">
                                                        @if($result->can_retake)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                                                                ⚠️ Izin Uji Ulang
                                                            </span>
                                                        @elseif($passed)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                ✓ LULUS
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                                ✗ TIDAK LULUS
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Metric Details (Kraepelin / Likert) -->
                                            @if(is_array($result->answers_submitted))
                                                @if($isKraepelin)
                                                    <div class="flex flex-wrap items-center justify-between gap-1.5 pt-1.5 border-t border-slate-100 text-[10px] text-slate-600">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-bold text-cyan-700">Akurasi {{ $result->answers_submitted['accuracy_rate'] ?? '-' }}%</span>
                                                            <span>•</span>
                                                            <span>{{ $result->answers_submitted['total_attempted'] ?? '-' }} hitungan ({{ $result->answers_submitted['columns_completed'] ?? '-' }} kol)</span>
                                                        </div>
                                                        <button type="button"
                                                                onclick='openKraepelinModal(@json($result->answers_submitted), "{{ addslashes($app->applicant_name) }}", "{{ $result->total_score }}", "{{ $result->result_status }}")'
                                                                class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all inline-flex items-center gap-1 cursor-pointer">
                                                            <span>📈 Lihat Kurva</span>
                                                        </button>
                                                    </div>
                                                @elseif($isLikert)
                                                    <div class="pt-1.5 border-t border-slate-100 text-[10px] text-purple-700 flex items-center justify-between">
                                                        <span>🧠 Likert: Rata-rata {{ $result->answers_submitted['average_rating'] ?? '-' }}/5.0</span>
                                                        <span class="text-slate-400">{{ count($result->answers_submitted['answers'] ?? []) }} butir</span>
                                                    </div>
                                                @endif
                                            @endif

                                            <!-- Card Footer: Attempt info & HR Retake controls -->
                                            <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-100 text-[10px]">
                                                <div class="text-slate-400">
                                                    Ujian ke-{{ $result->attempt_number ?? 1 }}
                                                    @if($result->completed_at)
                                                        • {{ $result->completed_at->format('d/m/Y H:i') }}
                                                    @endif
                                                </div>

                                                <div class="flex items-center space-x-1.5">
                                                    @if($result->can_retake)
                                                        <form action="{{ route('recruitment.applications.psychotests.cancel-retake', [$app->id, $result->psychotest_id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all cursor-pointer" title="Batalkan Izin Uji Ulang">
                                                                ✕ Batal Retake
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button"
                                                                onclick='openRetakeModalIndex("{{ $app->id }}", "{{ $result->psychotest_id }}", "{{ addslashes($app->applicant_name) }}", "{{ addslashes($result->psychotest?->title ?? 'Modul') }}")'
                                                                class="px-2 py-1 rounded-lg text-[10px] font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-all cursor-pointer" title="Izinkan Uji Ulang Modul Ini">
                                                            🔄 Retake
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Ringkasan Evaluasi -->
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex flex-col items-center p-3 rounded-2xl bg-slate-50 border border-slate-200/80 w-full max-w-[140px]">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Rata-Rata</div>
                                    <div class="inline-flex items-baseline space-x-0.5">
                                        <span class="text-lg font-black text-slate-900">{{ $avgScore }}</span>
                                        <span class="text-[10px] text-slate-400">/ 100</span>
                                    </div>
                                    <div class="text-[10px] font-semibold text-slate-500 mt-1">
                                        {{ $totalCompleted }} Modul Selesai
                                    </div>
                                    <div class="mt-2.5">
                                        @if($hasRetake)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                                                ⚠️ Ada Retake
                                            </span>
                                        @elseif($allPassed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ✓ LULUS SEMUA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                ✗ SEBAGIAN GAGAL
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Waktu Ujian Terakhir -->
                            <td class="px-6 py-5">
                                <div class="font-semibold text-slate-700 text-xs">
                                    {{ $latestCompleted ? $latestCompleted->format('d M Y') : '-' }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $latestCompleted ? $latestCompleted->format('H:i') . ' WIB' : '' }}
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('recruitment.applications.show', $app->id) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all inline-flex items-center space-x-1.5 shadow-2xs">
                                    <span>Dossier</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="text-3xl mb-2">📝</div>
                                <div class="font-semibold text-slate-900">Belum ada kandidat yang menyelesaikan psikotes</div>
                                <p class="text-xs text-slate-400 mt-1">Kandidat yang masuk tahap psikotes akan mengerjakan ujian melalui portal kandidat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($candidateApplications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $candidateApplications->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Kurva Kraepelin Detail (HR View) -->
<div id="kraepelin-hr-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl space-y-5 my-auto max-h-[92vh] overflow-y-auto">
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 mb-2">
                    <span>⚡ KRAEPELIN NUMERIK</span>
                </div>
                <h3 class="text-lg font-black text-slate-900" id="hr-modal-candidate-name">Kurva Kerja Kraepelin</h3>
                <p class="text-xs text-slate-500 mt-0.5">Analisis ritme kerja, kecepatan, ketelitian, dan ketahanan kalkulasi per kolom.</p>
            </div>
            <button type="button" onclick="closeKraepelinModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-lg font-bold cursor-pointer transition-colors">
                ✕
            </button>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Panker (Kecepatan)</span>
                <span class="text-sm font-black text-slate-900 mt-0.5 block" id="hr-modal-panker">-</span>
                <span class="text-[10px] text-slate-500">Rata-rata hitungan/kolom</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Tianker (Ketelitian)</span>
                <span class="text-sm font-black text-emerald-600 mt-0.5 block" id="hr-modal-tianker">-</span>
                <span class="text-[10px] text-slate-500">Akurasi kalkulasi</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Hanker (Ketahanan)</span>
                <span class="text-sm font-black text-indigo-600 mt-0.5 block" id="hr-modal-hanker">-</span>
                <span class="text-[10px] text-slate-500">Tren kurva kerja</span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-400 block">Janker (Kestabilan)</span>
                <span class="text-sm font-black text-amber-600 mt-0.5 block" id="hr-modal-janker">-</span>
                <span class="text-[10px] text-slate-500">Rentang fluktuasi puncak</span>
            </div>
        </div>

        <!-- SVG Chart Container -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 px-1">
                <span>Grafik Kurva Kerja Kolom (1 - 30)</span>
                <span class="text-[11px] text-amber-600 font-semibold" id="hr-modal-avg-label">-</span>
            </div>
            <div id="hr-modal-chart-wrapper" class="w-full bg-white rounded-xl p-3 border border-slate-200/80 overflow-hidden">
                <!-- SVG injected via JS -->
            </div>
        </div>

        <!-- Column breakdown table -->
        <div>
            <div class="text-xs font-bold text-slate-800 mb-2">Tabel Rincian Kalkulasi Tiap Kolom:</div>
            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold sticky top-0">
                        <tr>
                            <th class="px-3 py-2">Kolom</th>
                            <th class="px-3 py-2 text-center">Hitungan</th>
                            <th class="px-3 py-2 text-center text-emerald-700">Benar</th>
                            <th class="px-3 py-2 text-center text-rose-700">Keliru</th>
                            <th class="px-3 py-2 text-right">Akurasi</th>
                        </tr>
                    </thead>
                    <tbody id="hr-modal-tbody" class="divide-y divide-slate-100 bg-white text-[11px]">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="closeKraepelinModal()" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openKraepelinModal(data, applicantName, score, status) {
        const modal = document.getElementById('kraepelin-hr-modal');
        if (!modal) return;

        document.getElementById('hr-modal-candidate-name').textContent = `Kurva Kraepelin: ${applicantName} (Skor: ${score}/100)`;

        const details = data.column_details || [];
        const metrics = data.metrics || {};
        const totalAttempted = data.total_attempted || 0;
        const accuracy = data.accuracy_rate || 0;
        const colCount = Math.max(1, data.columns_completed || details.length);

        const panker = metrics.panker || (totalAttempted / colCount).toFixed(1);
        const tianker = metrics.tianker || accuracy;
        const hanker = metrics.hanker_trend || 'Stabil (Konsisten)';
        const janker = metrics.janker !== undefined ? `±${metrics.janker}` : '-';

        document.getElementById('hr-modal-panker').textContent = `${panker} butir`;
        document.getElementById('hr-modal-tianker').textContent = `${tianker}%`;
        document.getElementById('hr-modal-hanker').textContent = hanker;
        document.getElementById('hr-modal-janker').textContent = janker;
        document.getElementById('hr-modal-avg-label').textContent = `Garis Panker: ${panker} butir/kolom`;

        // Render SVG Chart
        drawModalChart(details, 'hr-modal-chart-wrapper');

        // Render Table
        const tbody = document.getElementById('hr-modal-tbody');
        if (tbody) {
            if (details.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-3 py-4 text-center text-slate-400">Rincian per kolom belum tersedia untuk tes ini.</td></tr>`;
            } else {
                tbody.innerHTML = details.map(d => {
                    const attempted = d.attempted || d.rows_attempted || 0;
                    const correct = d.correct !== undefined ? d.correct : attempted;
                    const incorrect = d.incorrect !== undefined ? d.incorrect : 0;
                    const acc = attempted > 0 ? Math.round((correct / attempted) * 100) : 0;
                    return `<tr>
                        <td class="px-3 py-1.5 font-bold text-slate-800">Kolom ${d.column || (d.column_index + 1)}</td>
                        <td class="px-3 py-1.5 text-center font-bold text-blue-600">${attempted}</td>
                        <td class="px-3 py-1.5 text-center text-emerald-600 font-semibold">${correct}</td>
                        <td class="px-3 py-1.5 text-center text-rose-600 font-semibold">${incorrect}</td>
                        <td class="px-3 py-1.5 text-right font-bold text-slate-700">${acc}%</td>
                    </tr>`;
                }).join('');
            }
        }

        modal.classList.remove('hidden');
    }

    function closeKraepelinModal() {
        const modal = document.getElementById('kraepelin-hr-modal');
        if (modal) modal.classList.add('hidden');
    }

    function drawModalChart(details, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;
        if (!details || details.length === 0) {
            wrapper.innerHTML = `<div class="p-6 text-center text-xs text-slate-400">Data rincian kolom tidak tersedia.</div>`;
            return;
        }

        const n = details.length;
        const attemptedList = details.map(d => d.attempted || d.rows_attempted || 0);
        const maxVal = Math.max(15, ...attemptedList) + 3;
        const minVal = 0;
        const width = 740;
        const height = 210;
        const padL = 36;
        const padR = 24;
        const padT = 16;
        const padB = 28;
        const chartW = width - padL - padR;
        const chartH = height - padT - padB;

        const points = details.map((d, i) => {
            const att = d.attempted || d.rows_attempted || 0;
            const x = padL + (i / Math.max(1, n - 1)) * chartW;
            const y = padT + chartH - ((att - minVal) / (maxVal - minVal)) * chartH;
            return {
                x: Math.round(x * 10) / 10,
                y: Math.round(y * 10) / 10,
                col: d.column || (d.column_index + 1),
                attempted: att,
                correct: d.correct !== undefined ? d.correct : att,
                incorrect: d.incorrect !== undefined ? d.incorrect : 0
            };
        });

        const avg = attemptedList.reduce((s, v) => s + v, 0) / Math.max(1, n);
        const avgY = Math.round((padT + chartH - ((avg - minVal) / (maxVal - minVal)) * chartH) * 10) / 10;

        const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
        const areaPath = `${linePath} L ${points[points.length - 1].x} ${padT + chartH} L ${points[0].x} ${padT + chartH} Z`;

        const gridSteps = 4;
        let gridLinesSvg = '';
        for (let step = 0; step <= gridSteps; step++) {
            const val = Math.round(minVal + (step / gridSteps) * (maxVal - minVal));
            const gy = Math.round((padT + chartH - (step / gridSteps) * chartH) * 10) / 10;
            gridLinesSvg += `
                <line x1="${padL}" y1="${gy}" x2="${width - padR}" y2="${gy}" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3,3" />
                <text x="${padL - 6}" y="${gy + 3}" fill="#94a3b8" font-size="9" text-anchor="end" font-weight="600">${val}</text>
            `;
        }

        let pointsSvg = '';
        points.forEach((p) => {
            pointsSvg += `
                <g class="cursor-pointer">
                    <circle cx="${p.x}" cy="${p.y}" r="3" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                    <title>Kolom ${p.col}: ${p.attempted} hitungan (${p.correct} benar, ${p.incorrect} salah)</title>
                </g>
            `;
        });

        let xLabelsSvg = '';
        const labelInterval = n <= 10 ? 1 : (n <= 25 ? 2 : 5);
        for (let i = 0; i < n; i += labelInterval) {
            const p = points[i];
            xLabelsSvg += `
                <text x="${p.x}" y="${height - 8}" fill="#64748b" font-size="9" text-anchor="middle" font-weight="600">K${p.col}</text>
            `;
        }
        if ((n - 1) % labelInterval !== 0) {
            const lastP = points[n - 1];
            xLabelsSvg += `
                <text x="${lastP.x}" y="${height - 8}" fill="#64748b" font-size="9" text-anchor="middle" font-weight="600">K${lastP.col}</text>
            `;
        }

        wrapper.innerHTML = `
            <svg viewBox="0 0 ${width} ${height}" class="w-full h-auto select-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="hrKraepelinGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.01" />
                    </linearGradient>
                </defs>
                ${gridLinesSvg}
                <line x1="${padL}" y1="${avgY}" x2="${width - padR}" y2="${avgY}" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4,4" />
                <text x="${width - padR}" y="${avgY - 4}" fill="#d97706" font-size="9" font-weight="bold" text-anchor="end">Panker (Rata-rata): ${avg.toFixed(1)}</text>
                <path d="${areaPath}" fill="url(#hrKraepelinGrad)" />
                <path d="${linePath}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                ${pointsSvg}
                ${xLabelsSvg}
            </svg>
        `;
    }

    function openRetakeModalIndex(appId, testId, candName, testTitle) {
        document.getElementById('retake-index-cand-name').textContent = candName;
        document.getElementById('retake-index-test-title').textContent = testTitle;
        document.getElementById('retake-index-form').action = `{{ url('recruitment/applications') }}/${appId}/psychotests/${testId}/allow-retake`;
        document.getElementById('retake-index-modal').classList.remove('hidden');
    }

    function closeRetakeModalIndex() {
        document.getElementById('retake-index-modal').classList.add('hidden');
    }
</script>

<!-- Modal Atur Uji Ulang (Monitoring Hub) -->
<div id="retake-index-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-4 my-auto">
        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200 inline-block mb-1">
                    🔄 Manajemen Uji Ulang
                </span>
                <h3 class="text-base font-bold text-slate-900">Atur Izin Uji Ulang Psikotes</h3>
            </div>
            <button type="button" onclick="closeRetakeModalIndex()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold cursor-pointer">
                ✕
            </button>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Apakah Anda yakin ingin mengizinkan kandidat <strong id="retake-index-cand-name" class="text-slate-900">-</strong> mengerjakan ulang tes <strong id="retake-index-test-title" class="text-slate-900">-</strong>?
        </p>

        <form id="retake-index-form" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="retake_index_reason" class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Catatan HR untuk Uji Ulang</label>
                <textarea
                    id="retake_index_reason"
                    name="reason"
                    rows="3"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl text-xs focus:bg-white focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 focus:outline-none transition-colors"
                    placeholder="Contoh: Kendala teknis / izin pengujian ulang hasil nilai..."
                ></textarea>
                <span class="text-[10px] text-slate-500">Catatan ini akan dapat dibaca kandidat di portal karir sebelum mengerjakan tes kembali.</span>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeRetakeModalIndex()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-600/20 transition-colors">
                    ✓ Konfirmasi Izinkan Uji Ulang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
