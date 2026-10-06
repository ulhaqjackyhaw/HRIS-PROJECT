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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('career.landing') }}" class="flex items-center space-x-3.5 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-md">
                    H
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold tracking-tight text-lg text-slate-900">HRIS Core</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">Candidate Portal</span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium">Asesmen Psikotes & Psikometri Online</span>
                </div>
            </a>

            <div class="flex items-center space-x-4">
                <a href="{{ route('career.dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors flex items-center space-x-1">
                    <span>← Kembali ke Dashboard</span>
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
                <li><strong>Tes Kraepelin:</strong> Siapkan konsentrasi penuh. Tes terdiri dari beberapa kolom yang berpindah secara otomatis per durasi waktu tertentu. Anda dapat menggunakan tombol angka pada keyboard (0-9 / Numpad) atau virtual keypad di layar.</li>
                <li><strong>Tes Kepribadian (Skala 1-5):</strong> Bacalah setiap butir pernyataan dan pilih nilai 1 (Sangat Tidak Sesuai) sampai 5 (Sangat Sesuai) yang paling menggambarkan diri Anda yang sebenarnya. Tidak ada jawaban salah; kejujuran dan konsistensi Anda dinilai tinggi.</li>
                <li>Pastikan koneksi internet Anda stabil sebelum menekan tombol mulai.</li>
            </ul>
        </div>
    </main>

</body>
</html>
