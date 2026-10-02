<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeContractRequest;
use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Http\RedirectResponse;

class EmployeeContractController extends Controller
{
    public function store(StoreEmployeeContractRequest $request, Employee $employee): RedirectResponse
    {
        $contract = $employee->contracts()->create($request->validated());

        // Update current contract info on employee if latest
        $employee->update([
            'current_contract_no' => $contract->contract_number,
            'end_date' => $contract->end_date,
        ]);

        return redirect()->route('employees.show', $employee)
            ->with('success', "Riwayat kontrak '{$contract->contract_number}' berhasil ditambahkan.");
    }

    public function destroy(Employee $employee, EmployeeContract $contract): RedirectResponse
    {
        $contract->delete();

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Riwayat kontrak berhasil dihapus.');
    }
}
