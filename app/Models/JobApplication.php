<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    public const STAGES = [
        'APPLIED' => [
            'label' => 'Berkas Terkirim',
            'order' => 1,
            'description' => 'Lamaran diterima & menunggu screening berkas oleh tim HR.',
        ],
        'SHORTLISTED' => [
            'label' => 'Shortlisted / Psikotes',
            'order' => 2,
            'description' => 'Lolos seleksi CV, kandidat berhak mengikuti asesmen psikotes online.',
        ],
        'PSYCHOTEST_PASSED' => [
            'label' => 'Lolos Psikotes',
            'order' => 3,
            'description' => 'Hasil skor psikotes memenuhi standar kualifikasi posisi.',
        ],
        'INTERVIEW_HR' => [
            'label' => 'Interview HR',
            'order' => 4,
            'description' => 'Wawancara kompetensi dan kesesuaian budaya kerja dengan HR.',
        ],
        'INTERVIEW_USER' => [
            'label' => 'Interview User',
            'order' => 5,
            'description' => 'Wawancara teknis & studi kasus bersama Hiring Manager / Atasan.',
        ],
        'INTERVIEW_BOD' => [
            'label' => 'Interview Direksi (BOD)',
            'order' => 6,
            'description' => 'Diskusi visi strategis dan finalisasi bersama jajaran Direksi.',
        ],
        'MCU' => [
            'label' => 'Medical Check-Up (MCU)',
            'order' => 7,
            'description' => 'Pemeriksaan kesehatan di klinik / lab rekanan resmi.',
        ],
        'OFFERING' => [
            'label' => 'Offering & Onboarding',
            'order' => 8,
            'description' => 'Penerbitan Surat Penawaran Kerja (Offering Letter) resmi.',
        ],
        'HIRED' => [
            'label' => 'Diterima (Core HR Employee)',
            'order' => 9,
            'description' => 'Resmi bergabung dan dikonversi menjadi karyawan aktif di Core HR.',
        ],
        'REJECTED' => [
            'label' => 'Belum Sesuai',
            'order' => -1,
            'description' => 'Profil belum sesuai dengan kualifikasi posisi saat ini.',
        ],
    ];

    protected $fillable = [
        'job_posting_id',
        'user_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'resume_path',
        'cover_letter',
        'portfolio_url',
        'linkedin_url',
        'current_stage',
        'stage_notes',
        'interview_scheduled_at',
        'interview_location',
        'mcu_document_path',
        'offering_letter_path',
        'offering_salary',
        'employee_id',
        'applied_at',
        'hired_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'hired_at' => 'datetime',
            'interview_scheduled_at' => 'datetime',
            'offering_salary' => 'decimal:2',
        ];
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function psychotestResults(): HasMany
    {
        return $this->hasMany(CandidatePsychotestResult::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(ApplicationCommunication::class);
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->current_stage]['label'] ?? $this->current_stage;
    }

    public function getStageOrderAttribute(): int
    {
        return self::STAGES[$this->current_stage]['order'] ?? 1;
    }

    public function getStageDescriptionAttribute(): string
    {
        return self::STAGES[$this->current_stage]['description'] ?? '';
    }
}
