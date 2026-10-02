<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Department::with('parent')
            ->withCount(['positions', 'employees']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $departments = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('modules.core-hr.departments.index', compact('departments'));
    }

    public function create(): View
    {
        $parentDepartments = Department::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.core-hr.departments.create', compact('parentDepartments'));
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return redirect()->route('departments.index')
            ->with('success', "Departemen '{$department->name}' berhasil ditambahkan.");
    }

    public function edit(Department $department): View
    {
        $parentDepartments = Department::where('id', '!=', $department->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('modules.core-hr.departments.edit', compact('department', 'parentDepartments'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('departments.index')
            ->with('success', "Departemen '{$department->name}' berhasil diperbarui.");
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->count() > 0) {
            return back()->with('error', "Departemen '{$department->name}' tidak dapat dihapus karena masih memiliki karyawan aktif.");
        }

        if ($department->children()->count() > 0) {
            return back()->with('error', "Departemen '{$department->name}' tidak dapat dihapus karena masih memiliki sub-departemen.");
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', "Departemen '{$department->name}' berhasil dihapus.");
    }
}
