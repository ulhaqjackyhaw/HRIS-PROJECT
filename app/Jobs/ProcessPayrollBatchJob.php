<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Services\PayrollEngineService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayrollBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public PayrollPeriod $period) {}

    public function handle(PayrollEngineService $engine): void
    {
        $this->period->update(['status' => 'PROCESSING']);

        $employees = Employee::where('is_active', true)->has('salaryStructure')->get();

        foreach ($employees as $employee) {
            try {
                $engine->computeEmployeePayroll($this->period, $employee);
            } catch (Exception $e) {
                logger()->error("Payroll calculation error for employee {$employee->id}: ".$e->getMessage());
            }
        }

        // Update total kumulatif periode
        $this->period->update([
            'status' => 'APPROVED',
            'total_gross_amount' => (float) $this->period->payslips()->sum('gross_salary'),
            'total_deduction_amount' => (float) $this->period->payslips()->sum('total_deductions'),
            'total_net_amount' => (float) $this->period->payslips()->sum('net_salary'),
        ]);
    }
}
