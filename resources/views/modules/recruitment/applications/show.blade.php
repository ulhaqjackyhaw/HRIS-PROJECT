@extends('layouts.app', ['title' => 'Review 360° Pelamar: ' . $application->applicant_name])

@section('content')
<div class="space-y-6">
    <!-- Top Bar Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('recruitment.applications.index') }}" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors">
            ← Kembali ke Pipeline Seleksi
        </a>

        <div class="flex items-center space-x-2">
            @if($application->employee)
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center space-x-1.5 shadow-xs">
                    <span>✓ Karyawan Aktif Core HR:</span>
                    <a href="{{ route('employees.show', $application->employee->id) }}" class="underline hover:text-emerald-900 font-extrabold">{{ $application->employee->nik }}</a>
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Candidate Profile Header Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center space-x-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md shrink-0 overflow-hidden">
                    @if($profile && $profile->photo_path)
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $application->applicant_name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr($application->applicant_name, 0, 2)) }}
                    @endif
                </div>

                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $application->applicant_name }}</h1>
                        <span class="px-3 py-1 rounded-xl text-xs font-bold {{ $application->current_stage === 'HIRED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($application->current_stage === 'REJECTED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-cyan-50 text-cyan-700 border border-cyan-200') }}">
                            {{ $application->stage_label }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                        <span>Melamar: <strong class="text-slate-900">{{ $application->jobPosting->title ?? '-' }}</strong> ({{ $application->jobPosting->department->name ?? '-' }})</span>
                        <span>•</span>
                        <span>{{ $application->applicant_email }}</span>
                        <span>•</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->applicant_phone) }}" target="_blank" class="text-emerald-700 font-semibold hover:underline">
                            WhatsApp: {{ $application->applicant_phone }} ↗
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Bar -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Ubah Tahapan -->
                <button type="button" onclick="document.getElementById('modal-update-stage').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-colors">
                    🔄 Pindah Tahap
                </button>

                <!-- Jadwalkan Wawancara -->
                <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition-colors">
                    📅 Jadwal Interview
                </button>

                <!-- Kirim Pesan / Broadcast -->
                <button type="button" onclick="document.getElementById('modal-send-message').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-xs">
                    💬 WhatsApp / Email
                </button>

                <!-- Convert to Employee (Core HR Onboarding) -->
                @if(!$application->employee)
                    <button type="button" onclick="document.getElementById('modal-convert-employee').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all flex items-center space-x-1.5">
                        <span>🎉 Convert to Employee</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 8-Stage Selection Stepper Progress -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs overflow-x-auto">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Linimasa Progres Seleksi</h3>
        <div class="flex items-center min-w-[900px] justify-between relative">
            @php
                $activeOrder = $application->stage_order;
            @endphp
            @foreach(\App\Models\JobApplication::STAGES as $stKey => $stVal)
                @if($stKey !== 'REJECTED')
                    @php
                        $isCurrent = $application->current_stage === $stKey;
                        $isPast = $stVal['order'] < $activeOrder && $activeOrder > 0;
                    @endphp
                    <div class="flex flex-col items-center text-center flex-1 relative group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs mb-2 transition-all {{ $isCurrent ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 shadow-xs' : ($isPast ? 'bg-emerald-500 text-white shadow-xs' : 'bg-slate-100 text-slate-400') }}">
                            {{ $isPast ? '✓' : $stVal['order'] }}
                        </div>
                        <span class="text-[11px] font-semibold {{ $isCurrent ? 'text-indigo-600 font-bold' : ($isPast ? 'text-slate-700' : 'text-slate-400') }} line-clamp-1">
                            {{ $stVal['label'] }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Candidate Detailed Dossier (Tabs / Accordions) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: Main Dossier -->
        <div class="lg:col-span-2 space-y-6">
            <!-- I. Berkas CV & Dokumen Digital -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold flex items-center justify-center text-xs">I</span>
                        <h3 class="text-base font-bold text-slate-900">Berkas CV & Dokumen Digital Pelamar</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- CV / Resume -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Berkas CV / Resume</span>
                            <span class="text-[10px] text-slate-500">PDF / Dokumen utama</span>
                        </div>
                        @if($application->resume_path || ($profile && $profile->cv_path))
                            <a href="{{ asset('storage/' . ($application->resume_path ?? $profile->cv_path)) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Unduh / Lihat CV ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Belum ada berkas</span>
                        @endif
                    </div>

                    <!-- Ijazah Terakhir -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Ijazah Terakhir</span>
                            <span class="text-[10px] text-slate-500">Dokumen kelulusan</span>
                        </div>
                        @if($profile && $profile->certificate_path)
                            <a href="{{ asset('storage/' . $profile->certificate_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Ijazah ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>

                    <!-- Transkrip Nilai -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Transkrip Nilai</span>
                            <span class="text-[10px] text-slate-500">Daftar nilai akademik</span>
                        </div>
                        @if($profile && $profile->transcript_path)
                            <a href="{{ asset('storage/' . $profile->transcript_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Transkrip ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>

                    <!-- Foto Formal -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Foto Formal Diri</span>
                            <span class="text-[10px] text-slate-500">Pas foto resmi</span>
                        </div>
                        @if($profile && $profile->photo_path)
                            <a href="{{ asset('storage/' . $profile->photo_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-colors">
                                Lihat Foto ↗
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Tidak dilampirkan</span>
                        @endif
                    </div>
                </div>

                @if($application->cover_letter)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-xs font-bold text-slate-700 block mb-1">Cover Letter / Motivasi Pelamar:</span>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $application->cover_letter }}</p>
                    </div>
                @endif
            </div>

            <!-- II. Data Pribadi & Legalitas -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 font-bold flex items-center justify-center text-xs">II</span>
                    <h3 class="text-base font-bold text-slate-900">Data Pribadi, Domisili & Identitas Legal</h3>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Nama Lengkap</span>
                        <span class="font-bold text-slate-900">{{ $profile->full_name ?? $application->applicant_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-700">{{ $profile->gender ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Tempat & Tgl Lahir</span>
                        <span class="font-semibold text-slate-700">{{ $profile?->birth_place ?? '-' }}, {{ $profile?->birth_date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">No KTP (NIK)</span>
                        <span class="font-semibold text-slate-700">{{ $profile->ktp_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">No NPWP</span>
                        <span class="font-semibold text-slate-700">{{ $profile->npwp_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">BPJS TK / KK</span>
                        <span class="font-semibold text-slate-700">{{ $profile->bpjs_tk_number ?? $profile->family_card_number ?? '-' }}</span>
                    </div>
                    <div class="sm:col-span-3">
                        <span class="text-slate-400 block">Alamat Domisili Sekarang</span>
                        <span class="font-semibold text-slate-700">{{ $profile->domicile_address ?? '-' }}</span>
                    </div>
                    @if($profile && $profile->ktp_address)
                        <div class="sm:col-span-3">
                            <span class="text-slate-400 block">Alamat KTP</span>
                            <span class="font-semibold text-slate-700">{{ $profile->ktp_address }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- III. Riwayat Pengalaman Kerja -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold flex items-center justify-center text-xs">III</span>
                    <h3 class="text-base font-bold text-slate-900">Riwayat Pengalaman Kerja</h3>
                </div>

                @if($profile && !empty($profile->work_experiences))
                    <div class="space-y-3">
                        @foreach($profile->work_experiences as $exp)
                            @if(!empty($exp['company']) || !empty($exp['position_end']))
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $exp['position_end'] ?? '-' }} di {{ $exp['company'] ?? '-' }}</h4>
                                        <span class="text-xs text-slate-500">{{ $exp['period_start'] ?? '' }} - {{ $exp['period_end'] ?? 'Sekarang' }}</span>
                                    </div>
                                    @if(!empty($exp['salary']))
                                        <div class="text-[11px] text-cyan-700 font-semibold mb-1">Gaji Terakhir: Rp {{ number_format((float) $exp['salary'], 0, ',', '.') }}</div>
                                    @endif
                                    @if(!empty($exp['job_desc']))
                                        <p class="text-xs text-slate-600 mt-1 whitespace-pre-line">{{ $exp['job_desc'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3">Tidak ada data riwayat pekerjaan / Fresh Graduate.</div>
                @endif
            </div>

            <!-- IV. Esai Diri & Preferensi Rekrutmen -->
            <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold flex items-center justify-center text-xs">IV</span>
                    <h3 class="text-base font-bold text-slate-900">Esai Diri & Preferensi Rekrutmen</h3>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-1">Kelebihan & Kekurangan Diri:</span>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-line">
                            {{ $profile->strengths_weaknesses ?? 'Belum diisi' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-1">Pencapaian Paling Membanggakan:</span>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-line">
                            {{ $profile->proudest_achievement ?? 'Belum diisi' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Ekspektasi Gaji:</span>
                            <span class="font-bold text-emerald-700">
                                {{ $profile && $profile->expected_salary ? 'Rp ' . number_format((float) $profile->expected_salary, 0, ',', '.') : 'Tidak disebutkan' }}
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-slate-500 block">Estimasi Mulai Kerja:</span>
                            <span class="font-bold text-slate-900">{{ $profile->estimated_start_date ?? 'Segera' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Psychotest Results, Interview & Communications -->
        <div class="space-y-6">
            <!-- Hasil Psikotes Online -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Hasil Ujian Psikotes</h3>
                    <span class="text-xs text-indigo-600 font-semibold">Online Assessment</span>
                </div>

                @forelse($application->psychotestResults as $res)
                    @php
                        $isPassed = $res->result_status === 'PASSED' || $res->total_score >= ($res->psychotest?->passing_score ?? 70);
                        $answers = is_array($res->answers_submitted) ? $res->answers_submitted : [];
                        $ansType = $answers['type'] ?? '';
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50 border {{ $isPassed ? 'border-emerald-300' : 'border-rose-300' }} space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $ansType === 'KRAEPELIN' ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }} inline-block mb-1">
                                    {{ $ansType === 'KRAEPELIN' ? '⚡ KRAEPELIN NUMERIK' : ($ansType === 'LIKERT_PERSONALITY' ? '🧠 KEPRIBADIAN (1-5)' : 'PSIKOMETRI') }}
                                </span>
                                <div class="text-xs font-bold text-slate-900">{{ $res->psychotest->title ?? 'Psikotes Standar' }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isPassed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $isPassed ? '✓ LULUS' : '✗ TIDAK LULUS' }}
                            </span>
                        </div>

                        <div class="flex items-baseline space-x-1.5">
                            <span class="text-2xl font-black {{ $isPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $res->total_score }}</span>
                            <span class="text-xs text-slate-400 font-semibold">/ 100</span>
                        </div>

                        @if($ansType === 'KRAEPELIN')
                            <div class="grid grid-cols-2 gap-1.5 text-[11px] text-slate-600 pt-1 border-t border-slate-200">
                                <div>Akurasi: <strong class="text-emerald-700">{{ $answers['accuracy_rate'] ?? '-' }}%</strong></div>
                                <div>Hitungan: <strong class="text-slate-900">{{ $answers['total_attempted'] ?? '-' }} butir</strong></div>
                                <div>Jawaban Benar: <strong class="text-cyan-700">{{ $answers['correct_count'] ?? '-' }}</strong></div>
                                <div>Kolom Selesai: <strong class="text-slate-900">{{ $answers['columns_completed'] ?? '-' }} kolom</strong></div>
                            </div>
                        @elseif($ansType === 'LIKERT_PERSONALITY')
                            <div class="space-y-1.5 text-[11px] text-slate-600 pt-1 border-t border-slate-200">
                                <div class="flex justify-between">
                                    <span>Skor Rata-rata:</span>
                                    <strong class="text-purple-700">{{ $answers['average_rating'] ?? '-' }} / 5.0</strong>
                                </div>
                                @if(!empty($answers['dimension_scores']) && is_array($answers['dimension_scores']))
                                    <div class="space-y-1 pt-1">
                                        <span class="text-[9px] uppercase tracking-wider text-slate-500 font-bold block">Dimensi Karakter:</span>
                                        @foreach(array_slice($answers['dimension_scores'], 0, 4) as $dim => $dimPct)
                                            <div class="flex justify-between text-[10px]">
                                                <span class="truncate max-w-[140px] text-slate-600">{{ $dim }}</span>
                                                <span class="font-bold text-slate-900">{{ $dimPct }}%</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="text-[10px] text-slate-400 pt-1">
                            Diselesaikan pada: {{ $res->completed_at ? $res->completed_at->format('d M Y, H:i') : '-' }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center border border-dashed border-slate-200 rounded-2xl text-xs text-slate-400">
                        Kandidat belum mengambil asesmen psikotes.
                    </div>
                @endforelse
            </div>

            <!-- Jadwal Wawancara Terjadwal -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Jadwal Wawancara</h3>
                    <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.remove('hidden')" class="text-xs text-indigo-600 font-semibold hover:underline">
                        + Atur Jadwal
                    </button>
                </div>

                @if($application->interview_scheduled_at)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="text-xs font-bold text-indigo-600">{{ $application->stage_label }}</div>
                        <div class="text-sm font-bold text-slate-900">{{ $application->interview_scheduled_at->format('l, d F Y - H:i') }} WIB</div>
                        <div class="text-xs text-slate-600">Lokasi / Link: {{ $application->interview_location }}</div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 py-3 text-center">Belum ada jadwal wawancara aktif.</div>
                @endif
            </div>

            <!-- Log Komunikasi (WhatsApp / Email) -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Komunikasi</h3>
                    <button type="button" onclick="document.getElementById('modal-send-message').classList.remove('hidden')" class="text-xs text-emerald-700 font-semibold hover:underline">
                        + Kirim Pesan
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($application->communications as $comm)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ $comm->channel }}</span>
                                <span class="text-[10px] text-slate-400">{{ $comm->sent_at?->diffForHumans() }}</span>
                            </div>
                            <div class="text-[11px] text-slate-700 font-semibold">{{ $comm->subject }}</div>
                            <p class="text-[11px] text-slate-500 line-clamp-2">{{ $comm->message }}</p>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-3 text-center">Belum ada catatan komunikasi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MODALS (Light Theme with clear borders)
     ========================================== -->

<!-- 1. Modal Update Stage -->
<div id="modal-update-stage" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Pindahkan Tahap Seleksi</h3>
        <p class="text-xs text-slate-500 mb-6">Pilih status tahapan terbaru untuk pelamar {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.stage', $application->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Tahap Baru*</label>
                <select name="stage" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                    @foreach(\App\Models\JobApplication::STAGES as $stK => $stV)
                        <option value="{{ $stK }}" {{ $application->current_stage === $stK ? 'selected' : '' }}>
                            {{ $stV['label'] }} (Order: {{ $stV['order'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Evaluasi HR (Opsional)</label>
                <textarea name="stage_notes" rows="3" placeholder="Catatan hasil diskusi, poin kekuatan kandidat..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">{{ $application->stage_notes }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-update-stage').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                    Simpan Perubahan Tahap
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Schedule Interview -->
<div id="modal-schedule-interview" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Jadwalkan Wawancara</h3>
        <p class="text-xs text-slate-500 mb-6">Atur jadwal temu wawancara kerja untuk {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.interview', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Babak Interview*</label>
                <select name="round" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                    <option value="INTERVIEW_HR">Interview HR (Culture & Basic Competency)</option>
                    <option value="INTERVIEW_USER">Interview User / Hiring Manager</option>
                    <option value="INTERVIEW_BOD">Interview Direksi (BOD / Executive)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu & Tanggal Pertemuan*</label>
                <input type="datetime-local" name="interview_scheduled_at" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi / Tautan Meeting Online (Google Meet / Zoom)*</label>
                <input type="text" name="interview_location" required placeholder="Contoh: https://meet.google.com/xyz atau Ruang Rapat Lt. 4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pewawancara & Catatan Khusus</label>
                <input type="text" name="notes" placeholder="Contoh: Bersama Ibu Sarah (HR Manager)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-schedule-interview').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all">
                    Jadwalkan & Kirim Undangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Send Communication (WhatsApp / Email) -->
<div id="modal-send-message" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Kirim Pesan Komunikasi</h3>
        <p class="text-xs text-slate-500 mb-6">Kirimkan notifikasi resmi via WhatsApp atau Email ke {{ $application->applicant_name }}.</p>

        <form action="{{ route('recruitment.applications.communicate', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Saluran (Channel)*</label>
                    <select name="channel" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="WHATSAPP">WhatsApp ({{ $application->applicant_phone }})</option>
                        <option value="EMAIL">Email ({{ $application->applicant_email }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Template Cepat</label>
                    <select onchange="applyTemplate(this.value)" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="">-- Pilih Template --</option>
                        <option value="shortlist">Undangan Psikotes Online</option>
                        <option value="interview">Undangan Sesi Interview</option>
                        <option value="offering">Pemberitahuan Offering Letter</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Subjek / Judul Pesan*</label>
                <input type="text" id="comm_subject" name="subject" required value="Undangan Seleksi - {{ $application->jobPosting->title }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Isi Pesan*</label>
                <textarea id="comm_message" name="message" rows="5" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-indigo-600">Halo {{ $application->applicant_name }}, terima kasih telah melamar posisi {{ $application->jobPosting->title }} di perusahaan kami.</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-send-message').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition-all">
                    Kirim Pesan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Modal Auto-Onboard to Core HR (Convert to Employee) -->
<div id="modal-convert-employee" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl">
        <div class="flex items-center space-x-3 mb-2">
            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold flex items-center justify-center text-lg">🎉</span>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Convert to Core HR Employee</h3>
                <span class="text-xs text-emerald-700 font-semibold">Auto-Onboarding Karyawan Baru</span>
            </div>
        </div>
        <p class="text-xs text-slate-500 mb-6">
            Kandidat <strong class="text-slate-900">{{ $application->applicant_name }}</strong> akan secara otomatis dibuatkan berkas pegawai aktif di master Core HR dengan seluruh data profil yang telah dilengkapi.
        </p>

        <form action="{{ route('recruitment.applications.convert-employee', $application->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Induk Karyawan (NIK)*</label>
                <input type="text" name="nik" value="EMP-{{ now()->format('Ym') }}-{{ str_pad((string)(\App\Models\Employee::count() + 1), 4, '0', STR_PAD_LEFT) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
                <span class="text-[10px] text-slate-400 mt-0.5 block">Format otomatis dapat disesuaikan bila perlu.</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Departemen*</label>
                    <select name="department_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $dept->id == $application->jobPosting->department_id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Posisi Jabatan*</label>
                    <select name="position_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Ikatan Kerja*</label>
                    <select name="employment_status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600">
                        <option value="PROBATION">Probation (Masa Percobaan)</option>
                        <option value="CONTRACT">Kontrak (PKWT)</option>
                        <option value="PERMANENT">Tetap (PKWTT)</option>
                        <option value="INTERNSHIP">Magang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mulai Bekerja (Join Date)*</label>
                    <input type="date" name="join_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:border-indigo-600" />
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-convert-employee').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md shadow-emerald-600/20 transition-all">
                    ✓ Konversi Resmi ke Core HR
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function applyTemplate(type) {
    const subjectEl = document.getElementById('comm_subject');
    const messageEl = document.getElementById('comm_message');
    const name = "{{ $application->applicant_name }}";
    const job = "{{ $application->jobPosting->title }}";

    if (type === 'shortlist') {
        subjectEl.value = `Undangan Psikotes Online - ${job}`;
        messageEl.value = `Halo ${name},\n\nSelamat! Berkas lamaran Anda untuk posisi ${job} telah berhasil lolos tahap screening. Silakan masuk ke Candidate Portal kami di ${window.location.origin}/career untuk mengikuti asesmen psikotes online.\n\nSalam,\nTim HR Recruitment`;
    } else if (type === 'interview') {
        subjectEl.value = `Undangan Wawancara Kerja - ${job}`;
        messageEl.value = `Halo ${name},\n\nKami mengundang Anda untuk mengikuti tahapan wawancara posisi ${job}. Detail waktu dan link pertemuan tercantum pada Candidate Portal Anda.\n\nSalam,\nTim HR Recruitment`;
    } else if (type === 'offering') {
        subjectEl.value = `Offering Letter & Onboarding - ${job}`;
        messageEl.value = `Halo ${name},\n\nSelamat! Anda terpilih untuk bergabung bersama kami pada posisi ${job}. Kami telah menerbitkan penawaran kerja resmi. Silakan hubungi tim HR untuk konfirmasi jadwal onboarding.\n\nSalam,\nTim HR Recruitment`;
    }
}
</script>
@endsection
