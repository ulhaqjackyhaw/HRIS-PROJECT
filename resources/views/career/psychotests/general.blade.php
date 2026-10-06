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
        <!-- Instructions Banner -->
        <div class="p-6 rounded-3xl bg-white border border-indigo-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl shrink-0 border border-indigo-200">
                📝
            </div>
            <div class="text-xs leading-relaxed">
                <h1 class="text-base font-bold text-slate-900 mb-1">{{ $psychotest->title }}</h1>
                <p class="text-slate-600">
                    {{ $psychotest->description ?? 'Pilihlah satu jawaban yang paling tepat untuk setiap butir pertanyaan logika penalaran di bawah ini.' }}
                </p>
                <div class="flex items-center space-x-4 mt-2 text-[11px] font-semibold text-slate-700">
                    <span>Durasi: <strong class="text-slate-900">{{ $psychotest->duration_minutes }} Menit</strong></span>
                    <span>•</span>
                    <span>Passing Score: <strong class="text-indigo-600">{{ $psychotest->passing_score }}%</strong></span>
                    <span>•</span>
                    <span>Jumlah: <strong class="text-cyan-700">{{ $qCount }} Butir</strong></span>
                </div>
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
            </div>
        </form>
    </main>

    <script>
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
