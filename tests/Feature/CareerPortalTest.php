<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\Department;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\Psychotest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_career_landing_page_renders_successfully(): void
    {
        $response = $this->get(route('career.landing'));

        $response->assertStatus(200);
        $response->assertSee('HRIS Core');
        $response->assertSee('Careers');
        $response->assertSee('Peta 8 Tahapan Seleksi Transparan');
    }

    public function test_published_job_postings_are_visible_on_landing_page(): void
    {
        $department = Department::factory()->create(['name' => 'Engineering']);

        $publishedJob = JobPosting::factory()->create([
            'title' => 'Senior Laravel Architect',
            'department_id' => $department->id,
            'status' => 'PUBLISHED',
        ]);

        $draftJob = JobPosting::factory()->draft()->create([
            'title' => 'Secret Stealth Role',
            'department_id' => $department->id,
        ]);

        $response = $this->get(route('career.landing'));

        $response->assertStatus(200);
        $response->assertSee('Senior Laravel Architect');
        $response->assertDontSee('Secret Stealth Role');
    }

    public function test_career_search_and_filter(): void
    {
        $deptA = Department::factory()->create(['name' => 'Human Capital']);
        $deptB = Department::factory()->create(['name' => 'Finance']);

        $jobA = JobPosting::factory()->create([
            'title' => 'People Operations Lead',
            'department_id' => $deptA->id,
            'work_model' => 'HYBRID',
            'status' => 'PUBLISHED',
        ]);

        $jobB = JobPosting::factory()->create([
            'title' => 'Tax Accountant',
            'department_id' => $deptB->id,
            'work_model' => 'ON_SITE',
            'status' => 'PUBLISHED',
        ]);

        // Search by keyword
        $responseSearch = $this->get(route('career.landing', ['search' => 'People Operations']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('People Operations Lead');
        $responseSearch->assertDontSee('Tax Accountant');

        // Filter by department
        $responseDept = $this->get(route('career.landing', ['department_id' => $deptB->id]));
        $responseDept->assertStatus(200);
        $responseDept->assertSee('Tax Accountant');
        $responseDept->assertDontSee('People Operations Lead');

        // Filter by work model
        $responseModel = $this->get(route('career.landing', ['work_model' => 'HYBRID']));
        $responseModel->assertStatus(200);
        $responseModel->assertSee('People Operations Lead');
        $responseModel->assertDontSee('Tax Accountant');
    }

    public function test_job_detail_page_increments_views_and_renders(): void
    {
        $job = JobPosting::factory()->create([
            'title' => 'DevOps Cloud Engineer',
            'views_count' => 5,
            'status' => 'PUBLISHED',
        ]);

        // 1. Guest views job
        $response = $this->get(route('career.jobs.show', $job->slug));
        $response->assertStatus(200);
        $response->assertSee('DevOps Cloud Engineer');
        $this->assertEquals(6, $job->fresh()->views_count);

        // 2. Authenticated candidate without completed profile views job
        $user = User::factory()->create(['user_type' => 'CANDIDATE']);
        $authResponse = $this->actingAs($user)->get(route('career.jobs.show', $job->slug));
        $authResponse->assertStatus(200);
        $authResponse->assertSee('Lengkapi Data Inti Profil Anda');

        // 3. Authenticated candidate with completed profile views job
        CandidateProfile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'gender' => 'Laki-laki',
            'birth_date' => '1995-01-01',
            'birth_place' => 'Jakarta',
            'phone_wa' => '081233334444',
            'domicile_address' => 'Jl. Tebet Raya',
            'cv_path' => 'cv.pdf',
            'agreement_signed' => true,
            'is_completed' => true,
        ]);
        $user->refresh();
        $completedResponse = $this->actingAs($user)->get(route('career.jobs.show', $job->slug));
        $completedResponse->assertStatus(200);
        $completedResponse->assertSee('Kirim Lamaran Sekarang');
    }

    public function test_candidate_auth_routes_are_accessible(): void
    {
        $loginResponse = $this->get(route('career.login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Masuk Portal Pelamar');

        $registerResponse = $this->get(route('career.register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Daftar Akun Pelamar');
    }

    public function test_user_type_distinction(): void
    {
        $candidateUser = User::factory()->create(['user_type' => 'CANDIDATE']);
        $internalUser = User::factory()->create(['user_type' => 'INTERNAL']);

        $this->assertTrue($candidateUser->isCandidate());
        $this->assertFalse($candidateUser->isInternal());

        $this->assertTrue($internalUser->isInternal());
        $this->assertFalse($internalUser->isCandidate());
    }

    public function test_job_application_pipeline_stages_structure(): void
    {
        $this->assertArrayHasKey('APPLIED', JobApplication::STAGES);
        $this->assertArrayHasKey('SHORTLISTED', JobApplication::STAGES);
        $this->assertArrayHasKey('PSYCHOTEST_PASSED', JobApplication::STAGES);
        $this->assertArrayHasKey('INTERVIEW_HR', JobApplication::STAGES);
        $this->assertArrayHasKey('INTERVIEW_USER', JobApplication::STAGES);
        $this->assertArrayHasKey('INTERVIEW_BOD', JobApplication::STAGES);
        $this->assertArrayHasKey('MCU', JobApplication::STAGES);
        $this->assertArrayHasKey('OFFERING', JobApplication::STAGES);
        $this->assertArrayHasKey('HIRED', JobApplication::STAGES);
    }

    public function test_candidate_registration_creates_account_and_redirects(): void
    {
        $response = $this->post(route('career.register.submit'), [
            'name' => 'Budi Candidate',
            'email' => 'budi.candidate@example.com',
            'phone' => '081299998888',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('career.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi.candidate@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('CANDIDATE', $user->user_type);
        $this->assertEquals('081299998888', $user->phone);
    }

    public function test_candidate_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'login.test@example.com',
            'password' => bcrypt('password123'),
            'user_type' => 'CANDIDATE',
        ]);

        $loginResponse = $this->post(route('career.login.submit'), [
            'email' => 'login.test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect(route('career.dashboard'));
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post(route('career.logout'));
        $logoutResponse->assertRedirect(route('career.landing'));
        $this->assertGuest();
    }

    public function test_candidate_without_completed_profile_is_redirected_to_complete_data(): void
    {
        $user = User::factory()->create([
            'user_type' => 'CANDIDATE',
            'phone' => '081233334444',
        ]);

        $job = JobPosting::factory()->create([
            'title' => 'Product Designer',
            'status' => 'PUBLISHED',
        ]);

        $response = $this->actingAs($user)->post(route('career.jobs.apply', $job->slug));

        $response->assertRedirect(route('career.profile', ['job' => $job->slug]));
        $this->assertDatabaseMissing('job_applications', [
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_candidate_with_completed_profile_can_1_click_apply_to_job(): void
    {
        $user = User::factory()->create([
            'user_type' => 'CANDIDATE',
            'phone' => '081233334444',
            'resume_path' => 'candidate-cvs/test_cv.pdf',
        ]);

        CandidateProfile::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'gender' => 'Laki-laki',
            'birth_date' => '1995-01-01',
            'birth_place' => 'Jakarta',
            'phone_wa' => $user->phone,
            'domicile_address' => 'Jl. Tebet Raya No. 10',
            'cv_path' => 'candidate-cvs/test_cv.pdf',
            'agreement_signed' => true,
            'is_completed' => true,
        ]);

        $job = JobPosting::factory()->create([
            'title' => 'Product Designer',
            'status' => 'PUBLISHED',
        ]);

        $response = $this->actingAs($user)->post(route('career.jobs.apply', $job->slug));

        $response->assertRedirect(route('career.dashboard'));

        $this->assertDatabaseHas('job_applications', [
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'current_stage' => 'APPLIED',
            'applicant_name' => $user->name,
        ]);
    }

    public function test_candidate_can_fill_out_comprehensive_profile(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'user_type' => 'CANDIDATE',
            'phone' => '081288887777',
        ]);

        $viewResponse = $this->actingAs($user)->get(route('career.profile'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Formulir Kelengkapan Data Pelamar Kerja');

        $cvFile = UploadedFile::fake()->create('curriculum_vitae.pdf', 150, 'application/pdf');

        $updateResponse = $this->actingAs($user)->post(route('career.profile.update'), [
            'full_name' => 'Budi Santoso',
            'gender' => 'Laki-laki',
            'birth_date' => '1996-05-12',
            'birth_place' => 'Jakarta',
            'phone_wa' => '081288887777',
            'domicile_address' => 'Jl. Tebet Raya No 10, Jakarta Selatan',
            'cv' => $cvFile,
            'agreement_signed' => '1',
            // Optional fields can be present or absent
            'nickname' => 'Budi',
            'ktp_address' => 'Jl. Merdeka No 45, Jakarta Selatan',
            'expected_salary' => 15000000,
        ]);

        $updateResponse->assertRedirect(route('career.dashboard'));

        $this->assertDatabaseHas('candidate_profiles', [
            'user_id' => $user->id,
            'full_name' => 'Budi Santoso',
            'nickname' => 'Budi',
            'gender' => 'Laki-laki',
            'is_completed' => true,
        ]);
    }

    public function test_candidate_can_access_psychotests_hub(): void
    {
        $user = User::factory()->create(['user_type' => 'CANDIDATE']);
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'applicant_name' => 'Budi Santoso',
            'applicant_email' => $user->email,
            'applicant_phone' => '081234567890',
            'current_stage' => 'SHORTLISTED',
            'applied_at' => now(),
        ]);

        $kraepelin = Psychotest::create([
            'title' => 'Tes Kraepelin Numerik',
            'test_type' => Psychotest::TYPE_KRAEPELIN,
            'passing_score' => 70,
            'duration_minutes' => 5,
            'is_active' => true,
            'questions_data' => ['columns_count' => 6, 'seconds_per_column' => 20, 'rows_per_column' => 25],
        ]);

        $response = $this->actingAs($user)->get(route('career.psychotests.index', $application->id));

        $response->assertStatus(200);
        $response->assertSee('Tes Kraepelin Numerik');
        $response->assertSee('KRAEPELIN');
        $response->assertSee('Belum Dikerjakan');
    }

    public function test_candidate_can_take_and_submit_kraepelin_test(): void
    {
        $user = User::factory()->create(['user_type' => 'CANDIDATE']);
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'applicant_name' => 'Citra Handayani',
            'applicant_email' => $user->email,
            'applicant_phone' => '081234567891',
            'current_stage' => 'SHORTLISTED',
            'applied_at' => now(),
        ]);

        $kraepelin = Psychotest::create([
            'title' => 'Tes Kraepelin Kecepatan & Ketelitian',
            'test_type' => Psychotest::TYPE_KRAEPELIN,
            'passing_score' => 70,
            'duration_minutes' => 5,
            'is_active' => true,
            'questions_data' => ['columns_count' => 6, 'seconds_per_column' => 20, 'rows_per_column' => 25],
        ]);

        // Render Kraepelin page
        $pageResponse = $this->actingAs($user)
            ->get(route('career.psychotests.show', [$application->id, $kraepelin->id]));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Simulasi Tes Kraepelin');
        $pageResponse->assertSee('Virtual Keypad');

        // Submit Kraepelin results
        $submitResponse = $this->actingAs($user)
            ->post(route('career.psychotests.submit', [$application->id, $kraepelin->id]), [
                'total_attempted' => 90,
                'correct_count' => 85,
                'incorrect_count' => 5,
                'columns_completed' => 6,
                'column_details' => [
                    ['column_index' => 0, 'rows_attempted' => 15],
                    ['column_index' => 1, 'rows_attempted' => 15],
                ],
            ]);

        $submitResponse->assertRedirect(route('career.psychotests.index', $application->id));
        $submitResponse->assertSessionHas('success');

        $this->assertDatabaseHas('candidate_psychotest_results', [
            'job_application_id' => $application->id,
            'psychotest_id' => $kraepelin->id,
            'result_status' => 'PASSED',
        ]);
    }

    public function test_candidate_can_take_and_submit_likert_personality_test(): void
    {
        $user = User::factory()->create(['user_type' => 'CANDIDATE']);
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'applicant_name' => 'Dewi Lestari',
            'applicant_email' => $user->email,
            'applicant_phone' => '081234567892',
            'current_stage' => 'SHORTLISTED',
            'applied_at' => now(),
        ]);

        $likert = Psychotest::create([
            'title' => 'Tes Kepribadian Sikap Kerja (Likert 1-5)',
            'test_type' => Psychotest::TYPE_LIKERT_PERSONALITY,
            'passing_score' => 70,
            'duration_minutes' => 15,
            'is_active' => true,
            'questions_data' => [
                'scale' => [1 => 'STS', 2 => 'TS', 3 => 'N', 4 => 'S', 5 => 'SS'],
                'questions' => [
                    ['id' => 1, 'dimension' => 'Integritas', 'statement' => 'Saya mematuhi etika kerja.'],
                    ['id' => 2, 'dimension' => 'Kerjasama Tim', 'statement' => 'Saya senang membantu rekan tim.'],
                    ['id' => 3, 'dimension' => 'Adaptabilitas', 'statement' => 'Saya fleksibel menghadapi perubahan.'],
                ],
            ],
        ]);

        // Render page
        $pageResponse = $this->actingAs($user)
            ->get(route('career.psychotests.show', [$application->id, $likert->id]));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Saya mematuhi etika kerja.');
        $pageResponse->assertSee('Saya senang membantu rekan tim.');

        // Submit 1-5 answers
        $submitResponse = $this->actingAs($user)
            ->post(route('career.psychotests.submit', [$application->id, $likert->id]), [
                'answers' => [
                    1 => 5, // Sangat Sesuai
                    2 => 4, // Sesuai
                    3 => 5, // Sangat Sesuai
                ],
            ]);

        $submitResponse->assertRedirect(route('career.psychotests.index', $application->id));

        $this->assertDatabaseHas('candidate_psychotest_results', [
            'job_application_id' => $application->id,
            'psychotest_id' => $likert->id,
            'result_status' => 'PASSED',
        ]);
    }

    public function test_candidate_cannot_access_other_candidates_psychotests(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $job = JobPosting::factory()->create();

        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $owner->id,
            'applicant_name' => 'Owner Name',
            'applicant_email' => $owner->email,
            'applicant_phone' => '081234567893',
            'current_stage' => 'SHORTLISTED',
            'applied_at' => now(),
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('career.psychotests.index', $application->id));

        $response->assertStatus(403);
    }

    public function test_candidate_can_take_and_submit_general_psychotest(): void
    {
        $user = User::factory()->create(['user_type' => 'CANDIDATE']);
        $job = JobPosting::factory()->create();
        $application = JobApplication::create([
            'job_posting_id' => $job->id,
            'user_id' => $user->id,
            'applicant_name' => 'General Candidate',
            'applicant_email' => $user->email,
            'applicant_phone' => '081234567894',
            'current_stage' => 'SHORTLISTED',
            'applied_at' => now(),
        ]);

        $generalTest = Psychotest::create([
            'title' => 'Tes Logika Penalaran & Numerik',
            'test_type' => Psychotest::TYPE_GENERAL,
            'passing_score' => 60,
            'duration_minutes' => 30,
            'is_active' => true,
            'questions_data' => [
                [
                    'id' => 1,
                    'question' => 'Berapakah 5 + 5?',
                    'options' => ['A' => '10', 'B' => '12'],
                    'correct_answer' => 'A',
                    'score_weight' => 50,
                ],
                [
                    'id' => 2,
                    'question' => 'Berapakah 10 x 2?',
                    'options' => ['A' => '20', 'B' => '15'],
                    'correct_answer' => 'A',
                    'score_weight' => 50,
                ],
            ],
        ]);

        $pageResponse = $this->actingAs($user)
            ->get(route('career.psychotests.show', [$application->id, $generalTest->id]));

        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Tes Logika Penalaran & Numerik');
        $pageResponse->assertSee('Berapakah 5 + 5?');

        $submitResponse = $this->actingAs($user)
            ->post(route('career.psychotests.submit', [$application->id, $generalTest->id]), [
                'answers' => [
                    1 => 'A',
                    2 => 'A',
                ],
            ]);

        $submitResponse->assertRedirect(route('career.psychotests.index', $application->id));

        $this->assertDatabaseHas('candidate_psychotest_results', [
            'job_application_id' => $application->id,
            'psychotest_id' => $generalTest->id,
            'total_score' => 100,
            'result_status' => 'PASSED',
        ]);
    }
}
