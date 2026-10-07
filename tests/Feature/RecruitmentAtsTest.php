<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\CandidatePsychotestResult;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Position;
use App\Models\Psychotest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecruitmentAtsTest extends TestCase
{
    use RefreshDatabase;

    private User $hrUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hrUser = User::factory()->create([
            'email' => 'hr.manager@company.local',
            'user_type' => 'INTERNAL',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_recruitment_panel(): void
    {
        $response = $this->get(route('recruitment.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_hr_user_can_access_recruitment_dashboard(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('recruitment.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Rekrutmen');
        $response->assertSee('Lowongan Aktif');
        $response->assertSee('Pipeline ATS');
    }

    public function test_hr_can_create_new_job_posting(): void
    {
        $department = Department::factory()->create(['name' => 'Human Capital']);

        $payload = [
            'title' => 'Talent Acquisition Specialist',
            'department_id' => $department->id,
            'job_level' => 'Mid Level',
            'location' => 'Jakarta Selatan',
            'employment_type' => 'FULL_TIME',
            'work_model' => 'HYBRID',
            'salary_min' => 8000000,
            'salary_max' => 12000000,
            'hide_salary' => '1',
            'description' => 'Bertanggung jawab dalam end-to-end recruitment.',
            'requirements' => 'Pengalaman minimal 2 tahun di bidang talent acquisition.',
            'benefits' => 'BPJS, Laptop, Health Insurance',
            'deadline' => now()->addMonth()->format('Y-m-d'),
            'status' => 'PUBLISHED',
        ];

        $response = $this->actingAs($this->hrUser)
            ->post(route('recruitment.jobs.store'), $payload);

        $response->assertRedirect(route('recruitment.jobs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'title' => 'Talent Acquisition Specialist',
            'department_id' => $department->id,
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_hr_can_update_job_posting(): void
    {
        $department = Department::factory()->create();
        $job = JobPosting::factory()->create([
            'department_id' => $department->id,
            'title' => 'Junior Recruiter',
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->hrUser)
            ->put(route('recruitment.jobs.update', $job->id), [
                'title' => 'Lead Recruiter',
                'department_id' => $department->id,
                'job_level' => 'Senior / Lead',
                'location' => 'Jakarta Pusat',
                'employment_type' => 'FULL_TIME',
                'work_model' => 'ONSITE',
                'description' => 'Memimpin tim perekrutan perusahaan.',
                'status' => 'PUBLISHED',
            ]);

        $response->assertRedirect(route('recruitment.jobs.index'));
        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'title' => 'Lead Recruiter',
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_hr_can_toggle_job_posting_status(): void
    {
        $job = JobPosting::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $response = $this->actingAs($this->hrUser)
            ->patch(route('recruitment.jobs.toggle-status', $job->id));

        $response->assertSessionHas('success');
        $this->assertEquals('CLOSED', $job->fresh()->status);

        $this->actingAs($this->hrUser)
            ->patch(route('recruitment.jobs.toggle-status', $job->id));

        $this->assertEquals('PUBLISHED', $job->fresh()->status);
    }

    public function test_hr_can_view_applications_in_kanban_and_table_view(): void
    {
        $job = JobPosting::factory()->create(['title' => 'Fullstack Developer']);
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Budi Santoso',
            'applicant_email' => 'budi.santoso@example.com',
            'applicant_phone' => '081234567890',
            'current_stage' => 'APPLIED',
            'applied_at' => now(),
        ]);

        // Kanban View
        $responseKanban = $this->actingAs($this->hrUser)
            ->get(route('recruitment.applications.index', ['view' => 'kanban']));
        $responseKanban->assertStatus(200);
        $responseKanban->assertSee('Budi Santoso');
        $responseKanban->assertSee('Fullstack Developer');

        // Table View
        $responseTable = $this->actingAs($this->hrUser)
            ->get(route('recruitment.applications.index', ['view' => 'table']));
        $responseTable->assertStatus(200);
        $responseTable->assertSee('Budi Santoso');
    }

    public function test_hr_can_inspect_candidate_360_dossier(): void
    {
        $candidateUser = User::factory()->create(['user_type' => 'CANDIDATE']);
        $profile = CandidateProfile::create([
            'user_id' => $candidateUser->id,
            'full_name' => 'Siti Nurhaliza',
            'nickname' => 'Siti',
            'gender' => 'Perempuan',
            'birth_date' => '1998-05-12',
            'birth_place' => 'Bandung',
            'height_cm' => 165,
            'weight_kg' => 52,
            'clothing_size' => 'M',
            'shoe_size' => '38',
            'blood_type' => 'O',
            'phone_wa' => '08987654321',
            'domicile_address' => 'Jl. Merdeka No. 45, Bandung',
            'domicile_city' => 'Bandung',
            'domicile_province' => 'Jawa Barat',
            'family_father' => ['name' => 'Bambang Sudarsono', 'job' => 'Pensiunan'],
            'vehicles_data' => ['car' => ['brand' => 'Honda Jazz', 'year' => '2021', 'status' => 'Milik Sendiri']],
            'education_formal' => ['s1' => ['school' => 'Universitas Indonesia', 'place' => 'Depok', 'major' => 'Manajemen SDM', 'graduation_year' => '2020']],
            'work_experiences' => [['company' => 'PT Nusantara Sejahtera', 'position_end' => 'Junior Recruiter', 'salary' => 6500000]],
            'expected_salary' => 9000000,
            'willing_to_relocate' => true,
            'agreement_signed' => true,
            'cv_path' => 'cv_files/dummy_cv.pdf',
            'is_completed' => true,
        ]);

        $job = JobPosting::factory()->create(['title' => 'HR Generalist']);
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $candidateUser->id,
            'applicant_name' => 'Siti Nurhaliza',
            'applicant_email' => $candidateUser->email,
            'applicant_phone' => '08987654321',
            'current_stage' => 'APPLIED',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->get(route('recruitment.applications.show', $application->id));

        $response->assertStatus(200);
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('Review 360° Pelamar');
        $response->assertSee('Convert to Employee');
        $response->assertSee('08987654321');
        $response->assertSee('Perempuan');
        $response->assertSee('165 cm');
        $response->assertSee('Bambang Sudarsono');
        $response->assertSee('Honda Jazz');
        $response->assertSee('Universitas Indonesia');
        $response->assertSee('PT Nusantara Sejahtera');
        $response->assertSee('9.000.000');
    }

    public function test_hr_can_advance_applicant_stage(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Ahmad Dani',
            'applicant_email' => 'ahmad@example.com',
            'applicant_phone' => '081234567891',
            'current_stage' => 'APPLIED',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->patch(route('recruitment.applications.stage', $application->id), [
                'stage' => 'SHORTLISTED',
                'notes' => 'CV sesuai kualifikasi awal.',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('SHORTLISTED', $application->fresh()->current_stage);
        $this->assertEquals('CV sesuai kualifikasi awal.', $application->fresh()->stage_notes);
    }

    public function test_hr_can_schedule_interview(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Citra Lestari',
            'applicant_email' => 'citra@example.com',
            'applicant_phone' => '081234567892',
            'current_stage' => 'INTERVIEW_HR',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->post(route('recruitment.applications.interview', $application->id), [
                'round' => 'INTERVIEW_HR',
                'interview_scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'interview_location' => 'https://meet.google.com/abc-defg-hij',
                'notes' => 'Persiapkan portofolio desain.',
            ]);

        $response->assertSessionHas('success');

        $this->assertEquals('INTERVIEW_HR', $application->fresh()->current_stage);
        $this->assertEquals('https://meet.google.com/abc-defg-hij', $application->fresh()->interview_location);

        $this->assertDatabaseHas('application_communications', [
            'job_application_id' => $application->id,
            'channel' => 'WHATSAPP',
            'recipient' => '081234567892',
        ]);
    }

    public function test_hr_can_send_communication_templates(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Doni Salman',
            'applicant_email' => 'doni@example.com',
            'applicant_phone' => '087711223344',
            'current_stage' => 'INTERVIEW_USER',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->post(route('recruitment.applications.communicate', $application->id), [
                'channel' => 'WHATSAPP',
                'recipient' => '087711223344',
                'subject' => 'Konfirmasi Interview User',
                'message' => 'Halo Doni, berikut konfirmasi jadwal interview teknis Anda.',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('application_communications', [
            'job_application_id' => $application->id,
            'channel' => 'WHATSAPP',
            'recipient' => '087711223344',
            'subject' => 'Konfirmasi Interview User',
        ]);
    }

    public function test_hr_can_auto_onboard_candidate_into_core_hr_employee(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $candidateUser = User::factory()->create([
            'user_type' => 'CANDIDATE',
            'email' => 'onboard.candidate@example.com',
        ]);

        CandidateProfile::create([
            'user_id' => $candidateUser->id,
            'full_name' => 'Rizky Febrian',
            'gender' => 'Laki-laki',
            'birth_date' => '1995-10-20',
            'phone_wa' => '081234567899',
            'domicile_address' => 'Jl. Sudirman Kav 20, Jakarta',
            'is_completed' => true,
        ]);

        $job = JobPosting::factory()->create(['department_id' => $department->id]);
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $candidateUser->id,
            'applicant_name' => 'Rizky Febrian',
            'applicant_email' => 'onboard.candidate@example.com',
            'applicant_phone' => '081234567899',
            'current_stage' => 'OFFERING',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->post(route('recruitment.applications.convert-employee', $application->id), [
                'department_id' => $department->id,
                'position_id' => $position->id,
                'employment_status' => 'PERMANENT',
                'join_date' => now()->format('Y-m-d'),
                'nik' => 'EMP-202610-9999',
            ]);

        $employee = Employee::where('nik', 'EMP-202610-9999')->first();
        $this->assertNotNull($employee);
        $this->assertEquals('Rizky Febrian', $employee->full_name);
        $this->assertEquals('onboard.candidate@example.com', $employee->email);
        $this->assertEquals('PERMANENT', $employee->employment_status);
        $this->assertEquals('ONBOARDING', $employee->lifecycle_stage);

        // Assert redirect to employee detail page
        $response->assertRedirect(route('employees.show', $employee->id));
        $response->assertSessionHas('success');

        // Assert Application stage updated to HIRED
        $this->assertEquals('HIRED', $application->fresh()->current_stage);
        $this->assertEquals($employee->id, $application->fresh()->employee_id);

        // Assert Candidate user account upgraded to INTERNAL
        $this->assertEquals('INTERNAL', $candidateUser->fresh()->user_type);
    }

    public function test_hr_can_view_psychotests_monitoring_page(): void
    {
        $psychotest = Psychotest::create([
            'title' => 'Tes Logika Aritmatika & Penalaran',
            'duration_minutes' => 30,
            'passing_score' => 75,
            'questions_data' => [
                ['question' => '2, 4, 8, 16, ...', 'options' => ['24', '32', '64'], 'correct_index' => 1],
            ],
            'is_active' => true,
        ]);

        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Diana Rose',
            'applicant_email' => 'diana@example.com',
            'applicant_phone' => '081234567893',
            'current_stage' => 'PSYCHOTEST_PASSED',
            'applied_at' => now(),
        ]);

        $psychotest2 = Psychotest::create([
            'title' => 'Tes Karakter & Kepribadian',
            'duration_minutes' => 20,
            'passing_score' => 60,
            'questions_data' => [],
            'is_active' => true,
        ]);

        CandidatePsychotestResult::create([
            'job_application_id' => $application->id,
            'psychotest_id' => $psychotest->id,
            'total_score' => 85,
            'result_status' => 'PASSED',
            'completed_at' => now()->subHour(),
        ]);

        CandidatePsychotestResult::create([
            'job_application_id' => $application->id,
            'psychotest_id' => $psychotest2->id,
            'total_score' => 95,
            'result_status' => 'PASSED',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->hrUser)
            ->get(route('recruitment.psychotests.index'));

        $response->assertStatus(200);
        $response->assertSee('Monitoring Tes Psikotes Online');
        $response->assertSee('Diana Rose');
        $response->assertSee('Tes Logika Aritmatika & Penalaran');
        $response->assertSee('Tes Karakter & Kepribadian');
        $response->assertSee('85');
        $response->assertSee('95');
        $response->assertSee('90'); // Average (85 + 95) / 2
        $response->assertSee('2 Modul Selesai');
        $response->assertSee('LULUS');

        $searchResponse = $this->actingAs($this->hrUser)
            ->get(route('recruitment.psychotests.index', ['search' => 'Diana']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Diana Rose');

        $notFoundResponse = $this->actingAs($this->hrUser)
            ->get(route('recruitment.psychotests.index', ['search' => 'UnknownPersonXYZ']));
        $notFoundResponse->assertStatus(200);
        $notFoundResponse->assertDontSee('Diana Rose');
    }

    public function test_hr_can_grant_and_cancel_psychotest_retake_permission(): void
    {
        $psychotest = Psychotest::create([
            'title' => 'Tes Kemampuan Numerik',
            'duration_minutes' => 30,
            'passing_score' => 70,
            'questions_data' => [],
            'is_active' => true,
        ]);

        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'applicant_name' => 'Budi Santoso',
            'applicant_email' => 'budi@example.com',
            'applicant_phone' => '081234567894',
            'current_stage' => 'PSYCHOTEST_PASSED',
            'applied_at' => now(),
        ]);

        $result = CandidatePsychotestResult::create([
            'job_application_id' => $application->id,
            'psychotest_id' => $psychotest->id,
            'total_score' => 65,
            'result_status' => 'FAILED',
            'completed_at' => now(),
            'can_retake' => false,
        ]);

        // HR grants retake permission
        $grantResponse = $this->actingAs($this->hrUser)
            ->post(route('recruitment.applications.psychotests.allow-retake', [$application->id, $psychotest->id]), [
                'reason' => 'Kandidat mengalami koneksi terputus saat pengerjaan.',
            ]);

        $grantResponse->assertRedirect();
        $grantResponse->assertSessionHas('success');

        $result->refresh();
        $this->assertTrue($result->can_retake);
        $this->assertEquals('Kandidat mengalami koneksi terputus saat pengerjaan.', $result->retake_reason);
        $this->assertEquals($this->hrUser->id, $result->retake_granted_by);

        // Application stage should revert to SHORTLISTED
        $application->refresh();
        $this->assertEquals('SHORTLISTED', $application->current_stage);

        // HR cancels retake permission
        $cancelResponse = $this->actingAs($this->hrUser)
            ->post(route('recruitment.applications.psychotests.cancel-retake', [$application->id, $psychotest->id]));

        $cancelResponse->assertRedirect();
        $cancelResponse->assertSessionHas('success');

        $result->refresh();
        $this->assertFalse($result->can_retake);
    }
}
