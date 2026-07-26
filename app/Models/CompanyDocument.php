<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CompanyDocument extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_id',
        'document_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    /**
     * The Company or Customer this document instance belongs to.
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function isExpired(): bool
    {
        return (bool) $this->expiry_date?->isPast();
    }
}
