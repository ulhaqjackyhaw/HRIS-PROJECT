<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModularSidebarTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);
        Employee::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
        ]);
    }

    public function test_sidebar_in_core_hr_shows_only_core_hr_features(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();

        // Must contain Core HR items
        $response->assertSee('Dashboard Core HR');
        $response->assertSee('Data Karyawan');
        $response->assertSee('Departemen / Unit');
        $response->assertSee('Jabatan & Posisi', false);

        // Must NOT contain Attendance items
        $response->assertDontSee('Presensi Selfie & GPS', false);
        $response->assertDontSee('Pengajuan Cuti & Izin', false);
        $response->assertDontSee('Rekapitulasi Presensi HR', false);
        $response->assertDontSee('Master Shift Kerja');
        $response->assertDontSee('Lokasi & Geofence', false);
        $response->assertDontSee('Roster & Jadwal', false);
        $response->assertDontSee('Lembur & Approval', false);
    }

    public function test_sidebar_in_attendance_shows_only_attendance_features(): void
    {
        $response = $this->actingAs($this->user)->get(route('attendance.index'));

        $response->assertOk();

        // Must contain Attendance items
        $response->assertSee('Presensi Selfie & GPS', false);
        $response->assertSee('Pengajuan Cuti & Izin', false);
        $response->assertSee('Monitoring Presensi');
        $response->assertSee('Rekapitulasi Presensi HR', false);
        $response->assertSee('Master Shift Kerja');
        $response->assertSee('Lokasi & Geofence', false);
        $response->assertSee('Roster & Jadwal', false);
        $response->assertSee('Lembur & Approval', false);

        // Must NOT contain Core HR items in the sidebar
        $response->assertDontSee('Dashboard Core HR');
        $response->assertDontSee('Jabatan & Posisi', false);
    }

    public function test_sidebar_in_payroll_shows_only_payroll_features(): void
    {
        $response = $this->actingAs($this->user)->get(route('payroll.index'));

        $response->assertOk();

        // Must contain Payroll items
        $response->assertSee('Periode & Batch Payroll', false);

        // Must NOT contain Core HR / Attendance main sidebar menus
        $response->assertDontSee('Data Karyawan');
        $response->assertDontSee('Presensi Selfie & GPS', false);
    }
}
