<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceComponent extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('sequence');
    }

    /**
     * Every service workflow step that references this component as a whole.
     */
    public function workflowSteps(): MorphMany
    {
        return $this->morphMany(ServiceWorkflowStep::class, 'step');
    }

    public function totalCost(): string
    {
        return (string) $this->tasks()->sum('cost');
    }
}
