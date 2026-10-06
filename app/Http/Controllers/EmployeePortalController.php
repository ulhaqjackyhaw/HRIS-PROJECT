<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    /**
     * Tampilkan Dashboard Khusus Karyawan (Employee Self Service / ESS).
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        // Cari data profil employee terkait
        $employee = Employee::with(['department', 'position', 'manager'])
            ->where('user_id', $user->id)
            ->first() ?? Employee::with(['department', 'position', 'manager'])
            ->where('is_active', true)
            ->first();

        $today = Carbon::today()->toDateString();

        $todayAttendance = null;
        $schedule = null;
        $recentAttendances = collect();
        $recentLeaves = collect();
        $recentOvertimes = collect();

        if ($employee) {
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
        }

        return view('modules.employee.dashboard', compact(
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
}
