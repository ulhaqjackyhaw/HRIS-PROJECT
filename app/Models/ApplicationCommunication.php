<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id',
        'sent_by',
        'channel',
        'subject',
        'message',
        'recipient',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sent_by');
    }
}
