<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentStatus extends Model
{
    protected $fillable = [
        'name',
        'is_terminal',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_terminal' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
