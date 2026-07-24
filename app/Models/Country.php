<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country_code',
        'country_code_alpha3',
        'location_title',
        'territory_title',
        'postal_code_title',
    ];

    /**
     * Get the addresses that belong to this country.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}