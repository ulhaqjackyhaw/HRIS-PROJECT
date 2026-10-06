<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\CandidatePsychotestResult;
use App\Models\Department;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Psychotest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CareerController extends Controller
{
    /**
     * Display the Career Portal Landing Page with job search and filters.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->input('search');
        $departmentId = $request->input('department_id');
        $employmentType = $request->input('employment_type');
        $workModel = $request->input('work_model');
        $location = $request->input('location');

        $query = JobPosting::with('department')
            ->published()
            ->filter([
                'search' => $search,
                'department_id' => $departmentId,
                'employment_type' => $employmentType,
                'work_model' => $workModel,
                'location' => $location,
            ])
            ->latest();

        $jobs = $query->paginate(9)->withQueryString();

        // Data statistik & agregasi departemen
        $departments = Department::whereHas('jobPostings', function ($q) {
            $q->published();
        })->withCount(['jobPostings' => function ($q) {
            $q->published();
        }])->get();

        $stats = [
            'total_openings' => JobPosting::published()->count(),
            'total_departments' => $departments->count(),
            'remote_friendly_count' => JobPosting::published()->whereIn('work_model', ['REMOTE', 'HYBRID'])->count(),
            'total_hires_this_year' => 24, // Enterprise metric
            'satisfaction_rate' => 98,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'jobs' => $jobs,
                'departments' => $departments,
                'stats' => $stats,
            ]);
        }

        return view('career.landing', compact(
            'jobs',
            'departments',
            'stats',
            'search',
            'departmentId',
            'employmentType',
            'workModel',
            'location'
        ));
    }

    /**
     * Display the Job Detail page.
     */
    public function show(string $slug): View|JsonResponse
    {
        $job = JobPosting::with(['department'])
            ->where('slug', $slug)
            ->firstOrFail();

        $job->increment('views_count');

        $relatedJobs = JobPosting::published()
            ->where('id', '!=', $job->id)
            ->where('department_id', $job->department_id)
            ->take(3)
            ->get();

        $profile = auth()->user()?->candidateProfile;

        if (request()->wantsJson()) {
            return response()->json([
                'job' => $job,
                'relatedJobs' => $relatedJobs,
                'profile' => $profile,
            ]);
        }

        return view('career.job-detail', compact('job', 'relatedJobs', 'profile'));
    }

    /**
     * Show Candidate Login Page.
     */
    public function loginForm(): View
    {
        return view('career.auth.login');
    }

    /**
     * Handle Candidate Login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($jobSlug = $request->input('redirect_job')) {
                return redirect()->route('career.jobs.show', $jobSlug);
            }

            return redirect()->route('career.dashboard')
                ->with('success', 'Selamat datang kembali di Candidate Portal!');
        }

        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Show Candidate Registration Page.
     */
    public function registerForm(): View
    {
        return view('career.auth.register');
    }

    /**
     * Handle Candidate Registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'CANDIDATE',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($jobSlug = $request->input('redirect_job')) {
            return redirect()->route('career.jobs.show', $jobSlug)
                ->with('success', 'Akun berhasil dibuat! Silakan lanjutkan proses melamar pekerjaan.');
        }

        return redirect()->route('career.dashboard')
            ->with('success', 'Selamat datang di Candidate Portal! Akun Anda telah berhasil dibuat.');
    }

    /**
     * Handle Candidate Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('career.landing')
            ->with('success', 'Anda telah berhasil keluar dari Candidate Portal.');
    }

    /**
     * Display Candidate Dashboard (Timeline Tracker & Profile).
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $applications = JobApplication::with(['jobPosting.department', 'psychotestResults'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('career.dashboard', compact('user', 'applications'));
    }

    /**
     * Handle Job Application Submission.
     */
    public function apply(Request $request, string $slug): RedirectResponse
    {
        $job = JobPosting::where('slug', $slug)->firstOrFail();
        $user = $request->user();

        // 1. Cek duplikasi lamaran
        $existing = JobApplication::where('job_posting_id', $job->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('career.dashboard')
                ->with('info', "Anda sudah pernah mengirimkan berkas lamaran untuk posisi {$job->title}.");
        }

        // 2. Cek kelengkapan data profil inti
        $profile = $user->candidateProfile;
        if (! $profile || ! $profile->is_completed) {
            return redirect()->route('career.profile', ['job' => $slug])
                ->with('info', 'Silakan lengkapi data profil inti Anda terlebih dahulu sebelum mengirimkan lamaran.');
        }

        // 3. Langsung kirim lamaran (1-Click Apply menggunakan data profil yang telah tersimpan)
        JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'applicant_name' => $profile->full_name ?? $user->name,
            'applicant_email' => $profile->email ?? $user->email,
            'applicant_phone' => $profile->phone_wa ?? $user->phone ?? '-',
            'resume_path' => $profile->cv_path ?? $user->resume_path,
            'portfolio_url' => $profile->emergency_contact['portfolio_url'] ?? null,
            'linkedin_url' => $profile->emergency_contact['linkedin_url'] ?? null,
            'cover_letter' => $request->input('cover_letter'),
            'current_stage' => 'APPLIED',
            'applied_at' => now(),
        ]);

        return redirect()->route('career.dashboard')
            ->with('success', "Lamaran untuk posisi {$job->title} berhasil dikirim! Silakan pantau tahapan seleksi Anda.");
    }

    /**
     * Show Candidate Complete Profile / CV Form.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $profile = $user->candidateProfile ?? new CandidateProfile([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'phone_wa' => $user->phone,
        ]);

        return view('career.profile', compact('user', 'profile'));
    }

    /**
     * Update Candidate Complete Profile / CV.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = CandidateProfile::firstOrNew(['user_id' => $user->id]);

        // Validasi: Hanya data inti yang wajib (*), sisanya opsional
        $request->validate([
            // Data Inti Wajib
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string'],
            'phone_wa' => ['required', 'string', 'max:30'],
            'domicile_address' => ['required', 'string'],
            'agreement_signed' => ['required'],

            // Berkas CV Wajib jika belum ada berkas sebelumnya
            'cv' => [
                ($profile->cv_path || $user->resume_path) ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:2048',
            ],

            // Data Opsional (boleh kosong)
            'nickname' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'string'],
            'religion' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
            'ethnicity' => ['nullable', 'string'],
            'height_cm' => ['nullable', 'numeric'],
            'weight_kg' => ['nullable', 'numeric'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'transcript' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'ktp_address' => ['nullable', 'string'],
        ]);

        // File uploads
        if ($request->hasFile('photo')) {
            $profile->photo_path = $request->file('photo')->store('candidate-photos', 'public');
        }
        if ($request->hasFile('cv')) {
            $profile->cv_path = $request->file('cv')->store('candidate-cvs', 'public');
            $user->update(['resume_path' => $profile->cv_path]);
        }
        if ($request->hasFile('certificate')) {
            $profile->certificate_path = $request->file('certificate')->store('candidate-certificates', 'public');
        }
        if ($request->hasFile('transcript')) {
            $profile->transcript_path = $request->file('transcript')->store('candidate-transcripts', 'public');
        }

        // Section I: Data Diri
        $profile->full_name = $request->input('full_name');
        $profile->nickname = $request->input('nickname');
        $profile->gender = $request->input('gender');
        $profile->marital_status = $request->input('marital_status');
        $profile->birth_date = $request->input('birth_date');
        $profile->birth_place = $request->input('birth_place');
        $profile->religion = $request->input('religion');
        $profile->nationality = $request->input('nationality', 'Indonesia');
        $profile->ethnicity = $request->input('ethnicity');
        $profile->height_cm = $request->input('height_cm');
        $profile->weight_kg = $request->input('weight_kg');
        $profile->clothing_size = $request->input('clothing_size');
        $profile->shoe_size = $request->input('shoe_size');
        $profile->blood_type = $request->input('blood_type');
        $profile->blood_rhesus = $request->input('blood_rhesus');
        $profile->hobbies = $request->input('hobbies');
        $profile->medical_history = $request->input('medical_history');

        // Section II: Identitas Diri
        $profile->ktp_number = $request->input('ktp_number');
        $profile->ktp_expiry = $request->input('ktp_expiry');
        $profile->npwp_number = $request->input('npwp_number');
        $profile->npwp_expiry = $request->input('npwp_expiry');
        $profile->passport_number = $request->input('passport_number');
        $profile->passport_expiry = $request->input('passport_expiry');
        $profile->bpjs_tk_number = $request->input('bpjs_tk_number');
        $profile->bpjs_tk_expiry = $request->input('bpjs_tk_expiry');
        $profile->marriage_cert_number = $request->input('marriage_cert_number');
        $profile->marriage_cert_expiry = $request->input('marriage_cert_expiry');
        $profile->family_card_number = $request->input('family_card_number');
        $profile->sim_a_number = $request->input('sim_a_number');
        $profile->sim_a_expiry = $request->input('sim_a_expiry');
        $profile->sim_c_number = $request->input('sim_c_number');
        $profile->sim_c_expiry = $request->input('sim_c_expiry');
        $profile->vehicles_data = $request->input('vehicles', []);

        // Section III: Kontak & Alamat
        $profile->phone_wa = $request->input('phone_wa');
        $profile->email = $request->input('email', $user->email);
        $profile->ktp_address = $request->input('ktp_address');
        $profile->ktp_province = $request->input('ktp_province');
        $profile->ktp_city = $request->input('ktp_city');
        $profile->ktp_home_phone = $request->input('ktp_home_phone');
        $profile->ktp_housing_status = $request->input('ktp_housing_status');
        $profile->domicile_address = $request->input('domicile_address');
        $profile->domicile_province = $request->input('domicile_province');
        $profile->domicile_city = $request->input('domicile_city');
        $profile->domicile_phone = $request->input('domicile_phone');
        $profile->domicile_housing_status = $request->input('domicile_housing_status');
        $profile->emergency_contact = $request->input('emergency_contact', []);

        // Section IV: Data Keluarga
        $profile->family_father = $request->input('family_father', []);
        $profile->family_mother = $request->input('family_mother', []);
        $profile->family_siblings = $request->input('family_siblings', []);
        $profile->family_core = $request->input('family_core', []);

        // Section V: Pendidikan & Keterampilan
        $profile->education_formal = $request->input('education_formal', []);
        $profile->education_non_formal = $request->input('education_non_formal', []);
        $profile->organizations = $request->input('organizations', []);
        $profile->languages = $request->input('languages', []);

        // Section VI: Pengalaman Kerja
        $profile->work_experiences = $request->input('work_experiences', []);

        // Section VII: Referensi
        $profile->references_data = $request->input('references', []);

        // Section VIII: Esai & Pertanyaan Rekrutmen
        $profile->strengths_weaknesses = $request->input('strengths_weaknesses');
        $profile->proudest_achievement = $request->input('proudest_achievement');
        $profile->applied_before = $request->boolean('applied_before');
        $profile->expected_salary = $request->input('expected_salary');
        $profile->willing_to_relocate = $request->boolean('willing_to_relocate');
        $profile->estimated_start_date = $request->input('estimated_start_date');
        $profile->preparation_notes = $request->input('preparation_notes');
        $profile->recruitment_location = $request->input('recruitment_location');
        $profile->agreement_signed = $request->boolean('agreement_signed');
        $profile->is_completed = true;

        $profile->save();

        // Sync to user model
        $user->update([
            'name' => $profile->full_name,
            'phone' => $profile->phone_wa,
        ]);

        if ($redirectJob = $request->input('redirect_job')) {
            return redirect()->route('career.jobs.show', $redirectJob)
                ->with('success', 'Data profil inti berhasil disimpan! Anda sekarang dapat langsung mengirimkan lamaran.');
        }

        return redirect()->route('career.dashboard')
            ->with('success', 'Formulir Data Diri, Dokumen & CV lengkap Anda berhasil disimpan!');
    }

    /**
     * Display candidate psychotests hub for the given application.
     */
    public function psychotestsIndex(Request $request, JobApplication $application): View
    {
        abort_unless($application->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');

        $psychotests = Psychotest::where('is_active', true)->orderBy('id')->get();
        $results = CandidatePsychotestResult::where('job_application_id', $application->id)
            ->get()
            ->keyBy('psychotest_id');

        return view('career.psychotests.index', compact('application', 'psychotests', 'results'));
    }

    /**
     * Show interactive psychotest taking page (Kraepelin or Likert Personality).
     */
    public function showPsychotest(Request $request, JobApplication $application, Psychotest $psychotest): View
    {
        abort_unless($application->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');
        abort_unless($psychotest->is_active, 404, 'Modul psikotes tidak aktif.');

        $existingResult = CandidatePsychotestResult::where('job_application_id', $application->id)
            ->where('psychotest_id', $psychotest->id)
            ->first();

        if ($psychotest->isKraepelin()) {
            $columnsCount = $psychotest->questions_data['columns_count'] ?? 6;
            $rowsCount = $psychotest->questions_data['rows_per_column'] ?? 25;
            $secondsPerColumn = $psychotest->questions_data['seconds_per_column'] ?? 20;

            $kraepelinColumns = [];
            for ($c = 0; $c < $columnsCount; $c++) {
                $columnNumbers = [];
                for ($r = 0; $r < $rowsCount; $r++) {
                    $columnNumbers[] = rand(1, 9);
                }
                $kraepelinColumns[] = $columnNumbers;
            }

            return view('career.psychotests.kraepelin', compact(
                'application',
                'psychotest',
                'kraepelinColumns',
                'columnsCount',
                'rowsCount',
                'secondsPerColumn',
                'existingResult'
            ));
        }

        if ($psychotest->isLikertPersonality()) {
            $questions = $psychotest->questions_data['questions'] ?? [];
            $scale = $psychotest->questions_data['scale'] ?? [
                1 => 'Sangat Tidak Sesuai',
                2 => 'Tidak Sesuai',
                3 => 'Netral / Ragu-Ragu',
                4 => 'Sesuai',
                5 => 'Sangat Sesuai',
            ];

            return view('career.psychotests.personality', compact(
                'application',
                'psychotest',
                'questions',
                'scale',
                'existingResult'
            ));
        }

        return view('career.psychotests.general', compact('application', 'psychotest', 'existingResult'));
    }

    /**
     * Submit completed psychotest results.
     */
    public function submitPsychotest(Request $request, JobApplication $application, Psychotest $psychotest): RedirectResponse|JsonResponse
    {
        abort_unless($application->user_id === $request->user()->id, 403, 'Akses tidak diizinkan.');

        if ($psychotest->isKraepelin()) {
            $validated = $request->validate([
                'total_attempted' => ['required', 'integer', 'min:0'],
                'correct_count' => ['required', 'integer', 'min:0'],
                'incorrect_count' => ['required', 'integer', 'min:0'],
                'columns_completed' => ['required', 'integer', 'min:0'],
                'column_details' => ['nullable', 'array'],
            ]);

            $totalAttempted = $validated['total_attempted'];
            $correctCount = $validated['correct_count'];
            $accuracy = $totalAttempted > 0 ? round(($correctCount / $totalAttempted) * 100, 1) : 0;

            // Speed score based on expected baseline (e.g. 75 calculations across columns)
            $expectedBaseline = ($psychotest->questions_data['columns_count'] ?? 6) * 12;
            $speedRate = min(100, round(($totalAttempted / max(1, $expectedBaseline)) * 100, 1));

            // Kraepelin final score = 60% Akurasi + 40% Kecepatan
            $finalScore = (int) round(($accuracy * 0.6) + ($speedRate * 0.4));
            $finalScore = min(100, max(0, $finalScore));

            $isPassed = $finalScore >= $psychotest->passing_score;

            $result = CandidatePsychotestResult::updateOrCreate(
                [
                    'job_application_id' => $application->id,
                    'psychotest_id' => $psychotest->id,
                ],
                [
                    'answers_submitted' => [
                        'type' => 'KRAEPELIN',
                        'total_attempted' => $totalAttempted,
                        'correct_count' => $correctCount,
                        'incorrect_count' => $validated['incorrect_count'],
                        'accuracy_rate' => $accuracy,
                        'speed_rate' => $speedRate,
                        'columns_completed' => $validated['columns_completed'],
                        'column_details' => $validated['column_details'] ?? [],
                    ],
                    'total_score' => $finalScore,
                    'result_status' => $isPassed ? 'PASSED' : 'FAILED',
                    'completed_at' => now(),
                ]
            );
        } elseif ($psychotest->isLikertPersonality()) {
            $validated = $request->validate([
                'answers' => ['required', 'array'],
                'answers.*' => ['required', 'integer', 'between:1,5'],
            ]);

            $answers = $validated['answers'];
            $questions = collect($psychotest->questions_data['questions'] ?? []);

            $totalSum = 0;
            $dimensionScores = [];

            foreach ($answers as $qId => $val) {
                $totalSum += (int) $val;
                $qMeta = $questions->firstWhere('id', (int) $qId);
                $dimName = $qMeta['dimension'] ?? 'Umum';

                if (! isset($dimensionScores[$dimName])) {
                    $dimensionScores[$dimName] = ['sum' => 0, 'count' => 0];
                }
                $dimensionScores[$dimName]['sum'] += (int) $val;
                $dimensionScores[$dimName]['count'] += 1;
            }

            $questionCount = max(1, count($answers));
            $averageScore = round($totalSum / $questionCount, 2);
            $finalScore = (int) round(($totalSum / ($questionCount * 5)) * 100);

            $dimensionPercentages = [];
            foreach ($dimensionScores as $dim => $data) {
                $dimensionPercentages[$dim] = round(($data['sum'] / ($data['count'] * 5)) * 100, 1);
            }

            $isPassed = $finalScore >= $psychotest->passing_score;

            $result = CandidatePsychotestResult::updateOrCreate(
                [
                    'job_application_id' => $application->id,
                    'psychotest_id' => $psychotest->id,
                ],
                [
                    'answers_submitted' => [
                        'type' => 'LIKERT_PERSONALITY',
                        'answers' => $answers,
                        'average_rating' => $averageScore,
                        'total_sum' => $totalSum,
                        'dimension_scores' => $dimensionPercentages,
                    ],
                    'total_score' => $finalScore,
                    'result_status' => $isPassed ? 'PASSED' : 'FAILED',
                    'completed_at' => now(),
                ]
            );
        } else {
            $answers = $request->input('answers', []);
            $questions = is_array($psychotest->questions_data) ? $psychotest->questions_data : [];
            $totalPoints = 0;
            $maxPoints = 0;
            $correctCount = 0;

            foreach ($questions as $q) {
                $weight = $q['score_weight'] ?? 20;
                $maxPoints += $weight;
                $qId = $q['id'] ?? null;
                if ($qId && isset($answers[$qId]) && $answers[$qId] === ($q['correct_answer'] ?? '')) {
                    $totalPoints += $weight;
                    $correctCount++;
                }
            }

            $finalScore = $maxPoints > 0 ? (int) round(($totalPoints / $maxPoints) * 100) : 80;
            $isPassed = $finalScore >= $psychotest->passing_score;

            $result = CandidatePsychotestResult::updateOrCreate(
                [
                    'job_application_id' => $application->id,
                    'psychotest_id' => $psychotest->id,
                ],
                [
                    'answers_submitted' => [
                        'type' => 'GENERAL',
                        'answers' => $answers,
                        'correct_count' => $correctCount,
                        'total_questions' => count($questions),
                    ],
                    'total_score' => $finalScore,
                    'result_status' => $isPassed ? 'PASSED' : 'FAILED',
                    'completed_at' => now(),
                ]
            );
        }

        // Auto-advance application to PSYCHOTEST_PASSED if all active tests passed
        $allActiveTests = Psychotest::where('is_active', true)->pluck('id');
        $passedCount = CandidatePsychotestResult::where('job_application_id', $application->id)
            ->whereIn('psychotest_id', $allActiveTests)
            ->where('result_status', 'PASSED')
            ->count();

        if ($passedCount >= $allActiveTests->count() && in_array($application->current_stage, ['APPLIED', 'SHORTLISTED'])) {
            $application->update([
                'current_stage' => 'PSYCHOTEST_PASSED',
                'stage_notes' => 'Kandidat berhasil menyelesaikan dan lulus seluruh modul psikotes online.',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score' => $finalScore,
                'status' => $result->result_status,
                'redirect' => route('career.psychotests.index', $application->id),
            ]);
        }

        return redirect()->route('career.psychotests.index', $application->id)
            ->with('success', "Tes {$psychotest->title} berhasil diselesaikan dengan skor {$finalScore}!");
    }
}
