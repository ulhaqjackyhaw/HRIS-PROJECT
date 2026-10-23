<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'type',
        'office_location_id',
        'reduces_annual_leave',
        'is_workday_override',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'reduces_annual_leave' => 'boolean',
            'is_workday_override' => 'boolean',
        ];
    }

    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Memeriksa apakah tanggal tertentu adalah hari libur (nasional atau spesifik kantor).
     */
    public static function getHolidayForDate(string|Carbon $date, ?int $locationId = null): ?self
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return self::whereDate('date', $dateStr)
            ->where(function ($q) use ($locationId) {
                $q->whereNull('office_location_id');
                if ($locationId) {
                    $q->orWhere('office_location_id', $locationId);
                }
            })
            ->first();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'NATIONAL' => 'Hari Libur Nasional',
            'MASS_LEAVE' => 'Cuti Bersama (Mass Leave)',
            'COMPANY' => 'Libur Perusahaan',
            'REGIONAL' => 'Libur Regional / Cabang',
            default => 'Hari Libur',
        };
    }
}
