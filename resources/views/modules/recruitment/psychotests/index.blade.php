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

    <!-- Candidate Results Table Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center space-x-2">
                    <span>📊</span>
                    <span>Riwayat Hasil Tes Psikotes Pelamar</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Hasil ujian dan skor yang diselesaikan kandidat melalui portal karir</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4">Lowongan Kerja</th>
                        <th class="px-6 py-4">Paket Tes</th>
                        <th class="px-6 py-4 text-center">Skor Akhir</th>
                        <th class="px-6 py-4 text-center">Status Hasil</th>
                        <th class="px-6 py-4">Waktu Ujian</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($testResults as $result)
                        @php
                            $app = $result->jobApplication;
                            $score = $result->total_score;
                            $passingScore = $result->psychotest?->passing_score ?? 70;
                            $passed = $result->result_status === 'PASSED' || $score >= $passingScore;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $app?->applicant_name ?? 'Kandidat #'.$result->id }}</div>
                                <div class="text-[11px] text-slate-500">{{ $app?->applicant_email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $app?->jobPosting?->title ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $app?->jobPosting?->department?->name ?? 'Dept. Umum' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-cyan-800">{{ $result->psychotest?->title ?? 'Psikotes Standar' }}</div>
                                @if(is_array($result->answers_submitted))
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        @if(($result->answers_submitted['type'] ?? '') === 'KRAEPELIN')
                                            <span class="text-cyan-700 font-semibold">⚡ Kraepelin:</span> Akurasi {{ $result->answers_submitted['accuracy_rate'] ?? '-' }}% • {{ $result->answers_submitted['total_attempted'] ?? '-' }} hitungan ({{ $result->answers_submitted['columns_completed'] ?? '-' }} kolom)
                                        @elseif(($result->answers_submitted['type'] ?? '') === 'LIKERT_PERSONALITY')
                                            <span class="text-purple-700 font-semibold">🧠 Likert:</span> Rata-rata {{ $result->answers_submitted['average_rating'] ?? '-' }}/5.0 • {{ count($result->answers_submitted['answers'] ?? []) }} butir
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-baseline space-x-1">
                                    <span class="text-base font-extrabold {{ $passed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $score }}</span>
                                    <span class="text-[10px] text-slate-400">/ 100</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($passed)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ LULUS ({{ $result->result_status ?? 'PASSED' }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                        ✗ TIDAK LULUS
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-[11px]">
                                {{ $result->completed_at ? $result->completed_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($app)
                                    <a href="{{ route('recruitment.applications.show', $app->id) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all inline-flex items-center space-x-1 shadow-xs">
                                        <span>Dossier</span>
                                        <span>→</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="text-3xl mb-2">📝</div>
                                <div class="font-semibold text-slate-900">Belum ada kandidat yang menyelesaikan psikotes</div>
                                <p class="text-xs text-slate-400 mt-1">Kandidat yang masuk tahap psikotes akan mengerjakan ujian melalui portal kandidat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($testResults->hasPages())
            <div class="p-4 border-t border-slate-100 bg-white">
                {{ $testResults->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
