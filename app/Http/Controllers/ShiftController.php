<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(): View
    {
        $shifts = Shift::withCount(['schedules', 'attendances'])
            ->orderBy('start_time')
            ->get();

        return view('modules.attendance.shifts.index', compact('shifts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:shifts,code'],
            'name' => ['required', 'string', 'max:100'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'is_overnight' => ['nullable', 'boolean'],
        ]);

        $validated['is_overnight'] = $request->boolean('is_overnight');

        $shift = Shift::create($validated);

        return redirect()->route('shifts.index')
            ->with('success', "Shift '{$shift->name}' ({$shift->code}) berhasil ditambahkan.");
    }

    public function update(Request $request, Shift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:shifts,code,'.$shift->id],
            'name' => ['required', 'string', 'max:100'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'is_overnight' => ['nullable', 'boolean'],
        ]);

        $validated['is_overnight'] = $request->boolean('is_overnight');

        $shift->update($validated);

        return redirect()->route('shifts.index')
            ->with('success', "Shift '{$shift->name}' berhasil diperbarui.");
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        if ($shift->schedules()->count() > 0) {
            return back()->with('error', "Shift '{$shift->name}' tidak dapat dihapus karena masih digunakan pada jadwal karyawan.");
        }

        $shift->delete();

        return redirect()->route('shifts.index')
            ->with('success', "Shift '{$shift->name}' berhasil dihapus.");
    }
}
