<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SequenceNumberFormat extends Model
{
    protected $fillable = [
        'category',
        'prefix',
        'separator',
        'incrementer',
        'length',
    ];

    protected function casts(): array
    {
        return [
            'incrementer' => 'integer',
            'length' => 'integer',
        ];
    }
}
