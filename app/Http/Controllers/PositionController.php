<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Position::with('department')->withCount('employees');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('job_function', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($level = $request->input('level')) {
            $query->where('level', $level);
        }

        $positions = $query->orderBy('title')->paginate(10)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('modules.core-hr.positions.index', compact('positions', 'departments'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('modules.core-hr.positions.create', compact('departments'));
    }

    public function store(StorePositionRequest $request): RedirectResponse
    {
        $position = Position::create($request->validated());

        return redirect()->route('positions.index')
            ->with('success', "Jabatan '{$position->title}' berhasil ditambahkan.");
    }

    public function edit(Position $position): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('modules.core-hr.positions.edit', compact('position', 'departments'));
    }

    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        return redirect()->route('positions.index')
            ->with('success', "Jabatan '{$position->title}' berhasil diperbarui.");
    }

    public function destroy(Position $position): RedirectResponse
    {
        if ($position->employees()->count() > 0) {
            return back()->with('error', "Jabatan '{$position->title}' tidak dapat dihapus karena masih ada karyawan yang memegang jabatan ini.");
        }

        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', "Jabatan '{$position->title}' berhasil dihapus.");
    }
}
