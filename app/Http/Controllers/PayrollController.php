<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPayrollBatchJob;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use App\Models\SalaryStructure;
use App\Services\PayrollEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    /**
     * Daftar Seluruh Periode Penggajian & Ringkasan Keuangan.
     */
    public function index(): View
    {
        $periods = PayrollPeriod::withCount('payslips')
            ->orderByDesc('start_date')
            ->paginate(10);

        $totalEmployees = Employee::where('is_active', true)->count();
        $employeesWithSalary = SalaryStructure::count();
        $totalPaidNet = PayrollPeriod::where('status', 'PAID')->sum('total_net_amount');
        $activeDraftPeriods = PayrollPeriod::whereIn('status', ['DRAFT', 'PROCESSING'])->count();

        $kpi = [
            'total_periods' => PayrollPeriod::count(),
            'total_employees' => $totalEmployees,
            'configured_salaries' => $employeesWithSalary,
            'total_paid_net' => $totalPaidNet,
            'draft_periods' => $activeDraftPeriods,
        ];

        return view('modules.payroll.index', compact('periods', 'kpi'));
    }

    /**
     * Buat Periode Penggajian Baru (Cut-Off Periode).
     */
    public function storePeriod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payout_date' => 'required|date',
        ]);

        $period = PayrollPeriod::create($validated);

        return redirect()->route('payroll.periods.show', $period)
            ->with('success', "Periode penggajian '{$period->name}' berhasil dibuat. Silakan jalankan kalkulasi payroll.");
    }

    /**
     * Tampilan Matriks Penggajian Periode Tertentu & Daftar Slip Gaji.
     */
    public function show(PayrollPeriod $period): View
    {
        $period->load([
            'payslips.employee.department',
            'payslips.employee.position',
            'payslips.items',
        ]);

        $employeesWithoutSalary = Employee::where('is_active', true)
            ->whereDoesntHave('salaryStructure')
            ->count();

        return view('modules.payroll.show', compact('period', 'employeesWithoutSalary'));
    }

    /**
     * Eksekusi Kalkulasi Payroll Batch untuk Seluruh Karyawan Aktif.
     */
    public function generate(PayrollPeriod $period, PayrollEngineService $engine): RedirectResponse
    {
        // Jalankan batch kalkulasi
        ProcessPayrollBatchJob::dispatchSync($period);

        return redirect()->route('payroll.periods.show', $period)
            ->with('success', "Kalkulasi payroll periode '{$period->name}' berhasil diproses dan data slip gaji telah diperbarui.");
    }

    /**
     * Tampilan Lembar Slip Gaji Resmi Karyawan (Print & PDF Ready).
     */
    public function showPayslip(Payslip $payslip): View
    {
        $payslip->load([
            'employee.department',
            'employee.position',
            'payrollPeriod',
            'items',
        ]);

        return view('modules.payroll.payslip-detail', compact('payslip'));
    }
}
