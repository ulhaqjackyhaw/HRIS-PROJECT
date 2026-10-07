<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Psychotest extends Model
{
    use HasFactory;

    public const TYPE_KRAEPELIN = 'KRAEPELIN';

    public const TYPE_LIKERT_PERSONALITY = 'LIKERT_PERSONALITY';

    public const TYPE_GENERAL = 'GENERAL';

    protected $fillable = [
        'title',
        'test_type',
        'description',
        'duration_minutes',
        'passing_score',
        'questions_data',
        'is_active',
    ];

    public function isKraepelin(): bool
    {
        return $this->test_type === self::TYPE_KRAEPELIN;
    }

    public function isLikertPersonality(): bool
    {
        return $this->test_type === self::TYPE_LIKERT_PERSONALITY;
    }

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'passing_score' => 'integer',
            'questions_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(CandidatePsychotestResult::class);
    }
}
