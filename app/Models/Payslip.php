<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'present_days',
        'absent_days',
        'unpaid_leave_days',
        'late_minutes',
        'overtime_hours',
        'gross_salary',
        'total_deductions',
        'total_reimbursements',
        'net_salary',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_salary' => 'float',
            'total_deductions' => 'float',
            'total_reimbursements' => 'float',
            'net_salary' => 'float',
            'overtime_hours' => 'float',
        ];
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayslipItem::class);
    }
}
