<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceModuleSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------
        // 1. Data User & Karyawan Dummy (untuk pengujian login)
        // -------------------------------------------------------------
        $user = User::firstOrCreate(
            ['email' => 'karyawan@hris.local'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $department = Department::firstOrCreate(
            ['code' => 'IT-ENG'],
            [
                'name' => 'IT & Software Engineering',
                'description' => 'Divisi Rekayasa Perangkat Lunak',
                'is_active' => true,
            ]
        );

        $position = Position::firstOrCreate(
            ['code' => 'SE-MID'],
            [
                'department_id' => $department->id,
                'title' => 'Software Engineer',
                'level' => 'Senior Staff',
                'is_active' => true,
            ]
        );

        $employee = Employee::firstOrCreate(
            ['nik' => 'EMP-2026-001'],
            [
                'user_id' => $user->id,
                'department_id' => $department->id,
                'position_id' => $position->id,
                'full_name' => 'Budi Santoso',
                'email' => 'karyawan@hris.local',
                'phone_number' => '081234567890',
                'gender' => 'MALE',
                'birth_date' => '1998-05-15',
                'join_date' => '2024-01-01',
                'employment_status' => 'PKWTT',
                'work_location' => 'Kantor Pusat',
                'ktp_number' => '3374011505980001',
                'marital_status' => 'SINGLE',
                'ptkp_status' => 'TK/0',
                'is_active' => true,
                'lifecycle_stage' => 'ACTIVE',
            ]
        );

        // -------------------------------------------------------------
        // 2. Data Master Titik Lokasi Kantor (Geofencing)
        // -------------------------------------------------------------
        $headOffice = OfficeLocation::firstOrCreate(
            ['name' => 'Kantor Pusat (HQ)'],
            [
                'latitude' => -6.20000000,    // Contoh koordinat
                'longitude' => 106.81666600,
                'radius_meters' => 100,      // Toleransi radius 100 meter
                'is_active' => true,
            ]
        );

        OfficeLocation::firstOrCreate(
            ['name' => 'Cabang Gudang & Operasional'],
            [
                'latitude' => -6.17539240,
                'longitude' => 106.82715280,
                'radius_meters' => 150,
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 3. Data Master Shift Kerja
        // -------------------------------------------------------------
        $shiftPagi = Shift::firstOrCreate(
            ['code' => 'SHIFT-PAGI'],
            [
                'name' => 'Office Reguler (Pagi)',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'late_tolerance_minutes' => 15, // Keterlambatan toleransi 15 menit
                'is_overnight' => false,
            ]
        );

        $shiftSiang = Shift::firstOrCreate(
            ['code' => 'SHIFT-SIANG'],
            [
                'name' => 'Operasional (Siang)',
                'start_time' => '13:00:00',
                'end_time' => '21:00:00',
                'late_tolerance_minutes' => 10,
                'is_overnight' => false,
            ]
        );

        $shiftMalam = Shift::firstOrCreate(
            ['code' => 'SHIFT-MALAM'],
            [
                'name' => 'Security & Server Shift (Malam)',
                'start_time' => '21:00:00',
                'end_time' => '06:00:00',
                'late_tolerance_minutes' => 10,
                'is_overnight' => true,
            ]
        );

        // -------------------------------------------------------------
        // 4. Jadwal Kerja Karyawan (Roster Hari Ini & 7 Hari ke Depan)
        // -------------------------------------------------------------
        $today = Carbon::today();

        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $isWeekend = $date->isWeekend();

            EmployeeSchedule::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                ],
                [
                    'shift_id' => $isWeekend ? null : $shiftPagi->id,
                    'office_location_id' => $isWeekend ? null : $headOffice->id,
                    'is_day_off' => $isWeekend, // Libur di hari Sabtu & Minggu
                ]
            );
        }
    }
}
