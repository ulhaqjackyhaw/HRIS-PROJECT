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
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-cyan-700 font-semibold">⚡ Kraepelin:</span>
                                                <span>Akurasi {{ $result->answers_submitted['accuracy_rate'] ?? '-' }}% • {{ $result->answers_submitted['total_attempted'] ?? '-' }} hitungan ({{ $result->answers_submitted['columns_completed'] ?? '-' }} kolom)</span>
                                                <button type="button"
                                                        onclick='openKraepelinModal(@json($result->answers_submitted), "{{ addslashes($app?->applicant_name ?? 'Kandidat') }}", "{{ $result->total_score }}", "{{ $result->result_status }}")'
                                                        class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all inline-flex items-center gap-1 cursor-pointer">
                                                    <span>📈 Lihat Kurva</span>
                                                </button>
                                            </div>
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
                <span>Grafik Kurva Kerja Kolom (1 - 40)</span>
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
</script>
@endsection
