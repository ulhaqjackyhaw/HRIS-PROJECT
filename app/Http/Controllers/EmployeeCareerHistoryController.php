<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeCareerHistoryRequest;
use App\Models\Employee;
use App\Models\EmployeeCareerHistory;
use Illuminate\Http\RedirectResponse;

class EmployeeCareerHistoryController extends Controller
{
    /**
     * Record a new career transition / movement for the employee.
     */
    public function store(StoreEmployeeCareerHistoryRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated();

        $career = $employee->careerHistories()->create([
            'transition_type' => $validated['transition_type'],
            'old_department_id' => $employee->department_id,
            'new_department_id' => $validated['new_department_id'] ?? $employee->department_id,
            'old_position_id' => $employee->position_id,
            'new_position_id' => $validated['new_position_id'] ?? $employee->position_id,
            'effective_date' => $validated['effective_date'],
            'reference_doc_no' => $validated['reference_doc_no'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Updates to employee current profile based on career movement
        $updates = [];

        if (! empty($validated['new_department_id'])) {
            $updates['department_id'] = $validated['new_department_id'];
        }

        if (! empty($validated['new_position_id'])) {
            $updates['position_id'] = $validated['new_position_id'];
        }

        // Adjust lifecycle stage & account active flag
        if (! empty($validated['lifecycle_stage'])) {
            $updates['lifecycle_stage'] = $validated['lifecycle_stage'];
            if ($validated['lifecycle_stage'] === 'TERMINATED') {
                $updates['is_active'] = false;
            } elseif ($validated['lifecycle_stage'] === 'ACTIVE') {
                $updates['is_active'] = true;
            } elseif ($validated['lifecycle_stage'] === 'SUSPENDED') {
                $updates['is_active'] = false;
            }
        } elseif (in_array($validated['transition_type'], ['RESIGN', 'TERMINATE'])) {
            $updates['lifecycle_stage'] = 'TERMINATED';
            $updates['is_active'] = false;
        }

        if (! empty($updates)) {
            $employee->update($updates);
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', "Riwayat pergerakan karir '{$career->transition_type}' berhasil dicatat dan status pegawai disinkronkan.");
    }

    /**
     * Remove a career transition record.
     */
    public function destroy(Employee $employee, EmployeeCareerHistory $career): RedirectResponse
    {
        $career->delete();

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Riwayat pergerakan karir berhasil dihapus.');
    }
}
