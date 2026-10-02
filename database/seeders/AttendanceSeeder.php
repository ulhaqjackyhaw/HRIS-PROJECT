<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\OfficeLocation;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Lokasi Kantor / Geofencing
        $ho = OfficeLocation::firstOrCreate(
            ['name' => 'Kantor Pusat Jakarta (Head Office)'],
            [
                'latitude' => -6.2087634,
                'longitude' => 106.8455990,
                'radius_meters' => 150,
                'is_active' => true,
            ]
        );

        $bdg = OfficeLocation::firstOrCreate(
            ['name' => 'Cabang Bandung Tech Hub'],
            [
                'latitude' => -6.9174640,
                'longitude' => 107.6191230,
                'radius_meters' => 100,
                'is_active' => true,
            ]
        );

        // 2. Master Shift
        $shiftReg = Shift::firstOrCreate(
            ['code' => 'SHIFT-REGULER'],
            [
                'name' => 'Shift Reguler Normal (08:00 - 17:00)',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'late_tolerance_minutes' => 15,
                'is_overnight' => false,
            ]
        );

        $shiftPagi = Shift::firstOrCreate(
            ['code' => 'SHIFT-PAGI'],
            [
                'name' => 'Shift Pagi Operasional (07:00 - 15:30)',
                'start_time' => '07:00:00',
                'end_time' => '15:30:00',
                'late_tolerance_minutes' => 10,
                'is_overnight' => false,
            ]
        );

        $shiftMalam = Shift::firstOrCreate(
            ['code' => 'SHIFT-MALAM'],
            [
                'name' => 'Shift Malam Overnight (22:00 - 06:00)',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'late_tolerance_minutes' => 10,
                'is_overnight' => true,
            ]
        );

        // 3. Buat Jadwal Kerja (Employee Schedule) Hari Ini & 7 Hari ke Depan
        $employees = Employee::where('is_active', true)->get();
        $today = Carbon::today();

        foreach ($employees as $employee) {
            for ($i = -3; $i <= 7; $i++) {
                $date = $today->copy()->addDays($i);
                $isWeekend = $date->isWeekend();

                EmployeeSchedule::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'shift_id' => $isWeekend ? null : $shiftReg->id,
                        'office_location_id' => $isWeekend ? null : $ho->id,
                        'is_day_off' => $isWeekend,
                    ]
                );
            }
        }

        // 4. Buat sampel data absensi kemarin
        $yesterday = $today->copy()->subDay();
        if (! $yesterday->isWeekend() && $employees->isNotEmpty()) {
            foreach ($employees as $emp) {
                Attendance::firstOrCreate(
                    [
                        'employee_id' => $emp->id,
                        'date' => $yesterday->toDateString(),
                    ],
                    [
                        'shift_id' => $shiftReg->id,
                        'clock_in' => $yesterday->copy()->setTime(7, 55, 0),
                        'clock_in_lat' => -6.2087600,
                        'clock_in_lng' => 106.8456000,
                        'clock_in_distance_meters' => 12.5,
                        'clock_out' => $yesterday->copy()->setTime(17, 10, 0),
                        'clock_out_lat' => -6.2087610,
                        'clock_out_lng' => 106.8456010,
                        'clock_out_distance_meters' => 14.2,
                        'status' => 'PRESENT',
                        'late_minutes' => 0,
                        'early_leave_minutes' => 0,
                        'total_work_minutes' => 555,
                    ]
                );
            }
        }
    }
}
