<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeCareerHistory;
use App\Models\EmployeeContract;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $totalDepartments = Department::count();
        $totalPositions = Position::count();

        // Lifecycle Stages count
        $stageCounts = [
            'ONBOARDING' => Employee::where('lifecycle_stage', 'ONBOARDING')->count(),
            'ACTIVE' => Employee::where('lifecycle_stage', 'ACTIVE')->count(),
            'SUSPENDED' => Employee::where('lifecycle_stage', 'SUSPENDED')->count(),
            'OFFBOARDING' => Employee::where('lifecycle_stage', 'OFFBOARDING')->count(),
            'TERMINATED' => Employee::where('lifecycle_stage', 'TERMINATED')->count(),
        ];

        $expiringContracts = EmployeeContract::with('employee.department', 'employee.position')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        $recentEmployees = Employee::with('department', 'position')
            ->latest('join_date')
            ->limit(5)
            ->get();

        $recentMovements = EmployeeCareerHistory::with(['employee', 'oldDepartment', 'newDepartment', 'oldPosition', 'newPosition'])
            ->latest('effective_date')
            ->limit(5)
            ->get();

        $departmentsSummary = Department::withCount('employees')
            ->orderByDesc('employees_count')
            ->limit(6)
            ->get();

        return view('modules.core-hr.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'totalDepartments',
            'totalPositions',
            'stageCounts',
            'expiringContracts',
            'recentEmployees',
            'recentMovements',
            'departmentsSummary'
        ));
    }
}
