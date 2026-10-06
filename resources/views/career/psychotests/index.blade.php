<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Modul Tes Psikotes Online - HRIS Careers</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <a href="{{ route('career.landing') }}" class="flex items-center space-x-2.5 sm:space-x-3.5 group">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-lg sm:text-xl shadow-md">
                    H
                </div>
                <div>
                    <div class="flex items-center space-x-1.5 sm:space-x-2">
                        <span class="font-extrabold tracking-tight text-base sm:text-lg text-slate-900">HRIS Core</span>
                        <span class="text-[10px] sm:text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">Candidate</span>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium hidden sm:block">Asesmen Psikotes & Psikometri Online</span>
                </div>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-4">
                <a href="{{ route('career.dashboard') }}" class="px-3 sm:px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors flex items-center space-x-1">
                    <span>← <span class="hidden sm:inline">Kembali ke </span>Dashboard</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <!-- Application Context Banner -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                    <span>Tahap Seleksi Psikotes Online</span>
                    <span>•</span>
                    <span>{{ $application->jobPosting?->department?->name ?? 'Departemen' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $application->jobPosting?->title ?? 'Posisi Pekerjaan' }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    Selesaikan instrumen asesmen di bawah ini secara mandiri, jujur, dan fokus. Hasil evaluasi akan secara otomatis terekam ke sistem Applicant Tracking System (ATS) perusahaan.
                </p>
            </div>

            <div class="shrink-0 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-right">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Status Lamaran</span>
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold inline-block bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $application->stage_label }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Psychotest Instruments Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($psychotests as $test)
                @php
                    $result = $results->get($test->id);
                    $isCompleted = !empty($result);
                    $score = $result?->total_score ?? null;
                    $isPassed = $result && ($result->result_status === 'PASSED' || $score >= $test->passing_score);
                    $isKraepelin = $test->isKraepelin();
                    $isLikert = $test->isLikertPersonality();
                @endphp

                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 hover:border-slate-300 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between relative group">
                    <div>
                        <!-- Top Badge & Type -->
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                {{ $isKraepelin ? 'bg-cyan-50 text-cyan-800 border border-cyan-200' : ($isLikert ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200') }}">
                                {{ $isKraepelin ? '⚡ TES KRAEPELIN NUMERIK' : ($isLikert ? '🧠 TES KEPRIBADIAN (LIKERT 1-5)' : '📝 PILIHAN GANDA') }}
                            </span>

                            @if($isCompleted)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                    {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-rose-50 text-rose-700 border border-rose-300' }}">
                                    {{ $isPassed ? '✓ Lulus Asesmen' : '✗ Belum Memenuhi' }} ({{ $score }}/100)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                    ⏳ Belum Dikerjakan
                                </span>
                            @endif
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 mb-2">{{ $test->title }}</h2>
                        <p class="text-xs text-slate-600 leading-relaxed mb-6">
                            {{ $test->description }}
                        </p>

                        <!-- Key Specs -->
                        <div class="grid grid-cols-3 gap-3 py-4 border-y border-slate-100 mb-6 text-center">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Format</span>
                                <span class="text-xs font-black text-slate-800 mt-0.5 block">
                                    {{ $isKraepelin ? 'Deret Hitung 0-9' : ($isLikert ? 'Pernyataan 1-5' : 'Pilihan Ganda') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Estimasi Waktu</span>
                                <span class="text-xs font-black text-cyan-700 mt-0.5 block">
                                    {{ $test->duration_minutes }} Menit
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Passing Grade</span>
                                <span class="text-xs font-black text-indigo-700 mt-0.5 block">
                                    {{ $test->passing_score }}%
                                </span>
                            </div>
                        </div>

                        <!-- Result Insight if Completed -->
                        @if($isCompleted && is_array($result->answers_submitted))
                            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Ringkasan Hasil Anda:</span>
                                @if($isKraepelin)
                                    <div class="grid grid-cols-2 gap-2 text-slate-700 text-[11px]">
                                        <div>Total Hitungan: <strong class="text-slate-900">{{ $result->answers_submitted['total_attempted'] ?? '-' }} butir</strong></div>
                                        <div>Tingkat Akurasi: <strong class="text-emerald-700">{{ $result->answers_submitted['accuracy_rate'] ?? '-' }}%</strong></div>
                                        <div>Jawaban Benar: <strong class="text-slate-900">{{ $result->answers_submitted['correct_count'] ?? '-' }}</strong></div>
                                        <div>Kolom Selesai: <strong class="text-cyan-700">{{ $result->answers_submitted['columns_completed'] ?? '-' }} kolom</strong></div>
                                    </div>
                                    @if(!empty($result->answers_submitted['metrics']))
                                        <div class="grid grid-cols-2 gap-1 text-[10px] text-slate-600 pt-1.5 border-t border-slate-200">
                                            <div>Panker: <strong class="text-slate-900">{{ $result->answers_submitted['metrics']['panker'] ?? '-' }} butir/kolom</strong></div>
                                            <div>Tren: <strong class="text-indigo-700">{{ $result->answers_submitted['metrics']['hanker_trend'] ?? '-' }}</strong></div>
                                        </div>
                                    @endif
                                    <button type="button"
                                            onclick='openCandidateKraepelinModal(@json($result->answers_submitted), "{{ $score }}")'
                                            class="w-full mt-2 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                        <span>📈</span>
                                        <span>Lihat Grafik Kurva Kraepelin Anda</span>
                                    </button>
                                @elseif($isLikert)
                                    <div class="space-y-1 text-slate-700 text-[11px]">
                                        <div class="flex justify-between">
                                            <span>Rata-rata Penilaian Diri:</span>
                                            <strong class="text-purple-700">{{ $result->answers_submitted['average_rating'] ?? '-' }} / 5.0</strong>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Kecocokan Karakter Kerja:</span>
                                            <strong class="text-emerald-700">{{ $score }}%</strong>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-700">
                                        Jawaban benar: <strong class="text-emerald-700">{{ $result->answers_submitted['correct_count'] ?? '-' }}</strong> dari {{ $result->answers_submitted['total_questions'] ?? '-' }} butir soal.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div>
                        <a
                            href="{{ route('career.psychotests.show', [$application->id, $test->id]) }}"
                            class="w-full py-3.5 rounded-2xl text-xs font-extrabold flex items-center justify-center space-x-2 transition-all shadow-md
                            {{ $isCompleted
                                ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300'
                                : ($isKraepelin
                                    ? 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white shadow-blue-600/20'
                                    : ($isLikert
                                        ? 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white shadow-purple-600/20'
                                        : 'bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white shadow-indigo-600/20')) }}"
                        >
                            <span>{{ $isCompleted ? '🔄 Buka / Uji Kembali Instrumen' : '🚀 Mulai Pengerjaan Tes Ini Sekarang' }}</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-white border border-dashed border-slate-300 rounded-3xl">
                    <p class="text-sm text-slate-500">Belum ada modul psikotes yang dijadwalkan untuk posisi ini.</p>
                </div>
            @endforelse
        </div>

        <!-- Rules & Guidelines Card -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 text-xs text-slate-600 space-y-3 shadow-sm">
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                <span>📌</span>
                <span>Petunjuk Penting Pengerjaan Psikotes</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                <li><strong>Tes Kraepelin:</strong> Siapkan konsentrasi penuh. Tes terdiri dari 30 kolom (60 baris angka per kolom) dengan durasi 25 detik per kolom yang berpindah secara otomatis. Anda dapat menggunakan tombol angka pada keyboard (0-9 / Numpad) atau virtual keypad di layar.</li>
                <li><strong>Tes Kepribadian (Skala 1-5):</strong> Bacalah setiap butir pernyataan dan pilih nilai 1 (Sangat Tidak Sesuai) sampai 5 (Sangat Sesuai) yang paling menggambarkan diri Anda yang sebenarnya. Tidak ada jawaban salah; kejujuran dan konsistensi Anda dinilai tinggi.</li>
                <li>Pastikan koneksi internet Anda stabil sebelum menekan tombol mulai.</li>
            </ul>
        </div>
    </main>

    <!-- Modal Kurva Kraepelin (Candidate View) -->
    <div id="candidate-kraepelin-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl space-y-5 my-auto max-h-[92vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 mb-2">
                        <span>⚡ KURVA RITME KERJA KRAEPELIN</span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Hasil Kurva Penjumlahan per Kolom</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Grafik performa kecepatan dan ketahanan kerja Anda sepanjang 30 kolom ujian.</p>
                </div>
                <button type="button" onclick="closeCandidateKraepelinModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-lg font-bold cursor-pointer transition-colors">
                    ✕
                </button>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Panker (Kecepatan)</span>
                    <span class="text-sm font-black text-slate-900 mt-0.5 block" id="cand-modal-panker">-</span>
                    <span class="text-[10px] text-slate-500">Rata-rata hitungan/kolom</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Tianker (Ketelitian)</span>
                    <span class="text-sm font-black text-emerald-600 mt-0.5 block" id="cand-modal-tianker">-</span>
                    <span class="text-[10px] text-slate-500">Akurasi kalkulasi</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Hanker (Ketahanan)</span>
                    <span class="text-sm font-black text-indigo-600 mt-0.5 block" id="cand-modal-hanker">-</span>
                    <span class="text-[10px] text-slate-500">Tren kurva kerja</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Janker (Kestabilan)</span>
                    <span class="text-sm font-black text-amber-600 mt-0.5 block" id="cand-modal-janker">-</span>
                    <span class="text-[10px] text-slate-500">Rentang fluktuasi</span>
                </div>
            </div>

            <!-- SVG Chart Container -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 px-1">
                    <span>Grafik Kurva Kerja Kolom (1 - 30)</span>
                    <span class="text-[11px] text-amber-600 font-semibold" id="cand-modal-avg-label">-</span>
                </div>
                <div id="cand-modal-chart-wrapper" class="w-full bg-white rounded-xl p-3 border border-slate-200/80 overflow-hidden">
                    <!-- SVG injected via JS -->
                </div>
            </div>

            <!-- Column Table -->
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
                        <tbody id="cand-modal-tbody" class="divide-y divide-slate-100 bg-white text-[11px]">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" onclick="closeCandidateKraepelinModal()" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openCandidateKraepelinModal(data, score) {
            const modal = document.getElementById('candidate-kraepelin-modal');
            if (!modal) return;

            const details = data.column_details || [];
            const metrics = data.metrics || {};
            const totalAttempted = data.total_attempted || 0;
            const accuracy = data.accuracy_rate || 0;
            const colCount = Math.max(1, data.columns_completed || details.length);

            const panker = metrics.panker || (totalAttempted / colCount).toFixed(1);
            const tianker = metrics.tianker || accuracy;
            const hanker = metrics.hanker_trend || 'Stabil (Konsisten)';
            const janker = metrics.janker !== undefined ? `±${metrics.janker}` : '-';

            document.getElementById('cand-modal-panker').textContent = `${panker} butir`;
            document.getElementById('cand-modal-tianker').textContent = `${tianker}%`;
            document.getElementById('cand-modal-hanker').textContent = hanker;
            document.getElementById('cand-modal-janker').textContent = janker;
            document.getElementById('cand-modal-avg-label').textContent = `Garis Panker: ${panker} butir/kolom`;

            drawCandidateChart(details, 'cand-modal-chart-wrapper');

            const tbody = document.getElementById('cand-modal-tbody');
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

        function closeCandidateKraepelinModal() {
            const modal = document.getElementById('candidate-kraepelin-modal');
            if (modal) modal.classList.add('hidden');
        }

        function drawCandidateChart(details, wrapperId) {
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
                        <linearGradient id="candKraepelinGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.01" />
                        </linearGradient>
                    </defs>
                    ${gridLinesSvg}
                    <line x1="${padL}" y1="${avgY}" x2="${width - padR}" y2="${avgY}" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4,4" />
                    <text x="${width - padR}" y="${avgY - 4}" fill="#d97706" font-size="9" font-weight="bold" text-anchor="end">Panker (Rata-rata): ${avg.toFixed(1)}</text>
                    <path d="${areaPath}" fill="url(#candKraepelinGrad)" />
                    <path d="${linePath}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    ${pointsSvg}
                    ${xLabelsSvg}
                </svg>
            `;
        }
    </script>
</body>
</html>
