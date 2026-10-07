<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ujian Tes Kepribadian (Skala 1-5) - {{ $psychotest->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-purple-500 selection:text-white">

    <!-- Top Navigation / Progress Header -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-sm px-4 sm:px-8 py-3.5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('career.psychotests.index', $application->id) }}" class="px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors text-xs font-bold flex items-center space-x-1">
                <span>←</span>
                <span>Kembali</span>
            </a>
            <div class="h-4 w-px bg-slate-200"></div>
            <div>
                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-widest block">Tes Kepribadian & Gambaran Diri</span>
                <span class="text-xs sm:text-sm font-extrabold text-slate-900">{{ $psychotest->title }}</span>
            </div>
        </div>

        <!-- Progress Counter Indicator -->
        <div class="flex items-center space-x-3">
            <div class="text-right">
                <span class="text-[10px] text-slate-400 uppercase font-bold block">Keterisian</span>
                <span class="text-xs font-black text-purple-600" id="progress-text">0 / {{ count($questions) }} Pernyataan</span>
            </div>
            <div class="w-24 sm:w-36 h-2.5 rounded-full bg-slate-200 overflow-hidden shadow-inner">
                <div id="progress-bar" class="h-full bg-gradient-to-r from-purple-600 to-indigo-600 w-0 transition-all duration-300"></div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Stage 1: Panduan & Simulasi Interaktif -->
        <div id="personality-briefing" class="max-w-3xl mx-auto space-y-6">
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200 inline-block mb-1">
                            🧠 TAHAP 1 DARI 2: PANDUAN & SIMULASI
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Panduan & Simulasi Tes Kepribadian (Likert 1-5)</h1>
                        <p class="text-xs text-slate-500 mt-1">Pahami makna skala penilaian dan coba simulasi pengisian sebelum masuk ke lembar ujian asli.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0 border border-purple-200">
                        💡
                    </div>
                </div>

                <!-- Panduan Skala -->
                <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 text-xs text-slate-700 space-y-3">
                    <span class="font-bold text-slate-900 block">📌 Arti Skala Pilihan Penilaian Diri:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-[11px] font-semibold">
                        <div class="p-2 rounded-xl bg-white border border-rose-200 text-rose-700 flex flex-col items-center text-center">
                            <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center font-black text-xs mb-1">1</span>
                            <span>Sangat Tidak Sesuai</span>
                        </div>
                        <div class="p-2 rounded-xl bg-white border border-amber-200 text-amber-700 flex flex-col items-center text-center">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-xs mb-1">2</span>
                            <span>Tidak Sesuai</span>
                        </div>
                        <div class="p-2 rounded-xl bg-white border border-slate-200 text-slate-700 flex flex-col items-center text-center">
                            <span class="w-6 h-6 rounded-full bg-slate-600 text-white flex items-center justify-center font-black text-xs mb-1">3</span>
                            <span>Netral / Ragu</span>
                        </div>
                        <div class="p-2 rounded-xl bg-white border border-blue-200 text-blue-700 flex flex-col items-center text-center">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs mb-1">4</span>
                            <span>Sesuai</span>
                        </div>
                        <div class="p-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 flex flex-col items-center text-center">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-xs mb-1">5</span>
                            <span>Sangat Sesuai</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed pt-1">
                        * Tidak ada jawaban benar atau salah dalam tes ini. Jawablah secara spontan, jujur, dan paling mencerminkan kecenderungan diri Anda saat bekerja.
                    </p>
                </div>

                <!-- Arena Simulasi Latihan -->
                <div class="p-5 sm:p-6 rounded-2xl bg-purple-50/50 border border-purple-200 space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 block">Simulasi Interaktif (Coba 2 Contoh Soal)</span>
                            <p class="text-[11px] text-slate-600">Klik salah satu skala di bawah ini untuk melihat feedback visual tombol pilihan.</p>
                        </div>
                        <span id="sim-likert-badge" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white text-purple-700 border border-purple-200 shrink-0">
                            Simulasi: 0/2
                        </span>
                    </div>

                    <!-- Soal Latihan 1 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">
                                Contoh 1: "Saya selalu merencanakan target harian dan menyusun prioritas sebelum memulai jam kerja."
                            </span>
                            <span id="badge-sim-q1" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200 shrink-0">
                                Belum
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
                            @foreach([1, 2, 3, 4, 5] as $val)
                                <label class="cursor-pointer block text-center">
                                    <input type="radio" name="sim_answers[1]" value="{{ $val }}" onchange="handleSimLikert(1, {{ $val }})" class="peer sr-only sim-radio-1">
                                    <div class="py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-slate-700 font-black text-xs sm:text-sm peer-checked:bg-purple-600 peer-checked:border-purple-700 peer-checked:text-white peer-checked:ring-2 peer-checked:ring-purple-400 transition-all">
                                        {{ $val }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Soal Latihan 2 -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">
                                Contoh 2: "Saya merasa tertantang dan tetap tenang saat harus menyelesaikan masalah darurat."
                            </span>
                            <span id="badge-sim-q2" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200 shrink-0">
                                Belum
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
                            @foreach([1, 2, 3, 4, 5] as $val)
                                <label class="cursor-pointer block text-center">
                                    <input type="radio" name="sim_answers[2]" value="{{ $val }}" onchange="handleSimLikert(2, {{ $val }})" class="peer sr-only sim-radio-2">
                                    <div class="py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-slate-700 font-black text-xs sm:text-sm peer-checked:bg-purple-600 peer-checked:border-purple-700 peer-checked:text-white peer-checked:ring-2 peer-checked:ring-purple-400 transition-all">
                                        {{ $val }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="sim-likert-feedback" class="p-3 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 text-center">
                        Pilih skala di atas untuk mencoba mekanisme pemilihan jawaban.
                    </div>
                </div>

                <!-- CTA Next Button -->
                <div class="space-y-2 pt-2">
                    <button
                        type="button"
                        onclick="startPersonalityExam()"
                        class="w-full py-4 rounded-2xl text-sm font-extrabold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-xl shadow-purple-600/20 transition-all transform active:scale-95 flex items-center justify-center space-x-2"
                    >
                        <span>Lanjut ke Lembar Ujian Sesungguhnya ({{ count($questions) }} Pernyataan)</span>
                        <span>→</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center">
                        ⚠️ Setelah diselesaikan, hasil ujian ini bersifat permanen dan tidak dapat diulang kecuali diizinkan oleh HRD.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stage 2: Lembar Ujian Sesungguhnya (Hidden pada awal sebelum simulasi/lanjut) -->
        <div id="personality-exam-container" class="space-y-6 hidden">
            <!-- Instructions Banner Ringkas -->
            <div class="p-5 rounded-3xl bg-white border border-purple-100 shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0 border border-purple-200">
                    🧠
                </div>
                <div class="text-xs leading-relaxed">
                    <h2 class="text-sm font-bold text-slate-900 mb-0.5">Ujian Kepribadian & Penilaian Diri (50 Pernyataan)</h2>
                    <p class="text-slate-600">
                        Pilihlah angka <strong>1 sampai 5</strong> yang paling mencerminkan diri Anda. Skala: 1 (Sangat Tidak Sesuai) s/d 5 (Sangat Sesuai).
                    </p>
                </div>
            </div>

            <!-- Questions Form -->
            <form id="personality-form" action="{{ route('career.psychotests.submit', [$application->id, $psychotest->id]) }}" method="POST" class="space-y-4">
                @csrf

            @php
                $existingAnswers = $existingResult && is_array($existingResult->answers_submitted)
                    ? ($existingResult->answers_submitted['answers'] ?? [])
                    : [];
            @endphp

            @foreach($questions as $index => $q)
                @php
                    $qId = $q['id'];
                    $selectedVal = $existingAnswers[$qId] ?? null;
                @endphp
                <div
                    id="question-card-{{ $qId }}"
                    class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all space-y-3 sm:space-y-4 question-card"
                    data-question-id="{{ $qId }}"
                >
                    <div class="flex items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                            <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-purple-50 text-purple-700 border border-purple-200 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <span class="text-[9px] sm:text-[10px] font-bold text-purple-600 uppercase tracking-wider block mb-0.5 sm:mb-1">
                                    {{ $q['dimension'] ?? 'Aspek Karakter' }}
                                </span>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-snug sm:leading-relaxed">
                                    "{{ $q['statement'] }}"
                                </p>
                            </div>
                        </div>
                        <span id="answered-badge-{{ $qId }}" class="shrink-0 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-bold {{ $selectedVal ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            {{ $selectedVal ? '✓ Terjawab' : 'Belum' }}
                        </span>
                    </div>

                    <!-- 1-5 Radio Scale Options Grid with Distinct Clicked vs Unclicked States (Mobile Optimized) -->
                    <div class="grid grid-cols-5 gap-1.5 sm:gap-3 pt-1 sm:pt-2">
                        @foreach([1, 2, 3, 4, 5] as $val)
                            @php
                                $activeColorMap = [
                                    1 => 'peer-checked:bg-rose-500 peer-checked:border-rose-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-rose-500/20 peer-checked:shadow-lg',
                                    2 => 'peer-checked:bg-amber-500 peer-checked:border-amber-600 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-amber-500/20 peer-checked:shadow-lg',
                                    3 => 'peer-checked:bg-slate-600 peer-checked:border-slate-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-slate-500/20 peer-checked:shadow-lg',
                                    4 => 'peer-checked:bg-blue-600 peer-checked:border-blue-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-blue-500/20 peer-checked:shadow-lg',
                                    5 => 'peer-checked:bg-emerald-600 peer-checked:border-emerald-700 peer-checked:text-white peer-checked:ring-4 peer-checked:ring-emerald-500/20 peer-checked:shadow-lg',
                                ];
                                $labelDesc = [
                                    1 => 'Sangat Tdk Sesuai',
                                    2 => 'Tdk Sesuai',
                                    3 => 'Netral',
                                    4 => 'Sesuai',
                                    5 => 'Sgt Sesuai',
                                ];
                            @endphp
                            <label class="cursor-pointer group">
                                <input
                                    type="radio"
                                    name="answers[{{ $qId }}]"
                                    value="{{ $val }}"
                                    class="peer sr-only answer-radio"
                                    data-qid="{{ $qId }}"
                                    {{ (string)$selectedVal === (string)$val ? 'checked' : '' }}
                                    required
                                >
                                <div class="p-2 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-slate-700 text-center transition-all transform active:scale-95 group-hover:border-slate-300 {{ $activeColorMap[$val] }}" style="touch-action: manipulation;">
                                    <div class="text-sm sm:text-xl font-black">{{ $val }}</div>
                                    <div class="text-[8px] sm:text-[10px] truncate mt-0.5 font-medium opacity-90">{{ $labelDesc[$val] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Sticky Bottom Submit Section (Mobile Thumb Friendly) -->
            <div class="sticky bottom-3 z-30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl bg-white/95 backdrop-blur-xl border border-slate-200 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    <span id="footer-status-text">Pastikan semua pernyataan telah dijawab sebelum mengirimkan hasil.</span>
                </div>

                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-extrabold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-md shadow-purple-600/30 transition-all flex items-center justify-center space-x-2"
                    >
                        <span>✓ Selesaikan & Kirim Hasil Tes Kepribadian</span>
                    </button>
            </div>
        </form>
    </div>
    </main>

    <script>
        const simAnswers = {};
        function handleSimLikert(qNum, val) {
            simAnswers[qNum] = val;
            const badge = document.getElementById(`badge-sim-q${qNum}`);
            if (badge) {
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200 shrink-0';
                badge.textContent = `✓ Skala ${val}`;
            }
            const count = Object.keys(simAnswers).length;
            const progressBadge = document.getElementById('sim-likert-badge');
            if (progressBadge) {
                progressBadge.textContent = `Simulasi: ${count}/2`;
                if (count === 2) {
                    progressBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300 shrink-0';
                    progressBadge.textContent = '✓ Simulasi Lengkap (2/2)';
                }
            }
            const feedback = document.getElementById('sim-likert-feedback');
            if (feedback) {
                feedback.className = 'p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800 text-center font-medium';
                feedback.innerHTML = `✓ Skala <strong>${val}</strong> berhasil dipilih pada latihan ${qNum}. Klik "Lanjut ke Lembar Ujian Sesungguhnya" untuk memulai tes asli.`;
            }
        }

        function startPersonalityExam() {
            const briefing = document.getElementById('personality-briefing');
            const exam = document.getElementById('personality-exam-container');
            if (briefing) briefing.classList.add('hidden');
            if (exam) exam.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        const totalQuestions = {{ count($questions) }};
        const radios = document.querySelectorAll('.answer-radio');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const footerStatusText = document.getElementById('footer-status-text');

        function updateProgress() {
            const answeredIds = new Set();
            radios.forEach(r => {
                const qId = r.getAttribute('data-qid');
                const badge = document.getElementById(`answered-badge-${qId}`);
                if (r.checked) {
                    answeredIds.add(qId);
                    if (badge) {
                        badge.className = 'shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        badge.textContent = '✓ Terjawab';
                    }
                }
            });

            const count = answeredIds.size;
            const pct = Math.round((count / totalQuestions) * 100);

            progressBar.style.width = `${pct}%`;
            progressText.textContent = `${count} / ${totalQuestions} (${pct}%)`;

            if (count === totalQuestions) {
                footerStatusText.innerHTML = `<span class="text-emerald-600 font-bold">✓ Seluruh ${totalQuestions} pernyataan telah lengkap dijawab!</span>`;
            } else {
                footerStatusText.textContent = `Masih tersisa ${totalQuestions - count} pernyataan yang belum dijawab.`;
            }
        }

        radios.forEach(r => {
            r.addEventListener('change', () => {
                updateProgress();
            });
        });

        // Initialize progress on load
        updateProgress();

        // Validation on form submission
        document.getElementById('personality-form').addEventListener('submit', function (e) {
            const answeredIds = new Set();
            radios.forEach(r => {
                if (r.checked) answeredIds.add(r.getAttribute('data-qid'));
            });

            if (answeredIds.size < totalQuestions) {
                e.preventDefault();
                alert(`Harap lengkapi seluruh ${totalQuestions} pernyataan sebelum mengirimkan hasil.`);

                // Find first unanswered
                const cards = document.querySelectorAll('.question-card');
                for (let card of cards) {
                    const qId = card.getAttribute('data-question-id');
                    if (!answeredIds.has(qId)) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        card.classList.add('border-rose-400', 'bg-rose-50/20');
                        setTimeout(() => card.classList.remove('border-rose-400', 'bg-rose-50/20'), 2500);
                        break;
                    }
                }
            }
        });
    </script>
</body>
</html>
