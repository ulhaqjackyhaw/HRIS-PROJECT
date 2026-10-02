<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::with(['department', 'position', 'manager']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('ktp_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($posId = $request->input('position_id')) {
            $query->where('position_id', $posId);
        }

        if ($status = $request->input('employment_status')) {
            $query->where('employment_status', $status);
        }

        if ($stage = $request->input('lifecycle_stage')) {
            $query->where('lifecycle_stage', $stage);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $employees = $query->orderBy('full_name')->paginate(10)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('title')->get();

        return view('modules.core-hr.employees.index', compact('employees', 'departments', 'positions'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('title')->get();
        $managers = Employee::where('is_active', true)->orderBy('full_name')->get();

        return view('modules.core-hr.employees.create', compact('departments', 'positions', 'managers'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['lifecycle_stage'])) {
            $data['lifecycle_stage'] = 'ACTIVE';
        }

        $employee = Employee::create($data);

        // Jika ada nomor kontrak awal dan tgl mulai, otomatis catat riwayat kontrak pertamanya
        if (! empty($data['current_contract_no']) && ! empty($data['join_date'])) {
            $employee->contracts()->create([
                'contract_number' => $data['current_contract_no'],
                'contract_type' => $data['employment_status'],
                'start_date' => $data['join_date'],
                'end_date' => $data['end_date'] ?? null,
                'notes' => 'Kontrak awal saat penerimaan karyawan.',
            ]);
        }

        // Catat jejak karir pertama (JOIN)
        $employee->careerHistories()->create([
            'transition_type' => 'JOIN',
            'new_department_id' => $employee->department_id,
            'new_position_id' => $employee->position_id,
            'effective_date' => $employee->join_date,
            'reference_doc_no' => $employee->current_contract_no,
            'notes' => 'Penerimaan karyawan baru (Onboarding / Penempatan Awal).',
        ]);

        return redirect()->route('employees.show', $employee)
            ->with('success', "Karyawan '{$employee->full_name}' ({$employee->nik}) berhasil didaftarkan.");
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'position',
            'manager',
            'subordinates.position',
            'educations' => fn ($q) => $q->orderByDesc('graduation_year'),
            'contracts' => fn ($q) => $q->orderByDesc('start_date'),
            'careerHistories.oldDepartment',
            'careerHistories.newDepartment',
            'careerHistories.oldPosition',
            'careerHistories.newPosition',
        ]);

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('title')->get();

        return view('modules.core-hr.employees.show', compact('employee', 'departments', 'positions'));
    }

    public function edit(Employee $employee): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('title')->get();
        $managers = Employee::where('id', '!=', $employee->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('modules.core-hr.employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('employees.show', $employee)
            ->with('success', "Profil karyawan '{$employee->full_name}' berhasil diperbarui.");
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->full_name;
        $employee->update(['lifecycle_stage' => 'TERMINATED', 'is_active' => false]);
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', "Data karyawan '{$name}' telah dialihkan ke status TERMINATED (Soft Delete).");
    }
}
