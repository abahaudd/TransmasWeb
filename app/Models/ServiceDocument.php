<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDocument extends Model
{
    protected $fillable = [
        'service_id',
        'document_id',
        'is_mandatory',
        'remarks',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'sequence' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
