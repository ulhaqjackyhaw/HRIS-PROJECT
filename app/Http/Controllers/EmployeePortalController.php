<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\OfficeLocation;
use App\Models\OvertimeRequest;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Dapatkan profil Employee dari user login atau fallback karyawan aktif pertama (untuk demo).
     */
    protected function getEmployee(Request $request): Employee
    {
        $user = $request->user();

        $employee = Employee::with(['department', 'position', 'manager'])
            ->where('user_id', $user->id)
            ->first() ?? Employee::with(['department', 'position', 'manager'])
            ->where('is_active', true)
            ->first();

        if (! $employee) {
            $employee = new Employee([
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'employee_code' => 'EMP-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'remaining_annual_leave' => 12,
                'is_active' => true,
            ]);
            $employee->user_id = $user->id;
            $employee->exists = false;
        }

        return $employee;
    }

    /**
     * 1. Beranda / Dashboard Mandiri Karyawan
     */
    public function dashboard(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $schedule = EmployeeSchedule::with(['shift', 'officeLocation'])
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $recentAttendances = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->latest('date')
            ->take(5)
            ->get();

        $recentLeaves = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->latest()
            ->take(3)
            ->get();

        $recentOvertimes = OvertimeRequest::where('employee_id', $employee->id)
            ->latest()
            ->take(3)
            ->get();

        return view('employee.dashboard', compact(
            'user',
            'employee',
            'todayAttendance',
            'schedule',
            'recentAttendances',
            'recentLeaves',
            'recentOvertimes',
            'today'
        ));
    }

    /**
     * 2. Presensi Selfie & GPS Check-In
     */
    public function attendanceCheckIn(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $today = Carbon::today()->toDateString();

        $schedule = EmployeeSchedule::with(['shift', 'officeLocation'])
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $todayAttendance = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $officeLocations = OfficeLocation::where('is_active', true)->get();

        return view('employee.attendance.check-in', compact(
            'employee',
            'schedule',
            'todayAttendance',
            'officeLocations',
            'today'
        ));
    }

    /**
     * Eksekusi Clock-In Karyawan
     */
    public function clockIn(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_data' => ['nullable', 'string'],
        ]);

        $employee = $this->getEmployee($request);
        $photo = $request->file('photo') ?? $request->input('photo_data');

        if (! $photo) {
            return back()->withErrors(['message' => 'Foto selfie wajib diambil sebelum Clock-In.']);
        }

        try {
            $attendance = $this->attendanceService->processClockIn(
                $employee,
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
                $photo
            );

            $statusText = $attendance->status === 'LATE'
                ? "Clock-In berhasil! Anda tercatat terlambat {$attendance->late_minutes} menit."
                : 'Clock-In berhasil tepat waktu!';

            return redirect()->route('employee.attendance.check-in')
                ->with('success', $statusText);
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Eksekusi Clock-Out Karyawan
     */
    public function clockOut(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_data' => ['nullable', 'string'],
        ]);

        $employee = $this->getEmployee($request);
        $photo = $request->file('photo') ?? $request->input('photo_data');

        try {
            $attendance = $this->attendanceService->processClockOut(
                $employee,
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
                $photo
            );

            return redirect()->route('employee.attendance.check-in')
                ->with('success', "Clock-Out berhasil pada {$attendance->clock_out->format('H:i')}. Terima kasih atas kerja keras Anda hari ini!");
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * 3. Riwayat Kehadiran Pribadi
     */
    public function attendanceHistory(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month.'-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month.'-01')->endOfMonth();

        $attendances = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_present' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->whereNotNull('clock_in')
                ->count(),
            'on_time' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->where('status', 'ON_TIME')
                ->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->where('status', 'LATE')
                ->count(),
        ];

        return view('employee.attendance.history', compact('employee', 'attendances', 'stats', 'month'));
    }

    /**
     * 4. Pengajuan Cuti & Izin Pribadi
     */
    public function leaves(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('name')->get();

        $leaves = LeaveRequest::with(['leaveType', 'approver'])
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('employee.leaves.index', compact('employee', 'leaveTypes', 'leaves'));
    }

    /**
     * Kirim Pengajuan Cuti Karyawan
     */
    public function storeLeave(Request $request): RedirectResponse
    {
        $employee = $this->getEmployee($request);

        $validated = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $leaveType = LeaveType::findOrFail($validated['leave_type_id']);

        // Cek kuota jika cuti mengurangi jatah tahunan
        if ($leaveType->deducts_annual_leave && $employee->remaining_annual_leave < $totalDays) {
            return back()->withErrors([
                'reason' => "Sisa cuti tahunan Anda ({$employee->remaining_annual_leave} hari) tidak mencukupi untuk pengajuan {$totalDays} hari.",
            ])->withInput();
        }

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        return redirect()->route('employee.leaves.index')
            ->with('success', "Permohonan {$leaveType->name} selama {$totalDays} hari berhasil dikirimkan dan menunggu persetujuan atasan.");
    }

    /**
     * 5. Pengajuan Lembur Pribadi
     */
    public function overtimes(Request $request): View
    {
        $employee = $this->getEmployee($request);

        $overtimes = OvertimeRequest::with('approver')
            ->where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->paginate(10);

        return view('employee.overtimes.index', compact('employee', 'overtimes'));
    }

    /**
     * Kirim Pengajuan Lembur Karyawan
     */
    public function storeOvertime(Request $request): RedirectResponse
    {
        $employee = $this->getEmployee($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $startDateTime = Carbon::parse($validated['date'].' '.$validated['start_time']);
        $endDateTime = Carbon::parse($validated['date'].' '.$validated['end_time']);
        $totalHours = round($startDateTime->diffInMinutes($endDateTime) / 60, 2);

        OvertimeRequest::create([
            'employee_id' => $employee->id,
            'date' => $validated['date'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'total_hours' => $totalHours,
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        return redirect()->route('employee.overtimes.index')
            ->with('success', "Pengajuan lembur {$totalHours} jam berhasil dikirimkan dan menunggu persetujuan.");
    }

    /**
     * 6. Roster Jadwal Kerja Pribadi
     */
    public function schedules(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $schedules = EmployeeSchedule::with(['shift', 'officeLocation'])
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('date')
            ->get();

        return view('employee.schedules.index', compact('employee', 'schedules', 'startOfWeek', 'endOfWeek'));
    }

    /**
     * 7. Halaman Profil Karyawan Lengkap
     */
    public function profile(Request $request): View
    {
        $employee = $this->getEmployee($request);
        $user = $request->user();

        $employee->load(['department', 'position', 'manager', 'educations', 'contracts']);

        return view('employee.profile', compact('employee', 'user'));
    }
}
