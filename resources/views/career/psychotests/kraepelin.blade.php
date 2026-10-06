<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ujian Tes Kraepelin - {{ $psychotest->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .kraepelin-digit {
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }
        @keyframes columnPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .active-pair {
            animation: columnPulse 1.2s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 select-none">

    <!-- Header / Status Bar -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-sm px-4 sm:px-8 py-3.5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('career.psychotests.index', $application->id) }}" class="px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors text-xs font-bold flex items-center space-x-1">
                <span>←</span>
                <span>Keluar</span>
            </a>
            <div class="h-4 w-px bg-slate-200"></div>
            <div>
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block">Tes Kraepelin Online</span>
                <span class="text-xs sm:text-sm font-extrabold text-slate-900">{{ $psychotest->title }}</span>
            </div>
        </div>

        <!-- Live Statistics Bar (while running) -->
        <div class="flex items-center space-x-4 sm:space-x-6">
            <div class="text-right">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Kolom Aktif</span>
                <span class="text-sm font-black text-blue-600" id="stat-column-display">Kolom 1 / {{ $columnsCount }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Sisa Waktu Kolom</span>
                <span class="text-base font-black text-amber-600 tabular-nums" id="stat-timer-display">{{ $secondsPerColumn }}s</span>
            </div>
            <div class="text-right hidden sm:block">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Terhitung</span>
                <span class="text-sm font-black text-emerald-600" id="stat-attempted-display">0</span>
            </div>
        </div>
    </header>

    <!-- Main Kraepelin Interactive Arena -->
    <main class="max-w-5xl mx-auto px-4 py-6 flex flex-col items-center justify-center min-h-[calc(100vh-140px)]">

        <!-- Pre-Test Briefing / Start Screen -->
        <div id="briefing-modal" class="max-w-xl w-full bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6 text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mx-auto border border-blue-200 shadow-inner">
                ⚡
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Aturan & Simulasi Tes Kraepelin</h1>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Tes ini mengukur kecepatan, ketahanan ritme kerja, serta ketelitian konsentrasi numerik Anda.
                </p>
            </div>

            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 text-left space-y-2.5 text-xs text-slate-700">
                <div class="flex items-start space-x-2">
                    <span class="text-blue-600 font-bold">1.</span>
                    <span>Jumlahkan <strong>2 angka berdekatan dari bawah ke atas</strong> pada kolom yang sedang aktif.</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-blue-600 font-bold">2.</span>
                    <span>Ketik hanya <strong>digit satuan terakhir</strong> dari hasil penjumlahan:<br/>
                        <span class="text-slate-500 italic">Contoh: 7 + 8 = 15 &rarr; Tekan <strong class="text-blue-600">5</strong> | 3 + 4 = 7 &rarr; Tekan <strong class="text-blue-600">7</strong></span>
                    </span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-blue-600 font-bold">3.</span>
                    <span>Setiap kolom berdurasi <strong>{{ $secondsPerColumn }} detik</strong>. Sistem akan otomatis memindahkan Anda ke kolom berikutnya setelah waktu habis ("PINDAH!").</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-blue-600 font-bold">4.</span>
                    <span>Gunakan tombol angka pada <strong>Keyboard / Numpad</strong> atau <strong>Virtual Keypad</strong> di bawah layar.</span>
                </div>
            </div>

            <button
                type="button"
                id="btn-start-test"
                class="w-full py-4 rounded-2xl text-sm font-extrabold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-xl shadow-blue-600/20 transition-all transform active:scale-95"
            >
                Mulai Tes Kraepelin Sekarang (Fokus Penuh) →
            </button>
        </div>

        <!-- In-Test Container (Hidden before start) -->
        <div id="test-arena" class="w-full hidden flex flex-col items-center space-y-4">

            <!-- "PINDAH KOLOM" Alert Banner -->
            <div id="column-switch-alert" class="hidden transition-all duration-300 transform scale-105 bg-amber-400 text-slate-900 border-2 border-amber-500 px-8 py-2 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl">
                ⚠️ PINDAH KE KOLOM BERIKUTNYA!
            </div>

            <!-- Columns Display Board (Bounded Scroll Camera Viewport) -->
            <div class="w-full max-w-4xl bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xl overflow-x-auto overflow-y-auto max-h-[44vh] sm:max-h-[48vh] relative scroll-smooth" id="columns-board" style="scroll-behavior: smooth;">
                <div class="flex justify-start space-x-3 sm:space-x-4 min-w-max px-4 py-8" id="columns-wrapper">
                    <!-- Columns rendered via JavaScript -->
                </div>
            </div>

            <!-- Virtual Numpad & Keyboard Helper (Mobile Ergonomic Sticky Bottom) -->
            <div class="w-full max-w-md bg-white/95 backdrop-blur-md border border-slate-200 rounded-3xl p-3 sm:p-6 shadow-2xl space-y-2 sm:space-y-3 sticky bottom-2 z-30">
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-bold px-1">
                    <span>Virtual Keypad (Tekan 0-9)</span>
                    <span class="text-blue-600 font-extrabold">Auto-submit</span>
                </div>

                <div class="grid grid-cols-5 gap-1.5 sm:gap-2.5">
                    @for($n = 1; $n <= 9; $n++)
                        <button
                            type="button"
                            onclick="handleInput({{ $n }})"
                            class="h-12 sm:h-16 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-blue-600 active:bg-blue-700 text-slate-900 hover:text-white font-black text-xl sm:text-2xl transition-all shadow-sm active:scale-90 border-2 border-slate-200 hover:border-blue-500 select-none"
                            style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
                        >
                            {{ $n }}
                        </button>
                    @endfor
                    <button
                        type="button"
                        onclick="handleInput(0)"
                        class="h-12 sm:h-16 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-blue-600 active:bg-blue-700 text-slate-900 hover:text-white font-black text-xl sm:text-2xl transition-all shadow-sm active:scale-90 border-2 border-slate-200 hover:border-blue-500 col-span-1 select-none"
                        style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
                    >
                        0
                    </button>
                </div>
            </div>
        </div>

        <!-- Completed Result Modal (Hidden until all columns complete) -->
        <div id="result-modal" class="max-w-3xl w-full bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 text-center hidden">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-3xl mx-auto shadow-inner">
                🎉
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Tes Kraepelin Selesai!</h2>
                <p class="text-xs text-slate-500 mt-1">Data kalkulasi, ritme kerja, dan kurva performa per kolom telah berhasil terekam secara otomatis.</p>
            </div>

            <!-- Score Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Total Hitungan</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5" id="res-total-attempted">0</div>
                    <span class="text-[10px] text-slate-500">Total kalkulasi</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Tingkat Akurasi</span>
                    <div class="text-xl font-black text-emerald-600 mt-0.5" id="res-accuracy">0%</div>
                    <span class="text-[10px] text-slate-500">Ketelitian kerja</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Jawaban Benar</span>
                    <div class="text-xl font-extrabold text-blue-600 mt-0.5" id="res-correct">0</div>
                    <span class="text-[10px] text-slate-500">Kalkulasi tepat</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Jawaban Keliru</span>
                    <div class="text-xl font-extrabold text-rose-600 mt-0.5" id="res-incorrect">0</div>
                    <span class="text-[10px] text-slate-500">Kalkulasi salah</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-900">Skor Akhir Terbobot:</span>
                    <span class="text-2xl font-black text-blue-600" id="res-final-score">0 / 100</span>
                </div>
                <div class="text-[11px] text-slate-600 mt-1" id="res-status-badge">
                    Menghitung hasil kelulusan...
                </div>
            </div>

            <!-- Kurva Kerja Kraepelin Section -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs text-left space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                            <span>📈</span>
                            <span>Kurva Ritme Kerja Kraepelin (Grafik per Kolom)</span>
                        </h3>
                        <p class="text-[11px] text-slate-500">Visualisasi jumlah angka yang berhasil dijumlahkan pada tiap kolom tes.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200" id="res-panker-badge">
                            Panker: -
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" id="res-trend-badge">
                            Tren: -
                        </span>
                    </div>
                </div>

                <!-- SVG Chart Container -->
                <div class="relative w-full bg-slate-50/70 border border-slate-200/80 rounded-xl p-3 overflow-hidden">
                    <div id="kraepelin-chart-wrapper" class="w-full">
                        <!-- SVG chart injected via JavaScript -->
                    </div>
                </div>

                <!-- Psychological Diagnostic Summary Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-center">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Panker (Kecepatan)</span>
                        <span class="text-xs font-black text-slate-800 mt-0.5 block" id="res-metric-panker">-</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Tianker (Ketelitian)</span>
                        <span class="text-xs font-black text-emerald-600 mt-0.5 block" id="res-metric-tianker">-</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Hanker (Ketahanan)</span>
                        <span class="text-xs font-black text-indigo-600 mt-0.5 block" id="res-metric-hanker">-</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Janker (Stabilitas)</span>
                        <span class="text-xs font-black text-amber-600 mt-0.5 block" id="res-metric-janker">-</span>
                    </div>
                </div>

                <!-- Toggle Breakdown Table Button -->
                <div class="pt-1">
                    <button type="button" id="btn-toggle-column-table" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 cursor-pointer">
                        <span>▼ Lihat Tabel Rincian Tiap Kolom</span>
                    </button>
                    <div id="column-details-table-container" class="hidden mt-3 max-h-48 overflow-y-auto border border-slate-200 rounded-xl">
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
                            <tbody id="column-details-tbody" class="divide-y divide-slate-100 bg-white text-[11px]">
                                <!-- Rows injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <a
                href="{{ route('career.psychotests.index', $application->id) }}"
                class="block w-full py-3.5 rounded-2xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all text-center"
            >
                Kembali ke Beranda Asesmen →
            </a>
        </div>
    </main>

    <!-- Hidden Form for CSRF POST Fallback -->
    <form id="kraepelin-submit-form" action="{{ route('career.psychotests.submit', [$application->id, $psychotest->id]) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="total_attempted" id="form-total-attempted">
        <input type="hidden" name="correct_count" id="form-correct-count">
        <input type="hidden" name="incorrect_count" id="form-incorrect-count">
        <input type="hidden" name="columns_completed" id="form-columns-completed">
    </form>

    <script>
        // Test Data from Server
        const columnsData = @json($kraepelinColumns);
        const totalColumns = {{ $columnsCount }};
        const rowsPerColumn = {{ $rowsCount }};
        const secondsPerCol = {{ $secondsPerColumn }};
        const passingScore = {{ $psychotest->passing_score }};
        const submitUrl = "{{ route('career.psychotests.submit', [$application->id, $psychotest->id]) }}";

        let currentColumnIdx = 0;
        let currentRowIdx = 0; // Starts from bottom: 0 is lowest pair
        let totalAttempted = 0;
        let correctCount = 0;
        let incorrectCount = 0;
        let columnDetails = [];
        let colTimeLeft = secondsPerCol;
        let timerInterval = null;
        let isRunning = false;

        document.getElementById('btn-start-test').addEventListener('click', startKraepelinTest);

        function startKraepelinTest() {
            document.getElementById('briefing-modal').classList.add('hidden');
            document.getElementById('test-arena').classList.remove('hidden');

            renderColumns();
            isRunning = true;
            startColumnTimer();

            // Keyboard listener
            window.addEventListener('keydown', handleKeyDown);
        }

        function renderColumns() {
            const wrapper = document.getElementById('columns-wrapper');
            wrapper.innerHTML = '';

            for (let c = 0; c < totalColumns; c++) {
                const colDiv = document.createElement('div');
                colDiv.id = `col-${c}`;
                colDiv.className = `flex flex-col-reverse items-center px-2.5 py-3 rounded-2xl transition-all ${
                    c === currentColumnIdx
                        ? 'bg-blue-50/80 border-2 border-blue-500 shadow-sm'
                        : (c < currentColumnIdx ? 'opacity-30 border border-slate-200 bg-slate-50' : 'opacity-40 border border-slate-200 bg-slate-50')
                }`;

                // Render digits from bottom to top
                const digits = columnsData[c] || [];
                for (let r = 0; r < digits.length; r++) {
                    const rowDiv = document.createElement('div');
                    rowDiv.id = `digit-${c}-${r}`;
                    rowDiv.className = `w-10 h-9 flex items-center justify-center font-bold text-lg rounded-lg kraepelin-digit transition-colors select-none ${
                        c === currentColumnIdx && (r === currentRowIdx || r === currentRowIdx + 1)
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-400 font-medium'
                    }`;
                    rowDiv.textContent = digits[r];
                    colDiv.appendChild(rowDiv);
                }

                // Column Label on Top
                const colLabel = document.createElement('div');
                colLabel.className = 'text-[10px] font-bold text-slate-500 uppercase mt-2';
                colLabel.textContent = `Kolom ${c + 1}`;
                colDiv.appendChild(colLabel);

                wrapper.appendChild(colDiv);
            }

            updateHighlightedPair();
        }

        function updateHighlightedPair() {
            // Update highlights in the active column
            const digits = columnsData[currentColumnIdx] || [];
            for (let r = 0; r < digits.length; r++) {
                const el = document.getElementById(`digit-${currentColumnIdx}-${r}`);
                if (!el) continue;

                if (r === currentRowIdx || r === currentRowIdx + 1) {
                    el.className = 'w-10 h-9 flex items-center justify-center font-bold text-lg rounded-lg kraepelin-digit bg-blue-600 text-white shadow-sm transition-colors select-none';
                } else if (r < currentRowIdx) {
                    el.className = 'w-10 h-9 flex items-center justify-center font-normal text-lg rounded-lg kraepelin-digit text-slate-300 opacity-40 transition-colors select-none';
                } else {
                    el.className = 'w-10 h-9 flex items-center justify-center font-bold text-lg rounded-lg kraepelin-digit text-slate-700 transition-colors select-none';
                }
            }

            focusCameraOnActivePair();
        }

        function focusCameraOnActivePair() {
            const activeEl = document.getElementById(`digit-${currentColumnIdx}-${currentRowIdx}`);
            if (activeEl) {
                // Auto-center camera onto the highlighted numbers vertically & horizontally
                activeEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'center'
                });
            }
        }

        function handleKeyDown(e) {
            if (!isRunning) return;
            if (e.key >= '0' && e.key <= '9') {
                e.preventDefault();
                handleInput(parseInt(e.key, 10));
            }
        }

        let colAttempted = 0;
        let colCorrect = 0;
        let colIncorrect = 0;

        function handleInput(digit) {
            if (!isRunning) return;

            const colDigits = columnsData[currentColumnIdx] || [];
            if (currentRowIdx >= colDigits.length - 1) {
                return; // Reached top of column
            }

            const bottomDigit = colDigits[currentRowIdx];
            const topDigit = colDigits[currentRowIdx + 1];
            const sum = bottomDigit + topDigit;
            const expectedDigit = sum % 10;

            totalAttempted++;
            colAttempted++;
            const isCorrect = (digit === expectedDigit);

            if (isCorrect) {
                correctCount++;
                colCorrect++;
            } else {
                incorrectCount++;
                colIncorrect++;
            }

            // Visual feedback on the answered digit
            const pairEl = document.getElementById(`digit-${currentColumnIdx}-${currentRowIdx}`);
            if (pairEl) {
                if (isCorrect) {
                    pairEl.classList.add('bg-emerald-500', 'text-white');
                } else {
                    pairEl.classList.add('bg-rose-500', 'text-white');
                }
            }

            // Advance to next pair in current column
            currentRowIdx++;
            updateStatsBar();

            if (currentRowIdx >= colDigits.length - 1) {
                nextColumn();
            } else {
                updateHighlightedPair();
            }
        }

        function startColumnTimer() {
            colTimeLeft = secondsPerCol;
            document.getElementById('stat-timer-display').textContent = `${colTimeLeft}s`;

            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                colTimeLeft--;
                document.getElementById('stat-timer-display').textContent = `${colTimeLeft}s`;

                if (colTimeLeft <= 3) {
                    document.getElementById('stat-timer-display').classList.add('text-rose-600');
                } else {
                    document.getElementById('stat-timer-display').classList.remove('text-rose-600');
                }

                if (colTimeLeft <= 0) {
                    nextColumn();
                }
            }, 1000);
        }

        function nextColumn() {
            // Record stats for finished column
            columnDetails.push({
                column: currentColumnIdx + 1,
                column_index: currentColumnIdx,
                attempted: colAttempted,
                correct: colCorrect,
                incorrect: colIncorrect,
            });

            colAttempted = 0;
            colCorrect = 0;
            colIncorrect = 0;

            currentColumnIdx++;

            if (currentColumnIdx >= totalColumns) {
                finishTest();
                return;
            }

            // Flash "PINDAH!" Alert
            const alertEl = document.getElementById('column-switch-alert');
            alertEl.classList.remove('hidden');
            setTimeout(() => {
                alertEl.classList.add('hidden');
            }, 1200);

            currentRowIdx = 0;
            renderColumns();
            updateStatsBar();
            startColumnTimer();
        }

        function updateStatsBar() {
            document.getElementById('stat-column-display').textContent = `Kolom ${currentColumnIdx + 1} / ${totalColumns}`;
            document.getElementById('stat-attempted-display').textContent = totalAttempted;
        }

        function finishTest() {
            isRunning = false;
            if (timerInterval) clearInterval(timerInterval);
            window.removeEventListener('keydown', handleKeyDown);

            // Hide test arena, show result modal
            document.getElementById('test-arena').classList.add('hidden');
            document.getElementById('result-modal').classList.remove('hidden');

            // Complete any remaining columns
            if (columnDetails.length === currentColumnIdx && currentColumnIdx < totalColumns) {
                columnDetails.push({
                    column: currentColumnIdx + 1,
                    column_index: currentColumnIdx,
                    attempted: colAttempted,
                    correct: colCorrect,
                    incorrect: colIncorrect,
                });
            }
            while (columnDetails.length < totalColumns) {
                const idx = columnDetails.length;
                columnDetails.push({
                    column: idx + 1,
                    column_index: idx,
                    attempted: 0,
                    correct: 0,
                    incorrect: 0,
                });
            }

            const accuracy = totalAttempted > 0 ? Math.round((correctCount / totalAttempted) * 100) : 0;
            const expectedBaseline = totalColumns * 15;
            const speedRate = Math.min(100, Math.round((totalAttempted / Math.max(1, expectedBaseline)) * 100));
            const finalScore = Math.min(100, Math.max(0, Math.round((accuracy * 0.6) + (speedRate * 0.4))));
            const isPassed = finalScore >= passingScore;

            document.getElementById('res-total-attempted').textContent = `${totalAttempted} butir`;
            document.getElementById('res-accuracy').textContent = `${accuracy}%`;
            document.getElementById('res-correct').textContent = correctCount;
            document.getElementById('res-incorrect').textContent = incorrectCount;
            document.getElementById('res-final-score').textContent = `${finalScore} / 100`;

            const statusBadge = document.getElementById('res-status-badge');
            if (isPassed) {
                statusBadge.innerHTML = `<span class="text-emerald-600 font-bold">✓ LULUS (Passing score: ${passingScore}%)</span>`;
            } else {
                statusBadge.innerHTML = `<span class="text-rose-600 font-bold">✗ Belum Memenuhi (Passing score: ${passingScore}%)</span>`;
            }

            // Calculate Kraepelin Curve Psychological Metrics
            const panker = (totalAttempted / Math.max(1, totalColumns)).toFixed(1);
            const attemptedList = columnDetails.map(d => d.attempted);
            const peakVal = Math.max(0, ...attemptedList);
            const minVal = Math.min(...attemptedList);
            const janker = Math.max(0, peakVal - minVal);

            const halfCount = Math.floor(columnDetails.length / 2);
            let hankerTrend = 'Stabil (Konsisten)';
            if (halfCount > 0) {
                const firstHalf = attemptedList.slice(0, halfCount);
                const secondHalf = attemptedList.slice(halfCount);
                const firstAvg = firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length;
                const secondAvg = secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length;
                const diffPct = firstAvg > 0 ? ((secondAvg - firstAvg) / firstAvg) * 100 : 0;
                if (diffPct > 5) {
                    hankerTrend = 'Menaik (Daya Tahan Tinggi)';
                } else if (diffPct < -5) {
                    hankerTrend = 'Menurun (Kelelahan Kerja)';
                }
            }

            document.getElementById('res-panker-badge').textContent = `Panker: ${panker} butir/kolom`;
            document.getElementById('res-trend-badge').textContent = `Tren: ${hankerTrend}`;
            document.getElementById('res-metric-panker').textContent = `${panker} butir`;
            document.getElementById('res-metric-tianker').textContent = `${accuracy}%`;
            document.getElementById('res-metric-hanker').textContent = hankerTrend;
            document.getElementById('res-metric-janker').textContent = `±${janker} rentang`;

            // Draw Kraepelin SVG Chart
            drawKraepelinChart(columnDetails);

            // Populate Breakdown Table
            const tbody = document.getElementById('column-details-tbody');
            if (tbody) {
                tbody.innerHTML = columnDetails.map(d => {
                    const acc = d.attempted > 0 ? Math.round((d.correct / d.attempted) * 100) : 0;
                    return `<tr>
                        <td class="px-3 py-1.5 font-bold text-slate-800">Kolom ${d.column}</td>
                        <td class="px-3 py-1.5 text-center font-bold text-blue-600">${d.attempted}</td>
                        <td class="px-3 py-1.5 text-center text-emerald-600 font-semibold">${d.correct}</td>
                        <td class="px-3 py-1.5 text-center text-rose-600 font-semibold">${d.incorrect}</td>
                        <td class="px-3 py-1.5 text-right font-bold text-slate-700">${acc}%</td>
                    </tr>`;
                }).join('');
            }

            // Send payload via Fetch API
            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    total_attempted: totalAttempted,
                    correct_count: correctCount,
                    incorrect_count: incorrectCount,
                    columns_completed: totalColumns,
                    column_details: columnDetails
                })
            }).catch(err => {
                console.error("Auto submit error, falling back to form submit", err);
                document.getElementById('form-total-attempted').value = totalAttempted;
                document.getElementById('form-correct-count').value = correctCount;
                document.getElementById('form-incorrect-count').value = incorrectCount;
                document.getElementById('form-columns-completed').value = totalColumns;
                document.getElementById('kraepelin-submit-form').submit();
            });
        }

        // Draw Interactive Kraepelin SVG Curve Chart
        function drawKraepelinChart(details) {
            const wrapper = document.getElementById('kraepelin-chart-wrapper');
            if (!wrapper || !details || details.length === 0) return;

            const n = details.length;
            const maxVal = Math.max(15, ...details.map(d => d.attempted)) + 3;
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
                const x = padL + (i / Math.max(1, n - 1)) * chartW;
                const y = padT + chartH - ((d.attempted - minVal) / (maxVal - minVal)) * chartH;
                return {
                    x: Math.round(x * 10) / 10,
                    y: Math.round(y * 10) / 10,
                    col: d.column,
                    attempted: d.attempted,
                    correct: d.correct,
                    incorrect: d.incorrect
                };
            });

            const avg = details.reduce((s, d) => s + d.attempted, 0) / Math.max(1, n);
            const avgY = Math.round((padT + chartH - ((avg - minVal) / (maxVal - minVal)) * chartH) * 10) / 10;

            const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
            const areaPath = `${linePath} L ${points[points.length - 1].x} ${padT + chartH} L ${points[0].x} ${padT + chartH} Z`;

            // Horizontal grid lines
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

            // Circles with hover tooltips
            let pointsSvg = '';
            points.forEach((p) => {
                pointsSvg += `
                    <g class="cursor-pointer">
                        <circle cx="${p.x}" cy="${p.y}" r="3" fill="#2563eb" stroke="#ffffff" stroke-width="1.5" />
                        <title>Kolom ${p.col}: ${p.attempted} hitungan (${p.correct} benar, ${p.incorrect} salah)</title>
                    </g>
                `;
            });

            // X-Axis column markers
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
                        <linearGradient id="kraepelinGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.01" />
                        </linearGradient>
                    </defs>
                    ${gridLinesSvg}
                    <line x1="${padL}" y1="${avgY}" x2="${width - padR}" y2="${avgY}" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4,4" />
                    <text x="${width - padR}" y="${avgY - 4}" fill="#d97706" font-size="9" font-weight="bold" text-anchor="end">Panker (Rata-rata): ${avg.toFixed(1)}</text>
                    <path d="${areaPath}" fill="url(#kraepelinGradient)" />
                    <path d="${linePath}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    ${pointsSvg}
                    ${xLabelsSvg}
                </svg>
            `;
        }

        document.getElementById('btn-toggle-column-table')?.addEventListener('click', function() {
            const tableContainer = document.getElementById('column-details-table-container');
            if (tableContainer) {
                tableContainer.classList.toggle('hidden');
            }
        });
    </script>
</body>
</html>
