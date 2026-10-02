<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\OfficeLocation;
use App\Models\OvertimeRequest;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Employee $employee;

    protected OfficeLocation $location;

    protected Shift $shift;

    protected EmployeeSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $department = Department::factory()->create(['name' => 'Human Capital']);
        $position = Position::factory()->create(['department_id' => $department->id, 'title' => 'HR Staff']);

        $this->employee = Employee::factory()->create([
            'user_id' => $this->user->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'full_name' => 'Budi Santoso',
            'is_active' => true,
        ]);

        $this->location = OfficeLocation::create([
            'name' => 'Kantor Pusat Jakarta',
            'latitude' => -6.2087634,
            'longitude' => 106.8455990,
            'radius_meters' => 150,
            'is_active' => true,
        ]);

        $this->shift = Shift::create([
            'code' => 'SHIFT-REG',
            'name' => 'Shift Reguler 08:00 - 17:00',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_overnight' => false,
        ]);

        $this->schedule = EmployeeSchedule::create([
            'employee_id' => $this->employee->id,
            'shift_id' => $this->shift->id,
            'office_location_id' => $this->location->id,
            'date' => Carbon::today()->toDateString(),
            'is_day_off' => false,
        ]);

        $this->actingAs($this->user);
    }

    public function test_user_can_access_check_in_page(): void
    {
        $response = $this->get(route('attendance.check-in'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Kantor Pusat Jakarta');
        $response->assertSee('Shift Reguler');
    }

    public function test_clock_in_within_geofence_succeeds(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $photo = UploadedFile::fake()->image('selfie.jpg', 640, 480);

        // Koordinat tepat di titik kantor
        $response = $this->post(route('attendance.clock-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.2087634,
            'longitude' => 106.8455990,
            'photo' => $photo,
        ]);

        $response->assertRedirect(route('attendance.check-in'));

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'PRESENT',
            'late_minutes' => 0,
        ]);

        $attendance = Attendance::where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertEquals(Carbon::today()->toDateString(), $attendance->date->toDateString());
        $this->assertNotNull($attendance->clock_in);
        $this->assertNotNull($attendance->clock_in_photo_path);
        $this->assertLessThanOrEqual(5.0, $attendance->clock_in_distance_meters);
    }

    public function test_clock_in_outside_geofence_fails(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(7, 50, 0));

        $photo = UploadedFile::fake()->image('selfie.jpg');

        // Koordinat sangat jauh (misal di Bandung ~120km)
        $response = $this->post(route('attendance.clock-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.9174640,
            'longitude' => 107.6191230,
            'photo' => $photo,
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->employee->id,
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_clock_in_late_records_late_minutes(): void
    {
        // Masuk jam 08:35 (shift 08:00 + toleransi 15m = 08:15 max, terlambat 35 menit)
        Carbon::setTestNow(Carbon::today()->setTime(8, 35, 0));

        $photo = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->post(route('attendance.clock-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.2087634,
            'longitude' => 106.8455990,
            'photo' => $photo,
        ]);

        $response->assertRedirect(route('attendance.check-in'));

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'LATE',
            'late_minutes' => 35,
        ]);
    }

    public function test_clock_out_calculates_work_minutes_and_early_leave(): void
    {
        // 1. Clock In tepat waktu jam 08:00
        Carbon::setTestNow(Carbon::today()->setTime(8, 0, 0));
        $photoIn = UploadedFile::fake()->image('in.jpg');

        $this->post(route('attendance.clock-in'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.2087634,
            'longitude' => 106.8455990,
            'photo' => $photoIn,
        ]);

        // 2. Clock Out jam 16:30 (shift selesai 17:00, pulang cepat 30 menit, kerja 510 menit)
        Carbon::setTestNow(Carbon::today()->setTime(16, 30, 0));
        $photoOut = UploadedFile::fake()->image('out.jpg');

        $response = $this->post(route('attendance.clock-out'), [
            'employee_id' => $this->employee->id,
            'latitude' => -6.2087634,
            'longitude' => 106.8455990,
            'photo' => $photoOut,
        ]);

        $response->assertRedirect(route('attendance.check-in'));

        $attendance = Attendance::where('employee_id', $this->employee->id)->firstOrFail();
        $this->assertNotNull($attendance->clock_out);
        $this->assertEquals(510, $attendance->total_work_minutes);
        $this->assertEquals(30, $attendance->early_leave_minutes);
        $this->assertEquals('EARLY_LEAVE', $attendance->status);
    }

    public function test_shifts_crud_management(): void
    {
        // 1. Create Shift
        $response = $this->post(route('shifts.store'), [
            'code' => 'SHIFT-NIGHT',
            'name' => 'Shift Malam Overnight',
            'start_time' => '22:00',
            'end_time' => '06:00',
            'late_tolerance_minutes' => 10,
            'is_overnight' => '1',
        ]);
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseHas('shifts', ['code' => 'SHIFT-NIGHT']);

        $shift = Shift::where('code', 'SHIFT-NIGHT')->firstOrFail();

        // 2. Update Shift
        $response = $this->put(route('shifts.update', $shift), [
            'code' => 'SHIFT-NIGHT',
            'name' => 'Shift Malam Revisi',
            'start_time' => '22:00',
            'end_time' => '06:00',
            'late_tolerance_minutes' => 20,
            'is_overnight' => '1',
        ]);
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseHas('shifts', ['name' => 'Shift Malam Revisi', 'late_tolerance_minutes' => 20]);

        // 3. Delete Shift
        $response = $this->delete(route('shifts.destroy', $shift));
        $response->assertRedirect(route('shifts.index'));
        $this->assertDatabaseMissing('shifts', ['id' => $shift->id]);
    }

    public function test_office_locations_crud(): void
    {
        // 1. Create Location
        $response = $this->post(route('locations.store'), [
            'name' => 'Site Project Cikarang',
            'latitude' => -6.3000,
            'longitude' => 107.1500,
            'radius_meters' => 250,
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('office_locations', ['name' => 'Site Project Cikarang']);

        $location = OfficeLocation::where('name', 'Site Project Cikarang')->firstOrFail();
        // 2. Update Location
        $response = $this->put(route('locations.update', $location), [
            'name' => 'Site Project Cikarang Phase 2',
            'latitude' => -6.3000,
            'longitude' => 107.1500,
            'radius_meters' => 300,
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('office_locations', ['radius_meters' => 300]);

        // 3. Delete Location
        $response = $this->delete(route('locations.destroy', $location));
        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseMissing('office_locations', ['id' => $location->id]);
    }

    public function test_overtime_request_and_approval(): void
    {
        // 1. Submit Overtime
        $response = $this->post(route('overtimes.store'), [
            'employee_id' => $this->employee->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'reason' => 'Perbaikan bug production',
        ]);

        $response->assertRedirect(route('overtimes.index'));
        $this->assertDatabaseHas('overtime_requests', [
            'employee_id' => $this->employee->id,
            'total_hours' => 3.0,
            'status' => 'PENDING',
        ]);

        $overtime = OvertimeRequest::where('employee_id', $this->employee->id)->firstOrFail();

        // 2. Approve Overtime
        $response = $this->post(route('overtimes.approve', $overtime));
        $response->assertRedirect(route('overtimes.index'));
        $this->assertDatabaseHas('overtime_requests', [
            'id' => $overtime->id,
            'status' => 'APPROVED',
            'approved_by' => $this->employee->id,
        ]);
    }
}
