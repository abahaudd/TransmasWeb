<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GovernmentRegistration extends Model
{
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_EXPIRED = 'Expired';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_PENDING = 'Pending';

    /**
     * Typical registration types. Data-driven — new types don't need schema
     * changes, this list only seeds sensible Filament select options.
     *
     * @var array<int, string>
     */
    public const COMMON_TYPES = [
        'Trade License',
        'Commercial Registration',
        'VAT Registration',
        'Corporate Tax Registration',
        'Chamber of Commerce',
        'Import License',
        'Export License',
        'Municipality License',
        'Labour Registration',
        'Customs Registration',
        'Food License',
        'ISO Certificate',
        'Insurance Policy',
    ];

    protected $fillable = [
        'company_id',
        'registration_type',
        'registration_number',
        'country_id',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'status',
        'document_path',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_PENDING => 'Pending',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function isExpired(): bool
    {
        return (bool) $this->expiry_date?->isPast();
    }
}
