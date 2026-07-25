<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ServiceWorkflowStep extends Model
{
    public const TYPE_TASK = 'task';
    public const TYPE_SERVICE_COMPONENT = 'service_component';

    protected $fillable = [
        'service_id',
        'step_type',
        'step_id',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_TASK => 'Task',
            self::TYPE_SERVICE_COMPONENT => 'Service Component',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The Task or ServiceComponent this step runs.
     */
    public function step(): MorphTo
    {
        return $this->morphTo();
    }

    public function label(): string
    {
        return match ($this->step_type) {
            self::TYPE_TASK => $this->step?->name ?? 'Task #' . $this->step_id,
            self::TYPE_SERVICE_COMPONENT => $this->step?->name ?? 'Service Component #' . $this->step_id,
            default => (string) $this->step_type,
        };
    }
}
