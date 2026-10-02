<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveAndSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Employee $employee;

    protected Employee $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $dept = Department::factory()->create(['name' => 'Engineering']);
        $mgrPosition = Position::factory()->create(['title' => 'Engineering Manager', 'department_id' => $dept->id]);
        $staffPosition = Position::factory()->create(['title' => 'Software Engineer', 'department_id' => $dept->id]);

        $this->manager = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $mgrPosition->id,
            'full_name' => 'Budi Supervisor',
        ]);

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $dept->id,
            'position_id' => $staffPosition->id,
            'manager_id' => $this->manager->id,
            'full_name' => 'Kurnia Staff',
        ]);
    }

    public function test_can_view_leave_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('leaves.index'));
        $response->assertOk();
        $response->assertSee('Pengajuan Cuti & Izin', false);
    }

    public function test_employee_can_submit_leave_request(): void
    {
        $response = $this->actingAs($this->user)->post(route('leaves.store'), [
            'employee_id' => $this->employee->id,
            'type' => 'ANNUAL',
            'start_date' => '2026-10-05', // Senin
            'end_date' => '2026-10-07',   // Rabu (3 hari kerja)
            'reason' => 'Keperluan keluarga di luar kota',
        ]);

        $response->assertRedirect(route('leaves.index'));
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'type' => 'ANNUAL',
            'total_days' => 3,
            'status' => 'PENDING',
        ]);
    }

    public function test_approving_leave_automatically_updates_attendance_calendar_to_leave(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'type' => 'ANNUAL',
            'start_date' => '2026-10-05', // Senin
            'end_date' => '2026-10-07',   // Rabu
            'total_days' => 3,
            'reason' => 'Cuti tahunan',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->user)->post(route('leaves.approve', $leave));

        $response->assertRedirect(route('leaves.index'));

        // Pastikan status permohonan menjadi APPROVED
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'APPROVED',
        ]);

        // Pastikan tabel attendances otomatis terisi status LEAVE untuk setiap hari kerja
        $att1 = Attendance::where('employee_id', $this->employee->id)->whereDate('date', '2026-10-05')->first();
        $att2 = Attendance::where('employee_id', $this->employee->id)->whereDate('date', '2026-10-06')->first();
        $att3 = Attendance::where('employee_id', $this->employee->id)->whereDate('date', '2026-10-07')->first();

        $this->assertNotNull($att1);
        $this->assertEquals('LEAVE', $att1->status);
        $this->assertEquals(0, $att1->late_minutes);

        $this->assertNotNull($att2);
        $this->assertEquals('LEAVE', $att2->status);

        $this->assertNotNull($att3);
        $this->assertEquals('LEAVE', $att3->status);
    }

    public function test_manager_can_reject_leave_request_with_note(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'type' => 'ANNUAL',
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-06',
            'total_days' => 2,
            'reason' => 'Liburan',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($this->user)->post(route('leaves.reject', $leave), [
            'rejection_note' => 'Beban pekerjaan sprint release sedang tinggi',
        ]);

        $response->assertRedirect(route('leaves.index'));

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'REJECTED',
            'rejection_note' => 'Beban pekerjaan sprint release sedang tinggi',
        ]);

        // Tidak boleh ada baris LEAVE di tabel attendances jika ditolak
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->employee->id,
            'date' => '2026-10-05',
        ]);
    }

    public function test_attendance_summary_reports_monthly_leave_and_overtime(): void
    {
        // Masukkan presensi LEAVE hasil cuti
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-10-05',
            'status' => 'LEAVE',
        ]);
        Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-10-06',
            'status' => 'LEAVE',
        ]);

        // Masukkan lembur disetujui
        OvertimeRequest::create([
            'employee_id' => $this->employee->id,
            'date' => '2026-10-08',
            'start_time' => '17:00:00',
            'end_time' => '20:00:00',
            'total_hours' => 3.0,
            'reason' => 'Deploy hotfix',
            'status' => 'APPROVED',
        ]);

        $response = $this->actingAs($this->user)->get(route('attendance.summary', ['month' => '2026-10']));

        $response->assertOk();
        $response->assertSee('Rekapitulasi Presensi & Lembur', false);
        $response->assertSee('Kurnia Staff');
        // Harus ada angka cuti 2 hari dan lembur 3.0 jam
        $response->assertSee('3.0 jam');
    }
}
