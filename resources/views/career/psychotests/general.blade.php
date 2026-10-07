<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ujian Psikotes Online - {{ $psychotest->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 relative selection:bg-indigo-500 selection:text-white">

    <!-- Header / Status Bar -->
    <header class="sticky top-0 z-40 backdrop-blur-xl bg-white/90 border-b border-slate-200/90 shadow-sm px-3.5 sm:px-8 py-2.5 sm:py-3.5 flex items-center justify-between">
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            <a href="{{ route('career.psychotests.index', $application->id) }}" class="px-2.5 sm:px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors text-xs font-bold flex items-center space-x-1 shrink-0">
                <span>←</span>
                <span class="hidden xs:inline sm:inline">Kembali</span>
            </a>
            <div class="h-4 w-px bg-slate-200 shrink-0"></div>
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-indigo-600 uppercase tracking-widest block truncate">Asesmen Pilihan Ganda</span>
                <span class="text-xs sm:text-sm font-extrabold text-slate-900 truncate block">{{ $psychotest->title }}</span>
            </div>
        </div>

        @php
            $questions = is_array($psychotest->questions_data) ? $psychotest->questions_data : [];
            $qCount = count($questions);
        @endphp

        <!-- Progress Counter Indicator -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 shrink-0">
            <div class="text-right">
                <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase font-bold block">Keterisian</span>
                <span class="text-[11px] sm:text-xs font-black text-indigo-600" id="progress-text">0 / {{ $qCount }} Soal</span>
            </div>
            <div class="w-16 sm:w-36 h-2 sm:h-2.5 rounded-full bg-slate-200 overflow-hidden shadow-inner">
                <div id="progress-bar" class="h-full bg-gradient-to-r from-indigo-600 to-cyan-600 w-0 transition-all duration-300"></div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Stage 1: Panduan & Simulasi Interaktif -->
        <div id="general-briefing" class="max-w-3xl mx-auto space-y-6">
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 inline-block mb-1">
                            📝 TAHAP 1 DARI 2: PANDUAN & SIMULASI
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Panduan & Simulasi Tes Logika Penalaran</h1>
                        <p class="text-xs text-slate-500 mt-1">Pahami format pilihan ganda dan coba simulasi latihan soal sebelum memasuki ujian asli.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl shrink-0 border border-indigo-200">
                        📝
                    </div>
                </div>

                <!-- Informasi & Ketentuan Tes -->
                <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 text-xs text-slate-700 space-y-3">
                    <span class="font-bold text-slate-900 block">📌 Ketentuan Pelaksanaan Ujian:</span>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Jumlah Soal</span>
                            <span class="text-sm font-black text-slate-900 mt-0.5 block">{{ $qCount }} Butir</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Estimasi Waktu</span>
                            <span class="text-sm font-black text-cyan-700 mt-0.5 block">{{ $psychotest->duration_minutes }} Menit</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Passing Grade</span>
                            <span class="text-sm font-black text-indigo-600 mt-0.5 block">{{ $psychotest->passing_score }}%</span>
                        </div>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600 pt-1 leading-relaxed">
                        <li>Pilihlah <strong>satu jawaban yang paling tepat</strong> untuk setiap butir soal.</li>
                        <li>Pastikan seluruh butir terjawab sebelum mengirimkan formulir ujian.</li>
                        <li>Hasil ujian akan langsung dihitung secara otomatis dan terekam ke sistem seleksi ATS.</li>
                    </ul>
                </div>

                <!-- Arena Simulasi Latihan -->
                <div class="p-5 sm:p-6 rounded-2xl bg-indigo-50/50 border border-indigo-200 space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-700 block">Simulasi Interaktif (Coba 2 Contoh Soal)</span>
                            <p class="text-[11px] text-slate-600">Pilih salah satu opsi jawaban untuk melihat feedback interaktif tombol.</p>
                        </div>
                        <span id="sim-general-badge" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white text-indigo-700 border border-indigo-200 shrink-0">
                            Simulasi: 0/2
                        </span>
                    </div>

                    <!-- Latihan 1: Silogisme Logika -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">
                                Contoh 1: "Semua staf IT menguasai pemecahan masalah teknis. Bagas adalah seorang staf IT. Kesimpulannya adalah..."
                            </span>
                            <span id="badge-sim-gen-1" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200 shrink-0">
                                Belum
                            </span>
                        </div>
                        <div class="space-y-2">
                            @php
                                $simOpts1 = [
                                    'A' => 'Bagas tidak menguasai masalah teknis',
                                    'B' => 'Bagas menguasai pemecahan masalah teknis',
                                    'C' => 'Semua orang selain Bagas tidak menguasai masalah teknis',
                                    'D' => 'Tidak dapat disimpulkan',
                                ];
                            @endphp
                            @foreach($simOpts1 as $key => $val)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="sim_gen[1]" value="{{ $key }}" onchange="handleSimGeneral(1, '{{ $key }}', 'B')" class="peer sr-only">
                                    <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 transition-all flex items-center space-x-2.5 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/90 peer-checked:ring-2 peer-checked:ring-indigo-500/20 peer-checked:[&_.opt-key]:bg-indigo-600 peer-checked:[&_.opt-key]:text-white peer-checked:[&_.opt-text]:text-indigo-950 peer-checked:[&_.opt-text]:font-bold" style="touch-action: manipulation;">
                                        <span class="opt-key w-6 h-6 rounded-lg bg-white border border-slate-300 text-slate-700 font-black text-xs flex items-center justify-center shrink-0">
                                            {{ $key }}
                                        </span>
                                        <span class="opt-text text-xs text-slate-700 font-medium">
                                            {{ $val }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Latihan 2: Deret Angka Numerik -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">
                                Contoh 2: "Perhatikan pola deret: 3, 6, 12, 24, [ ... ]. Angka selanjutnya adalah:"
                            </span>
                            <span id="badge-sim-gen-2" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 border border-slate-200 shrink-0">
                                Belum
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @php
                                $simOpts2 = ['A' => '36', 'B' => '42', 'C' => '48', 'D' => '52'];
                            @endphp
                            @foreach($simOpts2 as $key => $val)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="sim_gen[2]" value="{{ $key }}" onchange="handleSimGeneral(2, '{{ $key }}', 'C')" class="peer sr-only">
                                    <div class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-center transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50/90 peer-checked:ring-2 peer-checked:ring-indigo-500/20 peer-checked:[&_.opt-val]:text-indigo-950 peer-checked:[&_.opt-val]:font-bold" style="touch-action: manipulation;">
                                        <span class="text-[10px] text-slate-400 font-bold block">{{ $key }}</span>
                                        <span class="opt-val text-sm font-black text-slate-800">{{ $val }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="sim-gen-feedback" class="p-3 rounded-xl bg-white border border-slate-200 text-[11px] text-slate-600 text-center">
                        Pilih jawaban pada latihan di atas untuk mencoba mekanisme ujian.
                    </div>
                </div>

                <!-- CTA Next Button -->
                <div class="space-y-2 pt-2">
                    <button
                        type="button"
                        onclick="startGeneralExam()"
                        class="w-full py-4 rounded-2xl text-sm font-extrabold text-white bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-700 hover:to-cyan-700 shadow-xl shadow-indigo-600/20 transition-all transform active:scale-95 flex items-center justify-center space-x-2"
                    >
                        <span>Lanjut ke Lembar Ujian Sesungguhnya ({{ $qCount }} Butir Soal)</span>
                        <span>→</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center">
                        ⚠️ Setelah diselesaikan, hasil ujian ini bersifat permanen dan tidak dapat diulang kecuali diizinkan oleh HRD.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stage 2: Lembar Ujian Sesungguhnya (Hidden pada awal sebelum simulasi/lanjut) -->
        <div id="general-exam-container" class="space-y-6 hidden">
            <!-- Instructions Banner Ringkas -->
            <div class="p-5 rounded-3xl bg-white border border-indigo-100 shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0 border border-indigo-200">
                    📝
                </div>
                <div class="text-xs leading-relaxed">
                    <h2 class="text-sm font-bold text-slate-900 mb-0.5">{{ $psychotest->title }} ({{ $qCount }} Soal)</h2>
                    <p class="text-slate-600">
                        Pilihlah satu jawaban yang paling tepat. Nilai kelulusan: <strong>{{ $psychotest->passing_score }}%</strong>.
                    </p>
                </div>
            </div>

            <!-- Form Questions -->
            <form id="general-form" action="{{ route('career.psychotests.submit', [$application->id, $psychotest->id]) }}" method="POST" class="space-y-4">
                @csrf

            @php
                $existingAnswers = $existingResult && is_array($existingResult->answers_submitted)
                    ? ($existingResult->answers_submitted['answers'] ?? [])
                    : [];
            @endphp

            @foreach($questions as $index => $q)
                @php
                    $qId = $q['id'] ?? ($index + 1);
                    $options = $q['options'] ?? [];
                    $selectedVal = $existingAnswers[$qId] ?? null;
                @endphp
                <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all space-y-3 sm:space-y-4 question-card" data-question-id="{{ $qId }}">
                    <div class="flex items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                            <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-snug sm:leading-relaxed">
                                    {{ $q['question'] ?? 'Pertanyaan' }}
                                </p>
                            </div>
                        </div>
                        <span id="answered-badge-{{ $qId }}" class="shrink-0 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-bold {{ $selectedVal ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            {{ $selectedVal ? '✓ Terjawab' : 'Belum' }}
                        </span>
                    </div>

                    <!-- Multiple Choice Radio Options with Distinct Selection Colors -->
                    <div class="space-y-2 pt-1 sm:pt-2">
                        @foreach($options as $optKey => $optText)
                            <label class="cursor-pointer block group">
                                <input
                                    type="radio"
                                    name="answers[{{ $qId }}]"
                                    value="{{ $optKey }}"
                                    class="peer sr-only answer-radio"
                                    data-qid="{{ $qId }}"
                                    {{ (string)$selectedVal === (string)$optKey ? 'checked' : '' }}
                                    required
                                >
                                <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 hover:border-slate-300 transition-all flex items-center space-x-3 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/90 peer-checked:ring-4 peer-checked:ring-indigo-500/20 peer-checked:shadow-sm peer-checked:[&_.opt-key]:bg-indigo-600 peer-checked:[&_.opt-key]:border-indigo-600 peer-checked:[&_.opt-key]:text-white peer-checked:[&_.opt-text]:text-indigo-950 peer-checked:[&_.opt-text]:font-bold" style="touch-action: manipulation;">
                                    <span class="opt-key w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-white border-2 border-slate-300 text-slate-700 font-black text-xs flex items-center justify-center shrink-0 transition-colors shadow-xs">
                                        {{ $optKey }}
                                    </span>
                                    <span class="opt-text text-xs sm:text-sm text-slate-700 font-medium transition-colors">
                                        {{ $optText }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Submit Section (Mobile Thumb Friendly) -->
            <div class="sticky bottom-3 z-30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl bg-white/95 backdrop-blur-xl border border-slate-200 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    <span id="footer-status-text">Pastikan semua soal telah dijawab sebelum mengirimkan jawaban.</span>
                </div>

                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl sm:rounded-2xl text-xs sm:text-sm font-extrabold text-white bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-700 hover:to-cyan-700 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2"
                    >
                        <span>✓ Selesaikan & Kirim Jawaban</span>
                    </button>
            </div>
        </form>
    </div>
    </main>

    <script>
        const simGenAnswers = {};
        function handleSimGeneral(qNum, selectedKey, correctKey) {
            simGenAnswers[qNum] = selectedKey;
            const badge = document.getElementById(`badge-sim-gen-${qNum}`);
            if (badge) {
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0';
                badge.textContent = `✓ Opsi ${selectedKey}`;
            }

            const count = Object.keys(simGenAnswers).length;
            const progressBadge = document.getElementById('sim-general-badge');
            if (progressBadge) {
                progressBadge.textContent = `Simulasi: ${count}/2`;
                if (count === 2) {
                    progressBadge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-300 shrink-0';
                    progressBadge.textContent = '✓ Simulasi Lengkap (2/2)';
                }
            }

            const feedback = document.getElementById('sim-gen-feedback');
            if (feedback) {
                if (selectedKey === correctKey) {
                    feedback.className = 'p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-800 text-center font-medium';
                    feedback.innerHTML = `✓ Tepat Sekali! Pilihan <strong>${selectedKey}</strong> adalah kunci jawaban yang benar untuk contoh ${qNum}.`;
                } else {
                    feedback.className = 'p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 text-center font-medium';
                    feedback.innerHTML = `Opsi <strong>${selectedKey}</strong> dipilih pada latihan ${qNum}. Kunci yang paling tepat adalah <strong>${correctKey}</strong>. Anda dapat mencoba mengubah opsi.`;
                }
            }
        }

        function startGeneralExam() {
            const briefing = document.getElementById('general-briefing');
            const exam = document.getElementById('general-exam-container');
            if (briefing) briefing.classList.add('hidden');
            if (exam) exam.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        const totalQuestions = {{ $qCount }};
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
            const pct = totalQuestions > 0 ? Math.round((count / totalQuestions) * 100) : 0;

            progressBar.style.width = `${pct}%`;
            progressText.textContent = `${count} / ${totalQuestions} (${pct}%)`;

            if (count === totalQuestions && totalQuestions > 0) {
                footerStatusText.innerHTML = `<span class="text-emerald-600 font-bold">✓ Seluruh ${totalQuestions} soal telah dijawab!</span>`;
            } else {
                footerStatusText.textContent = `Masih tersisa ${totalQuestions - count} soal yang belum dijawab.`;
            }
        }

        radios.forEach(r => {
            r.addEventListener('change', updateProgress);
        });

        updateProgress();
    </script>
</body>
</html>
