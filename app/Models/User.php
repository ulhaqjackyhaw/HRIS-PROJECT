<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'resume_path', 'password', 'user_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public const TYPE_HR = 'HR';

    public const TYPE_EMPLOYEE = 'EMPLOYEE';

    public const TYPE_CANDIDATE = 'CANDIDATE';

    public function isCandidate(): bool
    {
        return ($this->user_type ?? self::TYPE_CANDIDATE) === self::TYPE_CANDIDATE;
    }

    public function isInternal(): bool
    {
        return in_array($this->user_type, [self::TYPE_HR, self::TYPE_EMPLOYEE, 'INTERNAL'], true);
    }

    public function isHr(): bool
    {
        if ($this->user_type === self::TYPE_HR || $this->user_type === 'INTERNAL') {
            return true;
        }

        return false;
    }

    public function isEmployee(): bool
    {
        if ($this->user_type === self::TYPE_EMPLOYEE) {
            return true;
        }

        return $this->isInternal();
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }
}
