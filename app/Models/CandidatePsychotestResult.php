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
    ];

    protected function casts(): array
    {
        return [
            'answers_submitted' => 'array',
            'total_score' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function psychotest(): BelongsTo
    {
        return $this->belongsTo(Psychotest::class);
    }
}
