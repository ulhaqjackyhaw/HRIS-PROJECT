<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatePsychotestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'psychotest_id',
        'answers_submitted',
        'total_score',
        'result_status',
        'completed_at',
        'can_retake',
        'retake_reason',
        'retake_granted_at',
        'retake_granted_by',
        'attempt_number',
    ];

    protected function casts(): array
    {
        return [
            'answers_submitted' => 'array',
            'total_score' => 'integer',
            'completed_at' => 'datetime',
            'can_retake' => 'boolean',
            'retake_granted_at' => 'datetime',
            'attempt_number' => 'integer',
        ];
    }

    public function isLocked(): bool
    {
        return $this->completed_at !== null && ! $this->can_retake;
    }

    public function getIsLockedAttribute(): bool
    {
        return $this->isLocked();
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function psychotest(): BelongsTo
    {
        return $this->belongsTo(Psychotest::class);
    }

    public function retakeGrantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retake_granted_by');
    }
}
