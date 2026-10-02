<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OvertimeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = OvertimeRequest::with(['employee.department', 'employee.position', 'approver']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $overtimes = $query->orderByDesc('date')->paginate(15)->withQueryString();
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();

        return view('modules.attendance.overtimes.index', compact('overtimes', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $startDateTime = Carbon::parse($validated['date'].' '.$validated['start_time']);
        $endDateTime = Carbon::parse($validated['date'].' '.$validated['end_time']);
        $totalHours = round($startDateTime->diffInMinutes($endDateTime) / 60, 2);

        OvertimeRequest::create([
            'employee_id' => $validated['employee_id'],
            'date' => $validated['date'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'total_hours' => $totalHours,
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', "Pengajuan lembur sebesar {$totalHours} jam berhasil dikirim dan menunggu persetujuan.");
    }

    public function approve(Request $request, OvertimeRequest $overtime): RedirectResponse
    {
        $approver = Employee::where('user_id', $request->user()->id)->first()
            ?? Employee::where('is_active', true)->first();

        $overtime->update([
            'status' => 'APPROVED',
            'approved_by' => $approver?->id,
            'rejection_note' => null,
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', "Pengajuan lembur untuk {$overtime->employee->full_name} berhasil disetujui.");
    }

    public function reject(Request $request, OvertimeRequest $overtime): RedirectResponse
    {
        $request->validate([
            'rejection_note' => ['required', 'string', 'max:500'],
        ]);

        $approver = Employee::where('user_id', $request->user()->id)->first()
            ?? Employee::where('is_active', true)->first();

        $overtime->update([
            'status' => 'REJECTED',
            'approved_by' => $approver?->id,
            'rejection_note' => $request->input('rejection_note'),
        ]);

        return redirect()->route('overtimes.index')
            ->with('success', "Pengajuan lembur untuk {$overtime->employee->full_name} telah ditolak.");
    }
}
