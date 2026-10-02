<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeEducation;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrisMvcTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_dashboard_renders_metrics_and_alerts(): void
    {
        $department = Department::factory()->create(['name' => 'Human Capital']);
        $position = Position::factory()->create(['department_id' => $department->id, 'title' => 'HR Specialist']);
        $employee = Employee::factory()->create([
            'department_id' => $department->id,
            'position_id' => $position->id,
            'full_name' => 'Budi Santoso',
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Eksekutif HRIS');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Human Capital');
    }

    public function test_departments_crud_flow(): void
    {
        // 1. Index
        $response = $this->get(route('departments.index'));
        $response->assertStatus(200);

        // 2. Create Page
        $response = $this->get(route('departments.create'));
        $response->assertStatus(200);

        // 3. Store
        $response = $this->post(route('departments.store'), [
            'code' => 'OPS',
            'name' => 'Operations Unit',
            'description' => 'Operations Department',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['code' => 'OPS', 'name' => 'Operations Unit']);

        $department = Department::where('code', 'OPS')->firstOrFail();

        // 4. Edit Page
        $response = $this->get(route('departments.edit', $department));
        $response->assertStatus(200);
        $response->assertSee('Operations Unit');

        // 5. Update
        $response = $this->put(route('departments.update', $department), [
            'code' => 'OPS',
            'name' => 'Operations & Supply Chain',
            'description' => 'Updated Description',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['code' => 'OPS', 'name' => 'Operations & Supply Chain']);

        // 6. Delete
        $response = $this->delete(route('departments.destroy', $department));
        $response->assertRedirect(route('departments.index'));
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    public function test_positions_crud_flow(): void
    {
        $department = Department::factory()->create();

        // 1. Index
        $response = $this->get(route('positions.index'));
        $response->assertStatus(200);

        // 2. Store
        $response = $this->post(route('positions.store'), [
            'department_id' => $department->id,
            'code' => 'POS-DEV-01',
            'title' => 'Software Engineer',
            'level' => 'Staff',
            'career_path' => 'Spesialis',
            'job_function' => 'IT Engineering',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseHas('positions', ['code' => 'POS-DEV-01', 'title' => 'Software Engineer']);

        $position = Position::where('code', 'POS-DEV-01')->firstOrFail();

        // 3. Update
        $response = $this->put(route('positions.update', $position), [
            'department_id' => $department->id,
            'code' => 'POS-DEV-01',
            'title' => 'Senior Software Engineer',
            'level' => 'Senior Staff',
            'career_path' => 'Spesialis',
            'job_function' => 'IT Engineering',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('positions.index'));
        $this->assertDatabaseHas('positions', ['title' => 'Senior Software Engineer']);

        // 4. Delete
        $response = $this->delete(route('positions.destroy', $position));
        $response->assertRedirect(route('positions.index'));
        $this->assertSoftDeleted('positions', ['id' => $position->id]);
    }

    public function test_employees_crud_flow(): void
    {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        // 1. Index
        $response = $this->get(route('employees.index'));
        $response->assertStatus(200);

        // 2. Create Page
        $response = $this->get(route('employees.create'));
        $response->assertStatus(200);

        // 3. Store
        $response = $this->post(route('employees.store'), [
            'department_id' => $department->id,
            'position_id' => $position->id,
            'nik' => 'EMP2026999',
            'employment_status' => 'PKWT',
            'join_date' => '2024-01-10',
            'end_date' => '2026-12-31',
            'current_contract_no' => 'CTR/2026/001',
            'work_location' => 'Head Office Jakarta',
            'is_active' => '1',
            'ktp_number' => '3273000000000099',
            'full_name' => 'Ahmad Fadhil',
            'gender' => 'MALE',
            'birth_date' => '1995-05-15',
            'religion' => 'ISLAM',
            'marital_status' => 'SINGLE',
            'email' => 'ahmad.fadhil@hris.corp',
            'phone_number' => '081234567899',
            'ptkp_status' => 'TK/0',
            'npwp' => '12.345.678.9-001.000',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567899',
            'bank_account_holder' => 'Ahmad Fadhil',
            'custom_fields' => ['blood_type' => 'O'],
        ]);

        $employee = Employee::where('nik', 'EMP2026999')->firstOrFail();
        $response->assertRedirect(route('employees.show', $employee));

        // Kontrak awal otomatis tercatat
        $this->assertDatabaseHas('employee_contracts', [
            'employee_id' => $employee->id,
            'contract_number' => 'CTR/2026/001',
        ]);

        // 4. Show Page
        $response = $this->get(route('employees.show', $employee));
        $response->assertStatus(200);
        $response->assertSee('Ahmad Fadhil');
        $response->assertSee('TK/0');
        $response->assertSee('CTR/2026/001');

        // 5. Edit Page
        $response = $this->get(route('employees.edit', $employee));
        $response->assertStatus(200);

        // 6. Update
        $response = $this->put(route('employees.update', $employee), [
            'department_id' => $department->id,
            'position_id' => $position->id,
            'nik' => 'EMP2026999',
            'employment_status' => 'PKWTT',
            'join_date' => '2024-01-10',
            'work_location' => 'Cabang Surabaya',
            'is_active' => '1',
            'ktp_number' => '3273000000000099',
            'full_name' => 'Ahmad Fadhil Prasetya',
            'gender' => 'MALE',
            'birth_date' => '1995-05-15',
            'email' => 'ahmad.fadhil@hris.corp',
            'ptkp_status' => 'TK/0',
        ]);

        $response->assertRedirect(route('employees.show', $employee));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'full_name' => 'Ahmad Fadhil Prasetya',
            'employment_status' => 'PKWTT',
            'work_location' => 'Cabang Surabaya',
        ]);

        // 7. Delete (Soft delete)
        $response = $this->delete(route('employees.destroy', $employee));
        $response->assertRedirect(route('employees.index'));
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_employee_contract_and_education_management(): void
    {
        $employee = Employee::factory()->create();

        // 1. Add Education
        $response = $this->post(route('employees.educations.store', $employee), [
            'education_level' => 'S1',
            'major' => 'Sistem Informasi',
            'institution_name' => 'Institut Teknologi Bandung',
            'graduation_year' => 2019,
            'is_recognized' => '1',
        ]);
        $response->assertRedirect(route('employees.show', $employee));
        $this->assertDatabaseHas('employee_educations', [
            'employee_id' => $employee->id,
            'major' => 'Sistem Informasi',
        ]);

        $education = EmployeeEducation::where('employee_id', $employee->id)->firstOrFail();

        // Delete Education
        $response = $this->delete(route('employees.educations.destroy', [$employee, $education]));
        $response->assertRedirect(route('employees.show', $employee));
        $this->assertDatabaseMissing('employee_educations', ['id' => $education->id]);

        // 2. Add Contract Extension
        $response = $this->post(route('employees.contracts.store', $employee), [
            'contract_number' => 'CTR/EXT/002',
            'contract_type' => 'PKWT-2',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'notes' => 'Perpanjangan kontrak tahun ke-2',
        ]);
        $response->assertRedirect(route('employees.show', $employee));
        $this->assertDatabaseHas('employee_contracts', [
            'employee_id' => $employee->id,
            'contract_number' => 'CTR/EXT/002',
            'contract_type' => 'PKWT-2',
        ]);

        $contract = EmployeeContract::where('contract_number', 'CTR/EXT/002')->firstOrFail();

        // Delete Contract
        $response = $this->delete(route('employees.contracts.destroy', [$employee, $contract]));
        $response->assertRedirect(route('employees.show', $employee));
    }

    public function test_employee_lifecycle_stage_and_career_movement_transitions(): void
    {
        $deptA = Department::factory()->create(['name' => 'Marketing']);
        $deptB = Department::factory()->create(['name' => 'Product Management']);

        $posStaff = Position::factory()->create(['department_id' => $deptA->id, 'title' => 'Marketing Officer', 'level' => 'Staff']);
        $posLead = Position::factory()->create(['department_id' => $deptB->id, 'title' => 'Product Lead', 'level' => 'Lead / Senior']);

        $employee = Employee::factory()->create([
            'department_id' => $deptA->id,
            'position_id' => $posStaff->id,
            'lifecycle_stage' => 'ONBOARDING',
            'is_active' => true,
        ]);

        // 1. Record Promotion & Mutation
        $response = $this->post(route('employees.careers.store', $employee), [
            'transition_type' => 'PROMOTION',
            'new_department_id' => $deptB->id,
            'new_position_id' => $posLead->id,
            'effective_date' => '2026-06-01',
            'reference_doc_no' => 'SK/DIR/2026/088',
            'notes' => 'Promosi sebagai Product Lead berdasarkan evaluasi kinerja',
            'lifecycle_stage' => 'ACTIVE',
        ]);

        $response->assertRedirect(route('employees.show', $employee));

        // Career history record created
        $this->assertDatabaseHas('employee_career_histories', [
            'employee_id' => $employee->id,
            'transition_type' => 'PROMOTION',
            'old_department_id' => $deptA->id,
            'new_department_id' => $deptB->id,
            'old_position_id' => $posStaff->id,
            'new_position_id' => $posLead->id,
            'reference_doc_no' => 'SK/DIR/2026/088',
        ]);

        // Employee current master data updated
        $employee->refresh();
        $this->assertEquals($deptB->id, $employee->department_id);
        $this->assertEquals($posLead->id, $employee->position_id);
        $this->assertEquals('ACTIVE', $employee->lifecycle_stage);

        // 2. Offboarding / Resignation transition
        $response = $this->post(route('employees.careers.store', $employee), [
            'transition_type' => 'RESIGN',
            'effective_date' => '2026-10-31',
            'reference_doc_no' => 'SK-EXIT-2026-012',
            'notes' => 'Pengunduran diri atas inisiatif pribadi, proses clearance berlangsung',
            'lifecycle_stage' => 'OFFBOARDING',
        ]);

        $response->assertRedirect(route('employees.show', $employee));
        $employee->refresh();
        $this->assertEquals('OFFBOARDING', $employee->lifecycle_stage);

        // 3. Employee list filtering by stage
        $response = $this->get(route('employees.index', ['stage' => 'OFFBOARDING']));
        $response->assertStatus(200);
        $response->assertSee($employee->full_name);
        $response->assertSee('OFFBOARDING');
    }
}
