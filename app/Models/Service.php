<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Service extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_INACTIVE = 'Inactive';

    protected $fillable = [
        'service_category_id',
        'name',
        'code',
        'description',
        'cost',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function workflowSteps(): HasMany
    {
        return $this->hasMany(ServiceWorkflowStep::class)->orderBy('sequence');
    }

    public function serviceDocuments(): HasMany
    {
        return $this->hasMany(ServiceDocument::class)->orderBy('sequence');
    }

    /**
     * The ordered, expanded list of tasks this service's workflow actually
     * runs — service-component steps are expanded into their member tasks
     * (in their own component sequence), task steps are included as-is.
     *
     * @return Collection<int, Task>
     */
    public function resolvedTasks(): Collection
    {
        $tasks = collect();

        foreach ($this->workflowSteps()->with('step')->get() as $step) {
            if ($step->step_type === ServiceWorkflowStep::TYPE_TASK && $step->step instanceof Task) {
                $tasks->push($step->step);
            } elseif ($step->step_type === ServiceWorkflowStep::TYPE_SERVICE_COMPONENT && $step->step instanceof ServiceComponent) {
                $tasks = $tasks->merge($step->step->tasks);
            }
        }

        return $tasks->values();
    }

    /**
     * Sum of the resolved workflow's task costs — a reference figure to
     * compare against the stored `cost` attribute, not a replacement for it.
     */
    public function workflowCost(): string
    {
        return (string) $this->resolvedTasks()->sum(fn (Task $task): float => (float) $task->cost);
    }
}
