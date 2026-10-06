<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\PayrollPeriod;
use App\Models\SalaryStructure;
use App\Services\PayrollEngineService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollModuleSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->get();

        // 1. Data Struktur Gaji Realistis Standard Indonesia
        $salaries = [
            'HC-2021001' => [
                'basic_salary' => 18000000,
                'fixed_allowance' => 4500000,
                'daily_transport_allowance' => 50000,
                'daily_meal_allowance' => 40000,
            ],
            'HC-2025014' => [
                'basic_salary' => 7800000,
                'fixed_allowance' => 1200000,
                'daily_transport_allowance' => 30000,
                'daily_meal_allowance' => 30000,
            ],
            'IT-2024003' => [
                'basic_salary' => 14500000,
                'fixed_allowance' => 2500000,
                'daily_transport_allowance' => 40000,
                'daily_meal_allowance' => 35000,
            ],
            'EMP-2026-001' => [
                'basic_salary' => 9500000,
                'fixed_allowance' => 1500000,
                'daily_transport_allowance' => 35000,
                'daily_meal_allowance' => 30000,
            ],
        ];

        foreach ($employees as $employee) {
            $config = $salaries[$employee->nik] ?? [
                'basic_salary' => 8000000,
                'fixed_allowance' => 1000000,
                'daily_transport_allowance' => 30000,
                'daily_meal_allowance' => 30000,
            ];

            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'basic_salary' => $config['basic_salary'],
                    'fixed_allowance' => $config['fixed_allowance'],
                    'daily_transport_allowance' => $config['daily_transport_allowance'],
                    'daily_meal_allowance' => $config['daily_meal_allowance'],
                    'use_bpjs_tk' => true,
                    'use_bpjs_kes' => true,
                ]
            );
        }

        // 2. Periode Cut-Off Payroll Bulan Berjalan
        $period = PayrollPeriod::firstOrCreate(
            ['name' => 'Payroll Oktober 2026'],
            [
                'start_date' => '2026-09-21',
                'end_date' => '2026-10-20',
                'payout_date' => '2026-10-25',
                'status' => 'DRAFT',
            ]
        );

        // 3. Klaim Pengeluaran / Reimbursement yang Disetujui
        $staffPayroll = $employees->firstWhere('nik', 'HC-2025014');
        if ($staffPayroll) {
            ExpenseClaim::firstOrCreate(
                [
                    'employee_id' => $staffPayroll->id,
                    'title' => 'Bensin & Operasional Koordinasi Pajak Pratama',
                ],
                [
                    'payroll_period_id' => $period->id,
                    'amount' => 250000,
                    'claim_date' => Carbon::parse('2026-10-02'),
                    'status' => 'APPROVED',
                ]
            );
        }

        $itStaff = $employees->firstWhere('nik', 'IT-2024003');
        if ($itStaff) {
            ExpenseClaim::firstOrCreate(
                [
                    'employee_id' => $itStaff->id,
                    'title' => 'Lisensi Tools Developer & Server Cloud Testing',
                ],
                [
                    'payroll_period_id' => $period->id,
                    'amount' => 750000,
                    'claim_date' => Carbon::parse('2026-10-01'),
                    'status' => 'APPROVED',
                ]
            );
        }

        // 4. Hitung awal payroll menggunakan PayrollEngineService
        $engine = app(PayrollEngineService::class);
        foreach ($employees as $emp) {
            if ($emp->salaryStructure) {
                $engine->computeEmployeePayroll($period, $emp);
            }
        }

        $period->update([
            'status' => 'APPROVED',
            'total_gross_amount' => (float) $period->payslips()->sum('gross_salary'),
            'total_deduction_amount' => (float) $period->payslips()->sum('total_deductions'),
            'total_net_amount' => (float) $period->payslips()->sum('net_salary'),
        ]);
    }
}
