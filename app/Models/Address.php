<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'address',
        'location',
        'territory',
        'postal_code',
        'country_id',
        'latitude',
        'longitude',
    ];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
