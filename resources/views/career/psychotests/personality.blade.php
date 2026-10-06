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

        <!-- Instructions Banner -->
        <div class="p-6 rounded-3xl bg-white border border-purple-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0 border border-purple-200">
                💡
            </div>
            <div class="text-xs leading-relaxed">
                <h1 class="text-base font-bold text-slate-900 mb-1">Panduan Pengisian Skala Penilaian Diri (1 s/d 5)</h1>
                <p class="text-slate-600">
                    Pilihlah angka <strong>1 sampai 5</strong> yang paling mencerminkan perilaku dan kebiasaan kerja Anda dalam kehidupan profesional sehari-hari. Warna tombol pilihan akan berubah secara kontras setelah Anda klik.
                </p>
                <!-- Scale Legend -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mt-3 pt-3 border-t border-slate-100 text-[11px] font-semibold">
                    <div class="flex items-center space-x-1.5 text-rose-600"><span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-[10px] border border-rose-300">1</span><span>Sangat Tidak Sesuai</span></div>
                    <div class="flex items-center space-x-1.5 text-amber-600"><span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[10px] border border-amber-300">2</span><span>Tidak Sesuai</span></div>
                    <div class="flex items-center space-x-1.5 text-slate-600"><span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[10px] border border-slate-300">3</span><span>Netral / Ragu-Ragu</span></div>
                    <div class="flex items-center space-x-1.5 text-cyan-600"><span class="w-5 h-5 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-[10px] border border-cyan-300">4</span><span>Sesuai</span></div>
                    <div class="flex items-center space-x-1.5 text-emerald-600"><span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] border border-emerald-300">5</span><span>Sangat Sesuai</span></div>
                </div>
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
                    class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all space-y-4 question-card"
                    data-question-id="{{ $qId }}"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start space-x-3.5">
                            <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block mb-1">
                                    {{ $q['dimension'] ?? 'Aspek Karakter' }}
                                </span>
                                <p class="text-sm sm:text-base font-bold text-slate-900 leading-relaxed">
                                    "{{ $q['statement'] }}"
                                </p>
                            </div>
                        </div>
                        <span id="answered-badge-{{ $qId }}" class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $selectedVal ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            {{ $selectedVal ? '✓ Terjawab' : 'Belum' }}
                        </span>
                    </div>

                    <!-- 1-5 Radio Scale Options Grid with Distinct Clicked vs Unclicked States -->
                    <div class="grid grid-cols-5 gap-2 sm:gap-3 pt-2">
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
                                    1 => 'Sangat Tidak Sesuai',
                                    2 => 'Tidak Sesuai',
                                    3 => 'Netral',
                                    4 => 'Sesuai',
                                    5 => 'Sangat Sesuai',
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
                                <div class="p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 border-2 border-slate-200 text-slate-700 text-center transition-all transform active:scale-95 group-hover:border-slate-300 {{ $activeColorMap[$val] }}">
                                    <div class="text-base sm:text-xl font-black">{{ $val }}</div>
                                    <div class="text-[9px] sm:text-[10px] truncate mt-0.5 font-medium opacity-90">{{ $labelDesc[$val] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Sticky Bottom Submit Section -->
            <div class="sticky bottom-4 z-30 p-4 sm:p-5 rounded-3xl bg-white/95 backdrop-blur-xl border border-slate-200 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 text-center sm:text-left">
                    <span id="footer-status-text">Pastikan semua pernyataan telah dijawab sebelum mengirimkan hasil.</span>
                </div>

                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-xs font-extrabold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-md shadow-purple-600/30 transition-all flex items-center justify-center space-x-2"
                    >
                        <span>✓ Selesaikan & Kirim Hasil Tes Kepribadian</span>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
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
