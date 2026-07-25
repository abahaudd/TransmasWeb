<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonAddress extends Model
{
    public const TYPE_CURRENT = 'Current';
    public const TYPE_PERMANENT = 'Permanent';
    public const TYPE_MAILING = 'Mailing';
    public const TYPE_OTHER = 'Other';

    protected $fillable = [
        'person_id',
        'address_type',
        'address',
        'location',
        'territory',
        'postal_code',
        'country_id',
        'is_primary',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_CURRENT => 'Current',
            self::TYPE_PERMANENT => 'Permanent',
            self::TYPE_MAILING => 'Mailing',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
