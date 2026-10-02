<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests with submission form.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $employeeId = $request->query('employee_id');

        $query = LeaveRequest::with(['employee.department', 'employee.position', 'approver']);

        if ($status && in_array($status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $status);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $currentUserEmployee = auth()->user()->employee;

        // KPI Counts
        $kpi = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'PENDING')->count(),
            'approved' => LeaveRequest::where('status', 'APPROVED')->count(),
            'rejected' => LeaveRequest::where('status', 'REJECTED')->count(),
        ];

        return view('modules.attendance.leaves.index', compact('leaves', 'employees', 'currentUserEmployee', 'kpi'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|in:ANNUAL,SICK,SPECIAL,MATERNITY,UNPAID,OTHER',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $period = CarbonPeriod::create($validated['start_date'], $validated['end_date']);
        $totalDays = 0;
        foreach ($period as $date) {
            if (! $date->isWeekend()) {
                $totalDays++;
            }
        }

        // Minimal 1 hari jika jatuh pada rentang tanggal
        $validated['total_days'] = max(1, $totalDays);
        $validated['status'] = 'PENDING';

        LeaveRequest::create($validated);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti / izin berhasil dikirim dan menunggu verifikasi.');
    }

    /**
     * Approve leave request and automatically mark attendances as LEAVE.
     */
    public function approve(Request $request, LeaveRequest $leave): RedirectResponse
    {
        if ($leave->status !== 'PENDING') {
            return back()->with('error', 'Pengajuan cuti ini sudah diverifikasi sebelumnya.');
        }

        $approver = auth()->user()->employee;

        $leave->update([
            'status' => 'APPROVED',
            'approved_by' => $approver?->id,
            'action_at' => Carbon::now(),
        ]);

        // Loop setiap hari dalam rentang tanggal cuti (lewati hari akhir pekan)
        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

        foreach ($period as $date) {
            if (! $date->isWeekend()) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $leave->employee_id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'status' => 'LEAVE',
                        'late_minutes' => 0,
                        'early_leave_minutes' => 0,
                        'total_work_minutes' => 0,
                    ]
                );
            }
        }

        return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti telah disetujui. Status kalender presensi otomatis tercatat sebagai LEAVE (Cuti/Izin).');
    }

    /**
     * Reject leave request with note.
     */
    public function reject(Request $request, LeaveRequest $leave): RedirectResponse
    {
        if ($leave->status !== 'PENDING') {
            return back()->with('error', 'Pengajuan cuti ini sudah diverifikasi sebelumnya.');
        }

        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $approver = auth()->user()->employee;

        $leave->update([
            'status' => 'REJECTED',
            'approved_by' => $approver?->id,
            'rejection_note' => $request->rejection_note,
            'action_at' => Carbon::now(),
        ]);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti telah ditolak.');
    }
}
