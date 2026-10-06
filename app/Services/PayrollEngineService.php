<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\PayrollPeriod;
use App\Models\Payslip;
use Exception;
use Illuminate\Support\Facades\DB;

class PayrollEngineService
{
    /**
     * Hitung PPh 21 skema TER Bulanan (PP 58/2023)
     */
    public function calculatePph21Ter(string $ptkpStatus, float $grossAmount): float
    {
        // 1. Tentukan Kategori TER
        $categoryB = ['TK/2', 'TK/3', 'K/1', 'K/2'];
        $categoryC = ['K/3'];

        $terCategory = 'A';
        if (in_array($ptkpStatus, $categoryB)) {
            $terCategory = 'B';
        }
        if (in_array($ptkpStatus, $categoryC)) {
            $terCategory = 'C';
        }

        // 2. Tentukan Tarif Berdasarkan Kategori & Rentang Bruto
        $rate = 0.0;
        if ($terCategory === 'A') {
            if ($grossAmount <= 5400000) {
                $rate = 0.0;
            } elseif ($grossAmount <= 5650000) {
                $rate = 0.0025;
            } elseif ($grossAmount <= 5950000) {
                $rate = 0.005;
            } elseif ($grossAmount <= 6300000) {
                $rate = 0.0075;
            } elseif ($grossAmount <= 6750000) {
                $rate = 0.01;
            } elseif ($grossAmount <= 7500000) {
                $rate = 0.0125;
            } elseif ($grossAmount <= 8550000) {
                $rate = 0.015;
            } elseif ($grossAmount <= 9650000) {
                $rate = 0.0175;
            } elseif ($grossAmount <= 10050000) {
                $rate = 0.02;
            } else {
                $rate = 0.05;
            }
        } elseif ($terCategory === 'B') {
            if ($grossAmount <= 6200000) {
                $rate = 0.0;
            } elseif ($grossAmount <= 6500000) {
                $rate = 0.0025;
            } elseif ($grossAmount <= 6850000) {
                $rate = 0.005;
            } elseif ($grossAmount <= 7200000) {
                $rate = 0.0075;
            } elseif ($grossAmount <= 7600000) {
                $rate = 0.01;
            } else {
                $rate = 0.03;
            }
        } else { // Kategori C
            if ($grossAmount <= 6600000) {
                $rate = 0.0;
            } elseif ($grossAmount <= 6950000) {
                $rate = 0.0025;
            } elseif ($grossAmount <= 7350000) {
                $rate = 0.005;
            } else {
                $rate = 0.02;
            }
        }

        return round($grossAmount * $rate, 2);
    }

    /**
     * Hitung Gaji 1 Karyawan untuk Suatu Periode
     */
    public function computeEmployeePayroll(PayrollPeriod $period, Employee $employee): Payslip
    {
        $salary = $employee->salaryStructure;
        if (! $salary) {
            throw new Exception("Karyawan {$employee->full_name} belum memiliki Struktur Gaji.");
        }

        return DB::transaction(function () use ($period, $employee, $salary) {
            // A. Tarik Rekapitulasi Presensi
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$period->start_date, $period->end_date])
                ->get();

            $presentDays = $attendances->whereIn('status', ['PRESENT', 'LATE'])->count();
            $absentDays = $attendances->where('status', 'ABSENT')->count();
            $lateMinutes = (int) $attendances->sum('late_minutes');

            // Cek izin Unpaid (Cuti Tidak Berbayar)
            $unpaidLeaves = LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'APPROVED')
                ->where(function ($q) {
                    $q->where('type', 'UNPAID')
                        ->orWhereHas('leaveType', fn ($sub) => $sub->where('is_paid', false));
                })
                ->whereBetween('start_date', [$period->start_date, $period->end_date])
                ->sum('total_days');

            // Cek jam lembur yang disetujui atasan
            $overtimeHours = (float) OvertimeRequest::where('employee_id', $employee->id)
                ->where('status', 'APPROVED')
                ->whereBetween('date', [$period->start_date, $period->end_date])
                ->sum('total_hours');

            // Cek klaim reimbursement yang sudah di-approve
            $reimbursements = ExpenseClaim::where('employee_id', $employee->id)
                ->where('status', 'APPROVED')
                ->whereBetween('claim_date', [$period->start_date, $period->end_date])
                ->get();

            $totalReimbursement = (float) $reimbursements->sum('amount');

            // Hubungkan klaim ke periode payroll ini
            ExpenseClaim::whereIn('id', $reimbursements->pluck('id'))
                ->update(['payroll_period_id' => $period->id]);

            // B. Perhitungan Pendapatan (Gross)
            $basicSalary = $salary->basic_salary;
            $fixedAllowance = $salary->fixed_allowance;
            $mealTotal = $salary->daily_meal_allowance * $presentDays;
            $transportTotal = $salary->daily_transport_allowance * $presentDays;

            // Upah lembur standar Depnaker: (1/173) * Gaji Pokok
            $hourlyRate = $basicSalary / 173;
            $overtimeTotal = round($overtimeHours * $hourlyRate, 2);

            $grossSalary = round($basicSalary + $fixedAllowance + $mealTotal + $transportTotal + $overtimeTotal, 2);

            // C. Perhitungan Potongan (Deductions)
            // 1. Potongan Keterlambatan (Misal Rp 1.000 / menit)
            $lateDeduction = $lateMinutes * 1000;

            // 2. Potongan Alpa / Unpaid Leave (Asumsi 22 hari kerja per bulan)
            $unpaidDeduction = round((($absentDays + $unpaidLeaves) / 22) * $basicSalary, 2);

            // 3. BPJS Ketenagakerjaan (Pekerja: JHT 2% + JP 1% = 3%)
            $bpjsTkDeduction = 0.0;
            if ($salary->use_bpjs_tk) {
                $bpjsTkDeduction = round($basicSalary * 0.03, 2);
            }

            // 4. BPJS Kesehatan (Pekerja: 1% batas max upah 12jt)
            $bpjsKesDeduction = 0.0;
            if ($salary->use_bpjs_kes) {
                $baseBpjsKes = min($basicSalary, 12000000);
                $bpjsKesDeduction = round($baseBpjsKes * 0.01, 2);
            }

            // 5. PPh 21 TER
            $pph21Deduction = $this->calculatePph21Ter($employee->ptkp_status ?? 'TK/0', $grossSalary);

            $totalDeductions = round($lateDeduction + $unpaidDeduction + $bpjsTkDeduction + $bpjsKesDeduction + $pph21Deduction, 2);

            // D. Net Salary (THP)
            $netSalary = round($grossSalary - $totalDeductions + $totalReimbursement, 2);

            // E. Simpan ke Payslip Snapshot
            $payslip = Payslip::updateOrCreate(
                [
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'unpaid_leave_days' => $unpaidLeaves,
                    'late_minutes' => $lateMinutes,
                    'overtime_hours' => $overtimeHours,
                    'gross_salary' => $grossSalary,
                    'total_deductions' => $totalDeductions,
                    'total_reimbursements' => $totalReimbursement,
                    'net_salary' => $netSalary,
                    'status' => 'DRAFT',
                ]
            );

            // Bersihkan item lama & isi ulang rincian
            $payslip->items()->delete();

            $items = [
                ['name' => 'Gaji Pokok', 'type' => 'EARNING', 'amount' => $basicSalary],
                ['name' => 'Tunjangan Tetap', 'type' => 'EARNING', 'amount' => $fixedAllowance],
                ['name' => 'Uang Makan', 'type' => 'EARNING', 'amount' => $mealTotal],
                ['name' => 'Uang Transport', 'type' => 'EARNING', 'amount' => $transportTotal],
                ['name' => 'Upah Lembur', 'type' => 'EARNING', 'amount' => $overtimeTotal],

                ['name' => 'Denda Keterlambatan', 'type' => 'DEDUCTION', 'amount' => $lateDeduction],
                ['name' => 'Potongan Alpa / Unpaid Leave', 'type' => 'DEDUCTION', 'amount' => $unpaidDeduction],
                ['name' => 'BPJS Ketenagakerjaan (3%)', 'type' => 'DEDUCTION', 'amount' => $bpjsTkDeduction],
                ['name' => 'BPJS Kesehatan (1%)', 'type' => 'DEDUCTION', 'amount' => $bpjsKesDeduction],
                ['name' => 'PPh 21 TER', 'type' => 'DEDUCTION', 'amount' => $pph21Deduction],
            ];

            foreach ($reimbursements as $claim) {
                $items[] = [
                    'name' => 'Reimbursement: '.$claim->title,
                    'type' => 'REIMBURSEMENT',
                    'amount' => $claim->amount,
                ];
            }

            foreach ($items as $item) {
                if ($item['amount'] > 0) {
                    $payslip->items()->create($item);
                }
            }

            return $payslip;
        });
    }
}
