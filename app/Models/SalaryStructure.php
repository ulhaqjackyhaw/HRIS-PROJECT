<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'fixed_allowance',
        'daily_transport_allowance',
        'daily_meal_allowance',
        'use_bpjs_tk',
        'use_bpjs_kes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'basic_salary' => 'float',
            'fixed_allowance' => 'float',
            'daily_transport_allowance' => 'float',
            'daily_meal_allowance' => 'float',
            'use_bpjs_tk' => 'boolean',
            'use_bpjs_kes' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
