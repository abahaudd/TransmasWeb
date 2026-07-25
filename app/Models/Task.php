<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    protected $fillable = [
        'service_component_id',
        'name',
        'code',
        'description',
        'cost',
        'government_department_id',
        'sequence',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function serviceComponent(): BelongsTo
    {
        return $this->belongsTo(ServiceComponent::class);
    }

    public function governmentDepartment(): BelongsTo
    {
        return $this->belongsTo(GovernmentDepartment::class);
    }

    /**
     * Every service workflow step that references this task directly.
     */
    public function workflowSteps(): MorphMany
    {
        return $this->morphMany(ServiceWorkflowStep::class, 'step');
    }
}
