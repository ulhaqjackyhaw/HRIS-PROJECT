<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Holiday;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $departmentId = $request->input('department_id');
        $shiftId = $request->input('shift_id');

        $query = EmployeeSchedule::with(['employee.department', 'employee.position', 'shift', 'officeLocation'])
            ->where('date', $selectedDate);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($shiftId) {
            if ($shiftId === 'off') {
                $query->where('is_day_off', true);
            } else {
                $query->where('shift_id', $shiftId);
            }
        }

        $schedules = $query->paginate(20)->withQueryString();

        // Cek apakah tanggal yang dipilih adalah Hari Libur Nasional / Cuti Bersama
        $holiday = Holiday::getHolidayForDate($selectedDate);

        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $shifts = Shift::orderBy('name')->get();
        $locations = OfficeLocation::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('title')->get();

        return view('modules.attendance.schedules.index', compact(
            'schedules',
            'selectedDate',
            'holiday',
            'employees',
            'shifts',
            'locations',
            'departments',
            'positions',
            'departmentId',
            'shiftId'
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

    /**
     * Penetapan Massal Jadwal Kerja (Mass Assignment Shift)
     * Mengatur shift untuk seluruh departemen, cabang, atau grup sekaligus dalam rentang tanggal.
     */
    public function massAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:all,department,location,position,selected_employees'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'work_location' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'office_location_id' => ['nullable', 'exists:office_locations,id'],
            'is_day_off' => ['nullable', 'boolean'],
            'skip_national_holidays' => ['nullable', 'boolean'],
        ]);

        $isDayOff = $request->boolean('is_day_off');
        $skipHolidays = $request->boolean('skip_national_holidays', true);

        // Filter karyawan yang menjadi sasaran
        $empQuery = Employee::where('is_active', true);

        match ($validated['target_type']) {
            'department' => $empQuery->where('department_id', $validated['department_id']),
            'position' => $empQuery->where('position_id', $validated['position_id']),
            'location' => $empQuery->where('work_location', $validated['work_location']),
            'selected_employees' => $empQuery->whereIn('id', $validated['employee_ids'] ?? []),
            default => null, // 'all'
        };

        $targetEmployees = $empQuery->get();

        if ($targetEmployees->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan karyawan yang memenuhi kriteria grup yang dipilih.');
        }

        $period = CarbonPeriod::create($validated['start_date'], $validated['end_date']);
        $now = Carbon::now();
        $batch = [];
        $totalCreated = 0;

        foreach ($targetEmployees as $emp) {
            foreach ($period as $date) {
                $dateStr = $date->toDateString();

                // Cek apakah hari libur nasional jika opsi skip aktif
                $isHoliday = false;
                if ($skipHolidays && ! $isDayOff) {
                    $holiday = Holiday::getHolidayForDate($dateStr, $validated['office_location_id'] ?? null);
                    if ($holiday) {
                        $isHoliday = true;
                    }
                }

                $batch[] = [
                    'employee_id' => $emp->id,
                    'date' => $dateStr,
                    'shift_id' => ($isDayOff || $isHoliday) ? null : $validated['shift_id'],
                    'office_location_id' => ($isDayOff || $isHoliday) ? null : ($validated['office_location_id'] ?? null),
                    'is_day_off' => $isDayOff || $isHoliday,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalCreated++;

                // Chunk insert jika batch sudah mencapai 500 records
                if (count($batch) >= 500) {
                    EmployeeSchedule::upsert($batch, ['employee_id', 'date'], ['shift_id', 'office_location_id', 'is_day_off', 'updated_at']);
                    $batch = [];
                }
            }
        }

        if (! empty($batch)) {
            EmployeeSchedule::upsert($batch, ['employee_id', 'date'], ['shift_id', 'office_location_id', 'is_day_off', 'updated_at']);
        }

        $targetCount = $targetEmployees->count();
        $daysCount = $period->count();

        return redirect()->route('schedules.index', ['date' => $validated['start_date']])
            ->with('success', "Penetapan massal berhasil! {$totalCreated} jadwal telah dibuat untuk {$targetCount} karyawan ({$daysCount} hari).");
    }

    /**
     * Rotasi Otomatis (Auto-Roster Pattern Generator)
     * Mengatur pola jadwal bergilir berulang (misal: 3 hari kerja, 2 hari libur; 5-2; rotasi shift) secara otomatis.
     */
    public function autoRoster(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:all,department,location,position,selected_employees'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'work_location' => ['nullable', 'string'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pattern_type' => ['required', 'in:3_2,5_2,4_2,6_1,ROTATING_3_SHIFT'],
            'work_shift_id' => ['nullable', 'exists:shifts,id'],
            'morning_shift_id' => ['nullable', 'exists:shifts,id'],
            'afternoon_shift_id' => ['nullable', 'exists:shifts,id'],
            'night_shift_id' => ['nullable', 'exists:shifts,id'],
            'office_location_id' => ['nullable', 'exists:office_locations,id'],
            'skip_national_holidays' => ['nullable', 'boolean'],
        ]);

        $skipHolidays = $request->boolean('skip_national_holidays', true);

        // Query target employees
        $empQuery = Employee::where('is_active', true);
        match ($validated['target_type']) {
            'department' => $empQuery->where('department_id', $validated['department_id']),
            'position' => $empQuery->where('position_id', $validated['position_id']),
            'location' => $empQuery->where('work_location', $validated['work_location']),
            'selected_employees' => $empQuery->whereIn('id', $validated['employee_ids'] ?? []),
            default => null,
        };

        $targetEmployees = $empQuery->get();
        if ($targetEmployees->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan karyawan untuk diterapkan rotasi Auto-Roster.');
        }

        $startDate = Carbon::parse($validated['start_date']);
        $period = CarbonPeriod::create($validated['start_date'], $validated['end_date']);
        $now = Carbon::now();
        $batch = [];
        $totalCreated = 0;

        // Fallback default shifts jika rotasi 3 shift
        $firstShift = Shift::first();
        $workShiftId = $validated['work_shift_id'] ?? $firstShift?->id;
        $morningShift = $validated['morning_shift_id'] ?? $workShiftId;
        $afternoonShift = $validated['afternoon_shift_id'] ?? $workShiftId;
        $nightShift = $validated['night_shift_id'] ?? $workShiftId;

        foreach ($targetEmployees as $emp) {
            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $dayIndex = $startDate->diffInDays($date);

                $isDayOff = false;
                $assignedShiftId = $workShiftId;

                switch ($validated['pattern_type']) {
                    case '3_2':
                        // Siklus 5 hari: 3 Hari Kerja, 2 Hari Libur
                        $cycle = $dayIndex % 5;
                        if ($cycle >= 3) {
                            $isDayOff = true;
                            $assignedShiftId = null;
                        }
                        break;

                    case '5_2':
                        // Siklus 7 hari: 5 Hari Kerja, 2 Hari Libur
                        $cycle = $dayIndex % 7;
                        if ($cycle >= 5) {
                            $isDayOff = true;
                            $assignedShiftId = null;
                        }
                        break;

                    case '4_2':
                        // Siklus 6 hari: 4 Hari Kerja, 2 Hari Libur
                        $cycle = $dayIndex % 6;
                        if ($cycle >= 4) {
                            $isDayOff = true;
                            $assignedShiftId = null;
                        }
                        break;

                    case '6_1':
                        // Siklus 7 hari: 6 Hari Kerja, 1 Hari Libur
                        $cycle = $dayIndex % 7;
                        if ($cycle >= 6) {
                            $isDayOff = true;
                            $assignedShiftId = null;
                        }
                        break;

                    case 'ROTATING_3_SHIFT':
                        // Siklus 4 hari: Pagi -> Siang -> Malam -> Libur
                        $cycle = $dayIndex % 4;
                        if ($cycle === 0) {
                            $assignedShiftId = $morningShift;
                        } elseif ($cycle === 1) {
                            $assignedShiftId = $afternoonShift;
                        } elseif ($cycle === 2) {
                            $assignedShiftId = $nightShift;
                        } else {
                            $isDayOff = true;
                            $assignedShiftId = null;
                        }
                        break;
                }

                // Cek kalender hari libur nasional / cuti bersama jika diaktifkan
                if ($skipHolidays && ! $isDayOff) {
                    $holiday = Holiday::getHolidayForDate($dateStr, $validated['office_location_id'] ?? null);
                    if ($holiday) {
                        $isDayOff = true;
                        $assignedShiftId = null;
                    }
                }

                $batch[] = [
                    'employee_id' => $emp->id,
                    'date' => $dateStr,
                    'shift_id' => $isDayOff ? null : $assignedShiftId,
                    'office_location_id' => $isDayOff ? null : ($validated['office_location_id'] ?? null),
                    'is_day_off' => $isDayOff,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $totalCreated++;

                if (count($batch) >= 500) {
                    EmployeeSchedule::upsert($batch, ['employee_id', 'date'], ['shift_id', 'office_location_id', 'is_day_off', 'updated_at']);
                    $batch = [];
                }
            }
        }

        if (! empty($batch)) {
            EmployeeSchedule::upsert($batch, ['employee_id', 'date'], ['shift_id', 'office_location_id', 'is_day_off', 'updated_at']);
        }

        $empCount = $targetEmployees->count();
        $patternNames = [
            '3_2' => '3 Hari Kerja - 2 Hari Libur',
            '5_2' => '5 Hari Kerja - 2 Hari Libur',
            '4_2' => '4 Hari Kerja - 2 Hari Libur',
            '6_1' => '6 Hari Kerja - 1 Hari Libur',
            'ROTATING_3_SHIFT' => 'Rotasi 3 Shift Bergilir (Pagi-Siang-Malam-Off)',
        ];
        $patternTitle = $patternNames[$validated['pattern_type']] ?? $validated['pattern_type'];

        return redirect()->route('schedules.index', ['date' => $validated['start_date']])
            ->with('success', "Auto-Roster berhasil di-generate! Pola '{$patternTitle}' telah diterapkan ke {$empCount} karyawan ({$totalCreated} entri jadwal).");
    }

    public function destroy(EmployeeSchedule $schedule): RedirectResponse
    {
        $date = $schedule->date->toDateString();
        $schedule->delete();

        return redirect()->route('schedules.index', ['date' => $date])
            ->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
