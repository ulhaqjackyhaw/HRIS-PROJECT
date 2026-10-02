<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_in_lat',
        'clock_in_lng',
        'clock_in_distance_meters',
        'clock_in_photo_path',
        'clock_out',
        'clock_out_lat',
        'clock_out_lng',
        'clock_out_distance_meters',
        'clock_out_photo_path',
        'status',
        'late_minutes',
        'early_leave_minutes',
        'total_work_minutes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'clock_in_lat' => 'float',
            'clock_in_lng' => 'float',
            'clock_in_distance_meters' => 'float',
            'clock_out_lat' => 'float',
            'clock_out_lng' => 'float',
            'clock_out_distance_meters' => 'float',
            'late_minutes' => 'integer',
            'early_leave_minutes' => 'integer',
            'total_work_minutes' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
