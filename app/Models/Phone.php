<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Phone extends Model
{
    public const TYPE_OFFICE = 'Office';
    public const TYPE_MOBILE = 'Mobile';
    public const TYPE_WHATSAPP = 'WhatsApp';
    public const TYPE_FAX = 'Fax';
    public const TYPE_TOLL_FREE = 'TollFree';
    public const TYPE_EMERGENCY = 'Emergency';

    protected $fillable = [
        'company_id',
        'phone_type',
        'country_code',
        'phone_number',
        'extension',
        'contact_name',
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
            self::TYPE_OFFICE => 'Office',
            self::TYPE_MOBILE => 'Mobile',
            self::TYPE_WHATSAPP => 'WhatsApp',
            self::TYPE_FAX => 'Fax',
            self::TYPE_TOLL_FREE => 'TollFree',
            self::TYPE_EMERGENCY => 'Emergency',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
