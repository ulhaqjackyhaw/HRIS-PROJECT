<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeEducation;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_be_created_with_complete_attributes(): void
    {
        $employee = Employee::factory()->create([
            'nik' => 'EMP001',
            'employment_status' => 'PKWT',
            'ptkp_status' => 'TK/0',
            'npwp' => '12.345.678.9-012.000',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'John Doe',
            'bpjs_ketenagakerjaan_no' => '12345678901',
            'bpjs_kesehatan_no' => '98765432101',
            'custom_fields' => ['blood_type' => 'O'],
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nik' => 'EMP001',
            'ptkp_status' => 'TK/0',
            'npwp' => '12.345.678.9-012.000',
            'bank_name' => 'BCA',
        ]);

        $this->assertEquals('O', $employee->custom_fields['blood_type']);
    }

    public function test_dynamic_age_accessor(): void
    {
        // Set fixed test time
        Carbon::setTestNow('2026-10-02');

        $employee = Employee::factory()->create([
            'birth_date' => '2000-05-15',
        ]);

        $this->assertEquals(26, $employee->age);

        Carbon::setTestNow();
    }

    public function test_dynamic_tenure_accessor(): void
    {
        Carbon::setTestNow('2026-10-02');

        $employee = Employee::factory()->create([
            'join_date' => '2024-06-02',
        ]);

        $this->assertEquals('2 Tahun 4 Bulan', $employee->tenure);

        Carbon::setTestNow();
    }

    public function test_employee_relationships_with_department_position_educations_and_contracts(): void
    {
        $department = Department::factory()->create(['name' => 'Human Capital']);
        $position = Position::factory()->create([
            'department_id' => $department->id,
            'title' => 'HR Specialist',
            'career_path' => 'Spesialis',
        ]);

        $manager = Employee::factory()->create();

        $employee = Employee::factory()->create([
            'department_id' => $department->id,
            'position_id' => $position->id,
            'manager_id' => $manager->id,
        ]);

        $education = EmployeeEducation::factory()->create([
            'employee_id' => $employee->id,
            'education_level' => 'S1',
            'major' => 'Manajemen Bisnis',
            'institution_name' => 'Universitas Indonesia',
            'is_recognized' => true,
        ]);

        $contract = EmployeeContract::factory()->create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR/001/PKWT/2026',
            'contract_type' => 'PKWT-1',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]);

        $this->assertEquals('Human Capital', $employee->department->name);
        $this->assertEquals('HR Specialist', $employee->position->title);
        $this->assertEquals($manager->id, $employee->manager->id);
        $this->assertCount(1, $manager->subordinates);
        $this->assertCount(1, $employee->educations);
        $this->assertEquals('Universitas Indonesia', $employee->educations->first()->institution_name);
        $this->assertCount(1, $employee->contracts);
        $this->assertEquals('CTR/001/PKWT/2026', $employee->contracts->first()->contract_number);
    }
}
