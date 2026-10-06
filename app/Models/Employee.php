<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'manager_id',
        'nik',
        'employment_status',
        'lifecycle_stage',
        'join_date',
        'end_date',
        'current_contract_no',
        'work_location',
        'is_active',
        'ktp_number',
        'full_name',
        'gender',
        'birth_date',
        'religion',
        'marital_status',
        'ktp_address',
        'current_address',
        'email',
        'phone_number',
        'npwp',
        'ptkp_status',
        'bpjs_ketenagakerjaan_no',
        'bpjs_kesehatan_no',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'annual_leave_quota',
        'annual_leave_used',
        'custom_fields',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'end_date' => 'date',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'annual_leave_quota' => 'integer',
            'annual_leave_used' => 'integer',
            'custom_fields' => 'array',
        ];
    }

    /**
     * Sisa Saldo Cuti Tahunan Berjalan.
     */
    public function getRemainingAnnualLeaveAttribute(): int
    {
        return max(0, (int) ($this->annual_leave_quota ?? 12) - (int) ($this->annual_leave_used ?? 0));
    }

    /**
     * Menghitung Usia Karyawan Otomatis.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    /**
     * Menghitung Masa Kerja Otomatis (Contoh: "2 Tahun 4 Bulan").
     */
    public function getTenureAttribute(): ?string
    {
        if (! $this->join_date) {
            return null;
        }

        $diff = Carbon::parse($this->join_date)->diff(Carbon::now());

        return "{$diff->y} Tahun {$diff->m} Bulan";
    }

    /**
     * Related application user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Department unit.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Assigned position.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Direct manager / supervisor.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Direct subordinates.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Education history.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    /**
     * Employment contracts history.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    /**
     * Career movement history (promotions, mutations, demotions, exits).
     */
    public function careerHistories(): HasMany
    {
        return $this->hasMany(EmployeeCareerHistory::class)->orderByDesc('effective_date');
    }

    /**
     * Work schedules and rosters.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    /**
     * Daily attendance logs.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class)->orderByDesc('date');
    }

    /**
     * Overtime requests.
     */
    public function overtimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class)->orderByDesc('date');
    }

    /**
     * Leave and time-off requests.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class)->orderByDesc('start_date');
    }

    /**
     * Compensation and salary structure.
     */
    public function salaryStructure(): HasOne
    {
        return $this->hasOne(SalaryStructure::class);
    }

    /**
     * Issued payslips across payroll periods.
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class)->orderByDesc('created_at');
    }

    /**
     * Expense claims and reimbursements.
     */
    public function expenseClaims(): HasMany
    {
        return $this->hasMany(ExpenseClaim::class)->orderByDesc('claim_date');
    }
}
