<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Person extends Model implements HasMedia
{
    use InteractsWithMedia, LogsModelActivity;

    // Eloquent would otherwise pluralize Person to "people"
    protected $table = 'persons';

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'nationality',
        'national_id',
        'phone',
        'mobile',
        'email',
        'blood_group',
        'address_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Multiple addresses on file (current, permanent, ...) — distinct from
     * the single legacy `address()` (address_id) relation above.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(PersonAddress::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function toModalData(): array
    {
        return [
            'FullName' => $this->full_name,
            'Address' => $this->address?->address,
        ];
    }

    // Define the media collection
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile()                          // Only allow one avatar per person
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }
}
