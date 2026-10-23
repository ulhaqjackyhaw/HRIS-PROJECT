<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'department_id',
        'position_id',
        'employment_type',
        'work_model',
        'location',
        'experience_level',
        'min_salary',
        'max_salary',
        'salary_currency',
        'is_salary_visible',
        'description',
        'requirements',
        'benefits',
        'quota',
        'deadline',
        'status',
        'views_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'is_salary_visible' => 'boolean',
            'quota' => 'integer',
            'views_count' => 'integer',
            'deadline' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobPosting $job) {
            if (empty($job->slug)) {
                $baseSlug = Str::slug($job->title);
                $uniqueSlug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $uniqueSlug)->exists()) {
                    $uniqueSlug = "{$baseSlug}-{$counter}";
                    $counter++;
                }
                $job->slug = $uniqueSlug;
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'PUBLISHED')
            ->where(function (Builder $q) {
                $q->whereNull('deadline')
                    ->orWhere('deadline', '>=', now()->toDateString());
            });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('department', function (Builder $d) use ($search) {
                            $d->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['department_id'] ?? null, function (Builder $q, $deptId) {
                $q->where('department_id', $deptId);
            })
            ->when($filters['employment_type'] ?? null, function (Builder $q, string $type) {
                $q->where('employment_type', $type);
            })
            ->when($filters['work_model'] ?? null, function (Builder $q, string $model) {
                $q->where('work_model', $model);
            })
            ->when($filters['location'] ?? null, function (Builder $q, string $loc) {
                $q->where('location', 'like', "%{$loc}%");
            });
    }

    public function getSalaryFormattedAttribute(): string
    {
        if (! $this->is_salary_visible || (! $this->min_salary && ! $this->max_salary)) {
            return 'Kompetitif';
        }

        $min = $this->min_salary ? 'Rp '.number_format($this->min_salary, 0, ',', '.') : null;
        $max = $this->max_salary ? 'Rp '.number_format($this->max_salary, 0, ',', '.') : null;

        if ($min && $max) {
            return "{$min} - {$max}";
        }

        return $min ? "Mulai dari {$min}" : "Hingga {$max}";
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return match ($this->employment_type) {
            'FULL_TIME' => 'Full-time',
            'CONTRACT' => 'Kontrak (PKWT)',
            'INTERNSHIP' => 'Magang',
            'PART_TIME' => 'Part-time',
            default => $this->employment_type,
        };
    }

    public function getWorkModelLabelAttribute(): string
    {
        return match ($this->work_model) {
            'ON_SITE' => 'On-site (WFO)',
            'HYBRID' => 'Hybrid',
            'REMOTE' => 'Remote (WFH)',
            default => $this->work_model,
        };
    }
}
