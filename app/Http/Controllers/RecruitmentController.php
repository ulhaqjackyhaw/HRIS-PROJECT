<?php

namespace App\Http\Controllers;

use App\Models\ApplicationCommunication;
use App\Models\CandidatePsychotestResult;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Position;
use App\Models\Psychotest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    /**
     * Display the Recruitment & ATS Overview Dashboard.
     */
    public function dashboard(): View
    {
        $activeJobsCount = JobPosting::where('status', 'PUBLISHED')->count();
        $totalJobsCount = JobPosting::count();
        $totalApplicationsCount = JobApplication::count();

        $inProgressCount = JobApplication::whereNotIn('current_stage', ['HIRED', 'REJECTED'])->count();
        $hiredCount = JobApplication::where('current_stage', 'HIRED')->count();
        $rejectedCount = JobApplication::where('current_stage', 'REJECTED')->count();

        // Stage breakdown counts
        $stageCounts = [];
        foreach (JobApplication::STAGES as $stageKey => $stageInfo) {
            $stageCounts[$stageKey] = JobApplication::where('current_stage', $stageKey)->count();
        }

        // Recent applications
        $recentApplications = JobApplication::with(['jobPosting.department', 'user.candidateProfile'])
            ->latest('applied_at')
            ->take(6)
            ->get();

        // Active job postings with applicant count
        $activeJobs = JobPosting::with(['department'])
            ->withCount('applications')
            ->where('status', 'PUBLISHED')
            ->latest()
            ->take(5)
            ->get();

        // Psychotest completions
        $recentTestResults = CandidatePsychotestResult::with(['jobApplication', 'psychotest'])
            ->latest()
            ->take(5)
            ->get();

        return view('modules.recruitment.dashboard', compact(
            'activeJobsCount',
            'totalJobsCount',
            'totalApplicationsCount',
            'inProgressCount',
            'hiredCount',
            'rejectedCount',
            'stageCounts',
            'recentApplications',
            'activeJobs',
            'recentTestResults'
        ));
    }

    /**
     * Display list of Job Postings.
     */
    public function jobs(Request $request): View
    {
        $query = JobPosting::with('department')
            ->withCount('applications')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        $jobs = $query->paginate(10)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('modules.recruitment.jobs.index', compact('jobs', 'departments'));
    }

    /**
     * Show form to create a new Job Posting.
     */
    public function createJob(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('modules.recruitment.jobs.create', compact('departments'));
    }

    /**
     * Store a new Job Posting.
     */
    public function storeJob(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'job_level' => ['required', 'string'],
            'employment_type' => ['required', 'string'],
            'work_model' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'hide_salary' => ['boolean'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
        ]);

        $validated['hide_salary'] = $request->boolean('hide_salary');
        $validated['created_by'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::lower(Str::random(5));

        JobPosting::create($validated);

        return redirect()->route('recruitment.jobs.index')
            ->with('success', "Lowongan posisi {$validated['title']} berhasil diterbitkan!");
    }

    /**
     * Show form to edit an existing Job Posting.
     */
    public function editJob(JobPosting $job): View
    {
        $departments = Department::orderBy('name')->get();

        return view('modules.recruitment.jobs.edit', compact('job', 'departments'));
    }

    /**
     * Update an existing Job Posting.
     */
    public function updateJob(Request $request, JobPosting $job): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'job_level' => ['required', 'string'],
            'employment_type' => ['required', 'string'],
            'work_model' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'hide_salary' => ['boolean'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
        ]);

        $validated['hide_salary'] = $request->boolean('hide_salary');

        $job->update($validated);

        return redirect()->route('recruitment.jobs.index')
            ->with('success', "Lowongan {$job->title} berhasil diperbarui!");
    }

    /**
     * Toggle Job Posting Status (PUBLISHED / CLOSED).
     */
    public function toggleJobStatus(JobPosting $job): RedirectResponse
    {
        $newStatus = $job->status === 'PUBLISHED' ? 'CLOSED' : 'PUBLISHED';
        $job->update(['status' => $newStatus]);

        return back()->with('success', "Status lowongan {$job->title} diubah menjadi {$newStatus}.");
    }

    /**
     * Delete a Job Posting.
     */
    public function destroyJob(JobPosting $job): RedirectResponse
    {
        $title = $job->title;
        $job->delete();

        return redirect()->route('recruitment.jobs.index')
            ->with('success', "Lowongan {$title} berhasil dihapus.");
    }

    /**
     * Display ATS Pipeline & Applicant Applications.
     */
    public function applications(Request $request): View
    {
        $viewMode = $request->input('view', 'kanban'); // 'kanban' or 'table'
        $jobId = $request->input('job_id');
        $stage = $request->input('stage');
        $search = $request->input('search');

        $query = JobApplication::with(['jobPosting.department', 'user.candidateProfile', 'psychotestResults'])
            ->latest('applied_at');

        if ($jobId) {
            $query->where('job_posting_id', $jobId);
        }

        if ($stage) {
            $query->where('current_stage', $stage);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                    ->orWhere('applicant_email', 'like', "%{$search}%")
                    ->orWhere('applicant_phone', 'like', "%{$search}%");
            });
        }

        $allApplications = $query->get();

        // Stage counts for quick filters
        $stageCounts = [];
        foreach (JobApplication::STAGES as $stageKey => $stageInfo) {
            $countQuery = JobApplication::where('current_stage', $stageKey);
            if ($jobId) {
                $countQuery->where('job_posting_id', $jobId);
            }
            $stageCounts[$stageKey] = $countQuery->count();
        }

        $jobPostings = JobPosting::orderBy('title')->get();

        return view('modules.recruitment.applications.index', compact(
            'allApplications',
            'stageCounts',
            'jobPostings',
            'viewMode',
            'jobId',
            'stage',
            'search'
        ));
    }

    /**
     * Show Candidate 360 Profile & Application Inspector.
     */
    public function showApplication(JobApplication $application): View
    {
        $application->load([
            'jobPosting.department',
            'user.candidateProfile',
            'psychotestResults.psychotest',
            'communications',
            'employee',
        ]);

        $profile = $application->user?->candidateProfile;
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return view('modules.recruitment.applications.show', compact(
            'application',
            'profile',
            'departments',
            'positions'
        ));
    }

    /**
     * Update Application Selection Stage.
     */
    public function updateStage(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'in:'.implode(',', array_keys(JobApplication::STAGES))],
            'stage_notes' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStage = $application->current_stage;
        $newStage = $validated['stage'];

        $updateData = [
            'current_stage' => $newStage,
            'stage_notes' => $validated['stage_notes'] ?? $request->input('notes') ?? $application->stage_notes,
        ];

        if ($newStage === 'HIRED' && ! $application->hired_at) {
            $updateData['hired_at'] = now();
        }

        $application->update($updateData);

        return back()->with('success', "Tahapan pelamar {$application->applicant_name} berhasil diubah ke: {$application->stage_label}.");
    }

    /**
     * Schedule an Interview Round for the Applicant.
     */
    public function scheduleInterview(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'interview_scheduled_at' => ['required', 'date'],
            'interview_location' => ['required', 'string', 'max:255'],
            'round' => ['required', 'in:INTERVIEW_HR,INTERVIEW_USER,INTERVIEW_BOD'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'interview_scheduled_at' => $validated['interview_scheduled_at'],
            'interview_location' => $validated['interview_location'],
            'current_stage' => $validated['round'],
            'stage_notes' => $validated['notes'] ? "Jadwal Wawancara: {$validated['notes']}" : $application->stage_notes,
        ]);

        // Automatically log communication invitation record
        ApplicationCommunication::create([
            'job_application_id' => $application->id,
            'channel' => 'WHATSAPP',
            'recipient' => $application->applicant_phone,
            'subject' => "Undangan Wawancara - {$application->jobPosting->title}",
            'message' => "Halo {$application->applicant_name}, Anda diundang untuk mengikuti wawancara posisi {$application->jobPosting->title} pada tanggal {$validated['interview_scheduled_at']} di/melalui {$validated['interview_location']}.",
            'status' => 'SENT',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Wawancara berhasil dijadwalkan dan undangan telah dicatat.');
    }

    /**
     * Send Communication Broadcast (WhatsApp / Email).
     */
    public function sendCommunication(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'channel' => ['required', 'in:WHATSAPP,EMAIL'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $recipient = $validated['channel'] === 'WHATSAPP' ? $application->applicant_phone : $application->applicant_email;

        ApplicationCommunication::create([
            'job_application_id' => $application->id,
            'channel' => $validated['channel'],
            'recipient' => $recipient,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'SENT',
            'sent_at' => now(),
        ]);

        return back()->with('success', "Pesan {$validated['channel']} berhasil dikirimkan ke {$recipient}.");
    }

    /**
     * Auto-Onboarding: Convert Hired Candidate to Core HR Employee.
     */
    public function convertToEmployee(Request $request, JobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'employment_status' => ['required', 'in:PERMANENT,CONTRACT,INTERNSHIP,PROBATION'],
            'join_date' => ['required', 'date'],
            'nik' => ['nullable', 'string', 'unique:employees,nik'],
        ]);

        $profile = $application->user?->candidateProfile;

        // Auto-generate NIK if not provided
        $nik = $validated['nik'] ?? 'EMP-'.now()->format('Ym').'-'.str_pad((string) (Employee::count() + 1), 4, '0', STR_PAD_LEFT);

        $gender = match ($profile?->gender) {
            'Perempuan', 'FEMALE' => 'FEMALE',
            default => 'MALE',
        };

        $employee = Employee::create([
            'user_id' => $application->user_id,
            'nik' => $nik,
            'full_name' => $profile?->full_name ?? $application->applicant_name,
            'email' => $profile?->email ?? $application->applicant_email,
            'phone_number' => $profile?->phone_wa ?? $application->applicant_phone,
            'gender' => $gender,
            'birth_date' => $profile?->birth_date ?? now()->subYears(23)->format('Y-m-d'),
            'ktp_number' => ! empty($profile?->ktp_number) ? $profile->ktp_number : ('317'.now()->format('ymd').rand(1000, 9999)),
            'npwp' => $profile?->npwp_number,
            'current_address' => $profile?->domicile_address ?? '-',
            'ktp_address' => $profile?->ktp_address ?? $profile?->domicile_address ?? '-',
            'department_id' => $validated['department_id'],
            'position_id' => $validated['position_id'],
            'join_date' => $validated['join_date'],
            'employment_status' => $validated['employment_status'],
            'lifecycle_stage' => 'ONBOARDING',
            'is_active' => true,
        ]);

        // Link employee to application and update stage to HIRED
        $application->update([
            'employee_id' => $employee->id,
            'current_stage' => 'HIRED',
            'hired_at' => now(),
            'stage_notes' => "Dikonversi menjadi Karyawan Resmi Core HR (NIK: {$employee->nik})",
        ]);

        // Upgrade user account to INTERNAL
        if ($application->user) {
            $application->user->update(['user_type' => 'INTERNAL']);
        }

        return redirect()->route('employees.show', $employee->id)
            ->with('success', "Selamat! Kandidat {$application->applicant_name} resmi dikonversi menjadi Karyawan Aktif (NIK: {$employee->nik}).");
    }

    /**
     * Display Psychotest Banks and Candidate Results (Grouped by Candidate).
     */
    public function psychotests(Request $request): View
    {
        $psychotests = Psychotest::withCount('results')->latest()->get();

        $query = JobApplication::whereHas('psychotestResults')
            ->with([
                'jobPosting.department',
                'psychotestResults.psychotest',
                'psychotestResults.retakeGrantedBy',
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                    ->orWhere('applicant_email', 'like', "%{$search}%")
                    ->orWhereHas('jobPosting', function ($jq) use ($search) {
                        $jq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $candidateApplications = $query->withMax('psychotestResults', 'completed_at')
            ->orderByDesc('psychotest_results_max_completed_at')
            ->paginate(15)
            ->withQueryString();

        $testResults = $candidateApplications;

        return view('modules.recruitment.psychotests.index', compact('psychotests', 'candidateApplications', 'testResults'));
    }

    /**
     * Grant permission for a candidate to retake a specific psychotest.
     */
    public function allowPsychotestRetake(Request $request, JobApplication $application, Psychotest $psychotest): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $result = CandidatePsychotestResult::where('job_application_id', $application->id)
            ->where('psychotest_id', $psychotest->id)
            ->first();

        if (! $result) {
            return back()->with('error', 'Hasil psikotes untuk kandidat ini tidak ditemukan.');
        }

        $reason = $validated['reason'] ?: 'Izin uji ulang diberikan oleh HR Administrator';

        $result->update([
            'can_retake' => true,
            'retake_reason' => $reason,
            'retake_granted_at' => now(),
            'retake_granted_by' => $request->user()->id,
        ]);

        if (in_array($application->current_stage, ['PSYCHOTEST_PASSED', 'REJECTED'])) {
            $application->update([
                'current_stage' => 'SHORTLISTED',
                'stage_notes' => "HR membuka kembali tahap psikotes untuk uji ulang modul: {$psychotest->title}. Catatan: {$reason}",
            ]);
        }

        return back()->with('success', "Izin uji ulang untuk modul '{$psychotest->title}' berhasil diaktifkan. Kandidat {$application->applicant_name} sekarang dapat mengerjakan tes kembali dari portal karir.");
    }

    /**
     * Cancel retake permission for a psychotest.
     */
    public function cancelPsychotestRetake(Request $request, JobApplication $application, Psychotest $psychotest): RedirectResponse
    {
        $result = CandidatePsychotestResult::where('job_application_id', $application->id)
            ->where('psychotest_id', $psychotest->id)
            ->first();

        if ($result) {
            $result->update([
                'can_retake' => false,
            ]);
        }

        return back()->with('success', "Izin uji ulang untuk modul '{$psychotest->title}' telah dibatalkan. Hasil ujian sebelumnya tetap berlaku permanen.");
    }
}
