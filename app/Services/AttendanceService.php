<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Holiday;
use App\Models\OfficeLocation;
use App\Models\OvertimeRequest;
use App\Models\Shift;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * Menghitung jarak antara 2 titik koordinat (meter) menggunakan Rumus Haversine.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Memproses Clock-In Karyawan
     *
     * @param  UploadedFile|string|null  $photo
     *
     * @throws Exception
     */
    public function processClockIn(Employee $employee, float $userLat, float $userLng, mixed $photo = null): Attendance
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Cek apakah sudah absen masuk hari ini
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->clock_in) {
            throw new Exception('Anda sudah melakukan Clock-In hari ini.');
        }

        // 2. Ambil jadwal kerja karyawan hari ini
        $schedule = EmployeeSchedule::with(['shift', 'officeLocation'])
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $location = $schedule?->officeLocation ?? OfficeLocation::where('is_active', true)->first();
        if (! $location) {
            throw new Exception('Titik lokasi kantor belum dikonfigurasi.');
        }

        // Cek apakah hari ini adalah Hari Libur Nasional / Cuti Bersama
        $holiday = Holiday::getHolidayForDate($today, $location->id);
        $isHolidayWork = false;
        $holidayId = null;

        if ($holiday) {
            $isHolidayWork = true;
            $holidayId = $holiday->id;
            $shift = $schedule?->shift ?? Shift::first();
        } else {
            if (! $schedule || $schedule->is_day_off || ! $schedule->shift) {
                throw new Exception('Hari ini adalah hari libur atau Anda tidak memiliki jadwal kerja.');
            }
            $shift = $schedule->shift;
        }

        // 3. Validasi Geofencing (Radius)
        $distance = $this->calculateDistance($userLat, $userLng, (float) $location->latitude, (float) $location->longitude);
        if ($distance > $location->radius_meters) {
            throw new Exception("Posisi Anda berada di luar radius kantor ({$distance} meter dari titik {$location->name}. Maksimal radius {$location->radius_meters} meter).");
        }

        // 4. Simpan foto selfie (mendukung file upload ataupun data URL base64)
        $photoPath = $this->storePhoto($photo, 'attendances/clock_in');

        // 5. Hitung Keterlambatan
        $shiftStartTime = Carbon::parse($today.' '.($shift?->start_time ?? '08:00:00'));
        $toleranceMinutes = $shift?->late_tolerance_minutes ?? 15;
        $maxOnTime = $shiftStartTime->copy()->addMinutes($toleranceMinutes);

        $lateMinutes = 0;
        $status = 'PRESENT';

        if (! $isHolidayWork && $now->greaterThan($maxOnTime)) {
            $lateMinutes = (int) round($shiftStartTime->diffInMinutes($now));
            $status = 'LATE';
        }

        // 6. Simpan / perbarui ke database
        return Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'shift_id' => $shift?->id,
                'clock_in' => $now,
                'clock_in_lat' => $userLat,
                'clock_in_lng' => $userLng,
                'clock_in_distance_meters' => $distance,
                'clock_in_photo_path' => $photoPath,
                'status' => $status,
                'is_holiday_work' => $isHolidayWork,
                'holiday_id' => $holidayId,
                'late_minutes' => (int) $lateMinutes,
            ]
        );
    }

    /**
     * Memproses Clock-Out Karyawan
     *
     * @param  UploadedFile|string|null  $photo
     *
     * @throws Exception
     */
    public function processClockOut(Employee $employee, float $userLat, float $userLng, mixed $photo = null): Attendance
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        $attendance = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance || ! $attendance->clock_in) {
            throw new Exception('Anda belum melakukan Clock-In hari ini.');
        }

        if ($attendance->clock_out) {
            throw new Exception('Anda sudah melakukan Clock-Out hari ini.');
        }

        // Cek Geofencing saat clock out jika lokasi tersedia
        $schedule = EmployeeSchedule::with('officeLocation')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $location = $schedule?->officeLocation ?? OfficeLocation::where('is_active', true)->first();
        $distance = $location
            ? $this->calculateDistance($userLat, $userLng, (float) $location->latitude, (float) $location->longitude)
            : null;

        $photoPath = $this->storePhoto($photo, 'attendances/clock_out');

        // Hitung total jam kerja
        $clockInTime = Carbon::parse($attendance->clock_in);
        $totalWorkMinutes = (int) round($clockInTime->diffInMinutes($now));

        // Cek pulang cepat (Early Leave)
        $earlyLeaveMinutes = 0;
        $status = $attendance->status;

        if ($attendance->shift) {
            $shiftEndTime = Carbon::parse($today.' '.$attendance->shift->end_time);
            if ($now->lessThan($shiftEndTime)) {
                $earlyLeaveMinutes = (int) round($now->diffInMinutes($shiftEndTime));
                if ($status === 'PRESENT') {
                    $status = 'EARLY_LEAVE';
                }
            }
        }

        $attendance->update([
            'clock_out' => $now,
            'clock_out_lat' => $userLat,
            'clock_out_lng' => $userLng,
            'clock_out_distance_meters' => $distance,
            'clock_out_photo_path' => $photoPath,
            'total_work_minutes' => (int) $totalWorkMinutes,
            'early_leave_minutes' => (int) $earlyLeaveMinutes,
            'status' => $status,
        ]);

        // Jika kehadiran dilakukan di hari libur nasional / cuti bersama, buat otomatis permohonan lembur hari libur
        if ($attendance->is_holiday_work || $attendance->holiday_id) {
            $holiday = $attendance->holiday ?? Holiday::find($attendance->holiday_id);
            $holidayName = $holiday?->name ?? 'Hari Libur Nasional / Cuti Bersama';
            $hours = round($totalWorkMinutes / 60, 2);

            OvertimeRequest::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $today,
                ],
                [
                    'start_time' => $attendance->clock_in,
                    'end_time' => $now,
                    'total_hours' => $hours,
                    'reason' => "[Otomatis] Kerja di Hari Libur: {$holidayName} ({$hours} jam kerja aktual)",
                    'status' => 'PENDING',
                ]
            );
        }

        return $attendance;
    }

    /**
     * Helper untuk menyimpan file atau base64 gambar
     */
    protected function storePhoto(mixed $photo, string $directory): ?string
    {
        if (! $photo) {
            return null;
        }

        if ($photo instanceof UploadedFile) {
            return $photo->store($directory, 'public');
        }

        // Handle data URL base64 format (e.g. data:image/jpeg;base64,...)
        if (is_string($photo) && str_starts_with($photo, 'data:image')) {
            $parts = explode(',', $photo, 2);
            if (count($parts) === 2) {
                $binary = base64_decode($parts[1]);
                $filename = $directory.'/selfie_'.Str::random(20).'.jpg';
                Storage::disk('public')->put($filename, $binary);

                return $filename;
            }
        }

        return null;
    }
}
