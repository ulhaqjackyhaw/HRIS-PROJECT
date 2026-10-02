<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\OfficeLocation;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $departmentId = $request->input('department_id');

        $query = EmployeeSchedule::with(['employee.department', 'employee.position', 'shift', 'officeLocation'])
            ->where('date', $selectedDate);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $schedules = $query->paginate(20)->withQueryString();

        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $shifts = Shift::orderBy('name')->get();
        $locations = OfficeLocation::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('modules.attendance.schedules.index', compact(
            'schedules',
            'selectedDate',
            'employees',
            'shifts',
            'locations',
            'departments',
            'departmentId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'office_location_id' => ['nullable', 'exists:office_locations,id'],
            'date' => ['required', 'date'],
            'is_day_off' => ['nullable', 'boolean'],
        ]);

        $validated['is_day_off'] = $request->boolean('is_day_off');

        EmployeeSchedule::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            [
                'shift_id' => $validated['is_day_off'] ? null : $validated['shift_id'],
                'office_location_id' => $validated['is_day_off'] ? null : $validated['office_location_id'],
                'is_day_off' => $validated['is_day_off'],
            ]
        );

        return redirect()->route('schedules.index', ['date' => $validated['date']])
            ->with('success', 'Jadwal kerja karyawan berhasil ditetapkan.');
    }

    public function destroy(EmployeeSchedule $schedule): RedirectResponse
    {
        $date = $schedule->date->toDateString();
        $schedule->delete();

        return redirect()->route('schedules.index', ['date' => $date])
            ->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
