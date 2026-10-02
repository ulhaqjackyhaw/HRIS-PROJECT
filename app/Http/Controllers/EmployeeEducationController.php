<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeEducationRequest;
use App\Models\Employee;
use App\Models\EmployeeEducation;
use Illuminate\Http\RedirectResponse;

class EmployeeEducationController extends Controller
{
    public function store(StoreEmployeeEducationRequest $request, Employee $employee): RedirectResponse
    {
        $education = $employee->educations()->create($request->validated());

        return redirect()->route('employees.show', $employee)
            ->with('success', "Data pendidikan '{$education->major}' ({$education->institution_name}) berhasil ditambahkan.");
    }

    public function destroy(Employee $employee, EmployeeEducation $education): RedirectResponse
    {
        $education->delete();

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Data pendidikan berhasil dihapus.');
    }
}
