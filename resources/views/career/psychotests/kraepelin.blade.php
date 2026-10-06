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
        <div id="test-arena" class="w-full hidden flex flex-col items-center space-y-6">

            <!-- "PINDAH KOLOM" Alert Banner -->
            <div id="column-switch-alert" class="hidden transition-all duration-300 transform scale-105 bg-amber-400 text-slate-900 border-2 border-amber-500 px-8 py-2 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl">
                ⚠️ PINDAH KE KOLOM BERIKUTNYA!
            </div>

            <!-- Columns Display Board -->
            <div class="w-full max-w-4xl bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl overflow-x-auto">
                <div class="flex justify-center space-x-4 sm:space-x-8 min-w-[500px]" id="columns-wrapper">
                    <!-- Columns rendered via JavaScript -->
                </div>
            </div>

            <!-- Virtual Numpad & Keyboard Helper -->
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xl space-y-3">
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-bold px-1">
                    <span>Virtual Keypad (Atau tekan 0-9 di Keyboard)</span>
                    <span class="text-blue-600">Auto-submit</span>
                </div>

                <div class="grid grid-cols-5 gap-2 sm:gap-2.5">
                    @for($n = 1; $n <= 9; $n++)
                        <button
                            type="button"
                            onclick="handleInput({{ $n }})"
                            class="h-14 sm:h-16 rounded-2xl bg-slate-50 hover:bg-blue-600 active:bg-blue-700 text-slate-900 hover:text-white font-black text-xl sm:text-2xl transition-all shadow-sm active:scale-90 border-2 border-slate-200 hover:border-blue-500"
                        >
                            {{ $n }}
                        </button>
                    @endfor
                    <button
                        type="button"
                        onclick="handleInput(0)"
                        class="h-14 sm:h-16 rounded-2xl bg-slate-50 hover:bg-blue-600 active:bg-blue-700 text-slate-900 hover:text-white font-black text-xl sm:text-2xl transition-all shadow-sm active:scale-90 border-2 border-slate-200 hover:border-blue-500 col-span-1"
                    >
                        0
                    </button>
                </div>
            </div>
        </div>

        <!-- Completed Result Modal (Hidden until all columns complete) -->
        <div id="result-modal" class="max-w-lg w-full bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 text-center hidden">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-3xl mx-auto shadow-inner">
                🎉
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Tes Kraepelin Selesai!</h2>
                <p class="text-xs text-slate-500 mt-1">Data kalkulasi dan ritme kerja Anda telah berhasil terekam secara otomatis.</p>
            </div>

            <!-- Score Cards -->
            <div class="grid grid-cols-2 gap-3 text-left">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Total Hitungan</span>
                    <div class="text-2xl font-black text-slate-900 mt-1" id="res-total-attempted">0</div>
                    <span class="text-[10px] text-slate-500">Kecepatan kalkulasi</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Tingkat Akurasi</span>
                    <div class="text-2xl font-black text-emerald-600 mt-1" id="res-accuracy">0%</div>
                    <span class="text-[10px] text-slate-500">Ketelitian kerja</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Jawaban Benar</span>
                    <div class="text-xl font-extrabold text-blue-600 mt-1" id="res-correct">0</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Jawaban Keliru</span>
                    <div class="text-xl font-extrabold text-rose-600 mt-1" id="res-incorrect">0</div>
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
                colDiv.className = `flex flex-col-reverse items-center p-3 rounded-2xl transition-all ${
                    c === currentColumnIdx
                        ? 'bg-blue-50/80 border-2 border-blue-500 shadow-md ring-4 ring-blue-500/10'
                        : (c < currentColumnIdx ? 'opacity-30 border border-slate-200 bg-slate-50' : 'opacity-40 border border-slate-200 bg-slate-50')
                }`;

                // Render digits from bottom to top
                const digits = columnsData[c] || [];
                for (let r = 0; r < digits.length; r++) {
                    const rowDiv = document.createElement('div');
                    rowDiv.id = `digit-${c}-${r}`;
                    rowDiv.className = `w-10 h-9 flex items-center justify-center font-bold text-lg rounded-lg kraepelin-digit transition-all ${
                        c === currentColumnIdx && (r === currentRowIdx || r === currentRowIdx + 1)
                            ? 'bg-blue-600 text-white font-black scale-110 shadow-md'
                            : 'text-slate-400'
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
                    el.className = 'w-10 h-9 flex items-center justify-center font-black text-xl rounded-lg kraepelin-digit transition-all bg-gradient-to-tr from-blue-600 to-indigo-600 text-white scale-110 shadow-md';
                } else if (r < currentRowIdx) {
                    el.className = 'w-10 h-9 flex items-center justify-center font-semibold text-sm rounded-lg kraepelin-digit text-slate-300';
                } else {
                    el.className = 'w-10 h-9 flex items-center justify-center font-bold text-lg rounded-lg kraepelin-digit text-slate-700';
                }
            }

            // Scroll active pair into view if needed
            const activeEl = document.getElementById(`digit-${currentColumnIdx}-${currentRowIdx}`);
            if (activeEl) {
                activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function handleKeyDown(e) {
            if (!isRunning) return;
            if (e.key >= '0' && e.key <= '9') {
                e.preventDefault();
                handleInput(parseInt(e.key, 10));
            }
        }

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
            const isCorrect = (digit === expectedDigit);

            if (isCorrect) {
                correctCount++;
            } else {
                incorrectCount++;
            }

            // Visual feedback
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
                column_index: currentColumnIdx,
                rows_attempted: currentRowIdx,
            });

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

            const accuracy = totalAttempted > 0 ? Math.round((correctCount / totalAttempted) * 100) : 0;
            const expectedBaseline = totalColumns * 12;
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
    </script>
</body>
</html>
