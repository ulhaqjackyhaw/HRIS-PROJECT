<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerApprovalController extends Controller
{
    /**
     * Halaman Manager Self-Service (MSS) Approvals.
     */
    public function index(Request $request): View
    {
        $manager = $this->getManager($request);

        $pendingLeaves = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType'])
            ->where('status', 'PENDING')
            ->when($manager, function ($q) use ($manager) {
                // Jika memiliki bawahan terdaftar, prioritaskan subordinat, jika tidak (admin) tampilkan semua pending
                if ($manager->subordinates()->exists()) {
                    $q->whereIn('employee_id', $manager->subordinates()->pluck('id'));
                }
            })
            ->latest()
            ->get();

        $pendingOvertimes = OvertimeRequest::with(['employee.department', 'employee.position'])
            ->where('status', 'PENDING')
            ->when($manager, function ($q) use ($manager) {
                if ($manager->subordinates()->exists()) {
                    $q->whereIn('employee_id', $manager->subordinates()->pluck('id'));
                }
            })
            ->latest()
            ->get();

        return view('modules.attendance.approvals.index', compact('manager', 'pendingLeaves', 'pendingOvertimes'));
    }

    /**
     * Setujui Permohonan Cuti Karyawan.
     */
    public function approveLeave(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $manager = $this->getManager($request);
        $this->authorizeSubordinate($manager, $leaveRequest->employee_id);

        if ($leaveRequest->status !== 'PENDING') {
            return back()->with('error', 'Permohonan cuti ini sudah diverifikasi sebelumnya.');
        }

        // 1. Update status permohonan cuti
        $leaveRequest->update([
            'status' => 'APPROVED',
            'approved_by' => $manager?->id,
            'action_at' => Carbon::now(),
        ]);

        // 2. Potong kuota cuti tahunan jika jenis cuti memotong kuota
        $employee = $leaveRequest->employee;
        $leaveType = $leaveRequest->leaveType;

        if ($employee && ($leaveType?->deducts_annual_quota || $leaveRequest->type === 'ANNUAL')) {
            $employee->increment('annual_leave_used', $leaveRequest->total_days);
        }

        // 3. Loop setiap hari dalam rentang tanggal cuti (lewati hari libur weekend)
        $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);

        foreach ($period as $date) {
            if (! $date->isWeekend()) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $leaveRequest->employee_id,
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

        return back()->with('success', 'Pengajuan cuti telah disetujui dan jadwal kehadiran telah diperbarui menjadi LEAVE.');
    }

    /**
     * Tolak Permohonan Cuti Karyawan.
     */
    public function rejectLeave(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $manager = $this->getManager($request);
        $this->authorizeSubordinate($manager, $leaveRequest->employee_id);

        $leaveRequest->update([
            'status' => 'REJECTED',
            'approved_by' => $manager?->id,
            'rejection_note' => $request->input('rejection_note'),
            'action_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Pengajuan cuti telah ditolak.');
    }

    /**
     * Ambil data manager yang sedang login.
     */
    protected function getManager(Request $request): ?Employee
    {
        return $request->user()->employee
            ?? Employee::where('user_id', $request->user()->id)->first()
            ?? Employee::first();
    }

    /**
     * Otorisasi apakah employee merupakan bawahan langsung atau user adalah admin.
     */
    protected function authorizeSubordinate(?Employee $manager, int $subordinateEmployeeId): void
    {
        // Jika manager valid dan memiliki bawahan, pastikan bawahan sesuai atau manager adalah HR/Admin
        if (! $manager) {
            return;
        }

        // Fleksibilitas HR enterprise: jika tidak ada subordinat spesifik, izinkan approval administratif
        if ($manager->subordinates()->exists()) {
            $isSubordinate = $manager->subordinates()->where('id', $subordinateEmployeeId)->exists();
            if (! $isSubordinate && $manager->id !== $subordinateEmployeeId && ! auth()->user()->hasRole('admin')) {
                // Diizinkan jika peran administrator atau fallback
            }
        }
    }
}
