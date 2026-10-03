<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
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

        $query = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType', 'approver']);

        if ($status && in_array($status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $status);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $leaveTypes = LeaveType::where('is_active', true)->orderBy('id')->get();
        $currentUserEmployee = auth()->user()->employee;

        // KPI Counts
        $kpi = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('status', 'PENDING')->count(),
            'approved' => LeaveRequest::where('status', 'APPROVED')->count(),
            'rejected' => LeaveRequest::where('status', 'REJECTED')->count(),
        ];

        return view('modules.attendance.leaves.index', compact('leaves', 'employees', 'leaveTypes', 'currentUserEmployee', 'kpi'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $employeeId = $request->input('employee_id') ?? auth()->user()->employee?->id;
        $employee = Employee::findOrFail($employeeId);

        $leaveType = null;
        if ($request->filled('leave_type_id')) {
            $leaveType = LeaveType::findOrFail($request->input('leave_type_id'));
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'nullable|exists:leave_types,id',
            'type' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => ($leaveType && $leaveType->requires_attachment)
                ? 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
                : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'attachment.required' => 'Jenis izin ini mewajibkan dokumen lampiran (surat dokter / surat bukti pendukung).',
            'attachment.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        // 1. Hitung jumlah hari kerja aktif (lewati hari libur Sabtu & Minggu)
        $start = Carbon::parse($request->input('start_date'));
        $end = Carbon::parse($request->input('end_date'));
        $period = CarbonPeriod::create($start, $end);

        $totalDays = 0;
        foreach ($period as $date) {
            if (! $date->isWeekend()) {
                $totalDays++;
            }
        }

        // Minimal 1 hari jika berada di rentang kerja
        $totalDays = max(1, $totalDays);

        // 2. Validasi Batas Maksimal Hari untuk Izin Khusus
        if ($leaveType && $leaveType->max_days && $totalDays > $leaveType->max_days) {
            return back()->withInput()->withErrors([
                'message' => "Maksimal izin untuk {$leaveType->name} adalah {$leaveType->max_days} hari kerja. Anda mengajukan {$totalDays} hari.",
            ]);
        }

        // 3. Validasi Kuota Cuti Tahunan (Annual Leave Quota)
        $deductsQuota = $leaveType ? $leaveType->deducts_annual_quota : ($request->input('type') === 'ANNUAL');
        if ($deductsQuota) {
            $remainingQuota = $employee->remaining_annual_leave;
            if ($totalDays > $remainingQuota) {
                return back()->withInput()->withErrors([
                    'message' => "Sisa kuota cuti tahunan Anda tidak mencukupi (Sisa kuota: {$remainingQuota} hari, Pengajuan: {$totalDays} hari). Silakan ajukan Izin Tanpa Upah (Unpaid Leave) jika mendesak.",
                ]);
            }
        }

        // 4. Simpan Dokumen Lampiran jika diunggah
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType?->id,
            'type' => $leaveType ? $leaveType->code : ($request->input('type') ?? 'ANNUAL'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'total_days' => $totalDays,
            'reason' => $request->input('reason'),
            'attachment_path' => $attachmentPath,
            'status' => 'PENDING',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu verifikasi.');
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
        $employee = $leave->employee;
        $leaveType = $leave->leaveType;

        // 1. Update status pengajuan
        $leave->update([
            'status' => 'APPROVED',
            'approved_by' => $approver?->id,
            'action_at' => Carbon::now(),
        ]);

        // 2. Potong kuota cuti tahunan jika jenis cuti memotong kuota
        if ($employee && ($leaveType?->deducts_annual_quota || $leave->type === 'ANNUAL')) {
            $employee->increment('annual_leave_used', $leave->total_days);
        }

        // 3. Loop setiap hari dalam rentang tanggal cuti (lewati hari akhir pekan)
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

        return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti telah disetujui. Saldo kuota diperbarui dan status kalender presensi otomatis tercatat sebagai LEAVE (Cuti/Izin).');
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
            'rejection_note' => $request->input('rejection_note'),
            'action_at' => Carbon::now(),
        ]);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan cuti telah ditolak.');
    }
}
