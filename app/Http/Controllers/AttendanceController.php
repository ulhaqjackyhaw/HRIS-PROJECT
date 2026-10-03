<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\LeaveRequest;
use App\Models\OfficeLocation;
use App\Models\OvertimeRequest;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Halaman Dashboard Presensi Mandiri (Webcam Selfie + GPS Check-In)
     */
    public function checkInForm(Request $request): View
    {
        $user = $request->user();

        // Cari profil employee yang terkait dengan user login, atau fallback ke employee aktif pertama untuk demo/admin
        $employee = Employee::where('user_id', $user->id)->first()
            ?? Employee::where('is_active', true)->firstOrFail();

        // Karyawan lain jika ingin simulasi switch user
        $allEmployees = Employee::where('is_active', true)->orderBy('full_name')->get();

        // Parameter override employee_id jika admin ingin melihat presensi karyawan lain
        if ($request->filled('simulate_employee_id')) {
            $employee = Employee::findOrFail($request->input('simulate_employee_id'));
        }

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

        $recentAttendances = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->latest('date')
            ->limit(5)
            ->get();

        return view('modules.attendance.check-in', compact(
            'employee',
            'schedule',
            'todayAttendance',
            'officeLocations',
            'recentAttendances',
            'allEmployees'
        ));
    }

    /**
     * Eksekusi Clock-In Karyawan
     */
    public function clockIn(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_data' => ['nullable', 'string'],
        ]);

        $employeeId = $request->input('employee_id');
        $employee = $employeeId
            ? Employee::findOrFail($employeeId)
            : (Employee::where('user_id', $request->user()->id)->first() ?? Employee::where('is_active', true)->firstOrFail());

        $photo = $request->file('photo') ?? $request->input('photo_data');

        if (! $photo) {
            return back()->withErrors(['message' => 'Foto selfie wajib diambil menggunakan kamera sebelum Clock-In.']);
        }

        try {
            $attendance = $this->attendanceService->processClockIn(
                $employee,
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
                $photo
            );

            $statusText = $attendance->status === 'LATE'
                ? "Clock-In berhasil! Anda tercatat TERLAMBAT {$attendance->late_minutes} menit."
                : 'Clock-In berhasil tepat waktu!';

            return redirect()->route('attendance.check-in')
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
            'employee_id' => ['nullable', 'exists:employees,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_data' => ['nullable', 'string'],
        ]);

        $employeeId = $request->input('employee_id');
        $employee = $employeeId
            ? Employee::findOrFail($employeeId)
            : (Employee::where('user_id', $request->user()->id)->first() ?? Employee::where('is_active', true)->firstOrFail());

        $photo = $request->file('photo') ?? $request->input('photo_data');

        try {
            $attendance = $this->attendanceService->processClockOut(
                $employee,
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
                $photo
            );

            $hours = floor($attendance->total_work_minutes / 60);
            $minutes = $attendance->total_work_minutes % 60;

            return redirect()->route('attendance.check-in')
                ->with('success', "Clock-Out berhasil! Total jam kerja hari ini: {$hours} jam {$minutes} menit.");
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Monitoring Aktivitas & Rekap Presensi Karyawan (Admin / HC View)
     */
    public function index(Request $request): View
    {
        $targetDate = $request->input('date', Carbon::today()->toDateString());

        $query = Attendance::with(['employee.department', 'employee.position', 'shift'])
            ->whereDate('date', $targetDate);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($deptId = $request->input('department_id')) {
            $query->whereHas('employee', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        if ($search = $request->input('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('clock_in')->paginate(15)->withQueryString();

        // Hitung statistik hari terpilih
        $totalPresent = Attendance::whereDate('date', $targetDate)->where('status', 'PRESENT')->count();
        $totalLate = Attendance::whereDate('date', $targetDate)->where('status', 'LATE')->count();
        $totalEarlyLeave = Attendance::whereDate('date', $targetDate)->where('early_leave_minutes', '>', 0)->count();
        $totalActiveEmployees = Employee::where('is_active', true)->count();
        $totalAbsent = max(0, $totalActiveEmployees - ($totalPresent + $totalLate));

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('modules.attendance.index', compact(
            'attendances',
            'targetDate',
            'totalPresent',
            'totalLate',
            'totalEarlyLeave',
            'totalAbsent',
            'totalActiveEmployees',
            'departments'
        ));
    }

    /**
     * Rekapitulasi Presensi & Lembur Karyawan Bulanan untuk HR & Payroll
     */
    public function summary(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month)->endOfMonth()->toDateString();
        $departmentId = $request->input('department_id');

        $query = Employee::with(['department', 'position'])
            ->where('is_active', true);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->orderBy('full_name')->get()
            ->map(function ($emp) use ($startDate, $endDate) {
                $attendances = Attendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $overtimes = OvertimeRequest::where('employee_id', $emp->id)
                    ->where('status', 'APPROVED')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->sum('total_hours');

                $approvedLeaves = LeaveRequest::with('leaveType')
                    ->where('employee_id', $emp->id)
                    ->where('status', 'APPROVED')
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($sub) use ($startDate, $endDate) {
                                $sub->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                    ->get();

                $unpaidLeaveDays = 0;
                $paidLeaveDays = 0;
                $leaveAttendances = $attendances->where('status', 'LEAVE');

                foreach ($leaveAttendances as $leaveAtt) {
                    $attDate = Carbon::parse($leaveAtt->date)->toDateString();
                    $matchedLeave = $approvedLeaves->first(function ($l) use ($attDate) {
                        $lStart = Carbon::parse($l->start_date)->toDateString();
                        $lEnd = Carbon::parse($l->end_date)->toDateString();

                        return $attDate >= $lStart && $attDate <= $lEnd;
                    });

                    if ($matchedLeave && ($matchedLeave->leaveType?->is_paid === false || $matchedLeave->type === 'UNPAID')) {
                        $unpaidLeaveDays++;
                    } else {
                        $paidLeaveDays++;
                    }
                }

                return [
                    'id' => $emp->id,
                    'nik' => $emp->nik,
                    'name' => $emp->full_name,
                    'department' => $emp->department?->name ?? '-',
                    'position' => $emp->position?->title ?? '-',
                    'present_count' => $attendances->where('status', 'PRESENT')->count(),
                    'late_count' => $attendances->where('status', 'LATE')->count(),
                    'leave_count' => $attendances->where('status', 'LEAVE')->count(),
                    'paid_leave_count' => $paidLeaveDays,
                    'unpaid_leave_count' => $unpaidLeaveDays,
                    'absent_count' => $attendances->where('status', 'ABSENT')->count(),
                    'annual_leave_quota' => $emp->annual_leave_quota ?? 12,
                    'annual_leave_used' => $emp->annual_leave_used ?? 0,
                    'remaining_annual_leave' => $emp->remaining_annual_leave,
                    'total_late_minutes' => (int) $attendances->sum('late_minutes'),
                    'total_overtime_hours' => (float) $overtimes,
                    'total_work_minutes' => (int) $attendances->sum('total_work_minutes'),
                ];
            });

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $kpi = [
            'total_employees' => $employees->count(),
            'total_present' => $employees->sum('present_count'),
            'total_late' => $employees->sum('late_count'),
            'total_leave' => $employees->sum('leave_count'),
            'total_paid_leave' => $employees->sum('paid_leave_count'),
            'total_unpaid_leave' => $employees->sum('unpaid_leave_count'),
            'total_overtime_hours' => $employees->sum('total_overtime_hours'),
        ];

        return view('modules.attendance.summary', compact('employees', 'month', 'departments', 'kpi'));
    }
}
