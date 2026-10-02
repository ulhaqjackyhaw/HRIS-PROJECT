<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'employee_educations';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'education_level',
        'major',
        'institution_name',
        'graduation_year',
        'is_recognized',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'graduation_year' => 'integer',
            'is_recognized' => 'boolean',
        ];
    }

    /**
     * Employee to whom this education belongs.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
