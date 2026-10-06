<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'ktp_expiry' => 'date',
            'npwp_expiry' => 'date',
            'passport_expiry' => 'date',
            'bpjs_tk_expiry' => 'date',
            'marriage_cert_expiry' => 'date',
            'sim_a_expiry' => 'date',
            'sim_c_expiry' => 'date',
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
            'vehicles_data' => 'array',
            'emergency_contact' => 'array',
            'family_father' => 'array',
            'family_mother' => 'array',
            'family_siblings' => 'array',
            'family_core' => 'array',
            'education_formal' => 'array',
            'education_non_formal' => 'array',
            'organizations' => 'array',
            'languages' => 'array',
            'work_experiences' => 'array',
            'references_data' => 'array',
            'applied_before' => 'boolean',
            'willing_to_relocate' => 'boolean',
            'agreement_signed' => 'boolean',
            'is_completed' => 'boolean',
            'expected_salary' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
