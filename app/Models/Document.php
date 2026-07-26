<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    public const GROUP_VISA = 'Visa';
    public const GROUP_BUSINESS_SETUP = 'Business Setup';
    public const GROUP_EMPLOYMENT = 'Employment';
    public const GROUP_LEGAL = 'Legal';
    public const GROUP_IDENTITY = 'Identity';
    public const GROUP_FINANCIAL = 'Financial';

    protected $fillable = [
        'group',
        'name',
        'issuing_authority',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function groupOptions(): array
    {
        return [
            self::GROUP_VISA => 'Visa',
            self::GROUP_BUSINESS_SETUP => 'Business Setup',
            self::GROUP_EMPLOYMENT => 'Employment',
            self::GROUP_LEGAL => 'Legal',
            self::GROUP_IDENTITY => 'Identity',
            self::GROUP_FINANCIAL => 'Financial',
        ];
    }

    public function serviceDocuments(): HasMany
    {
        return $this->hasMany(ServiceDocument::class);
    }

    public function companyDocuments(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function employeeDocuments(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }
}
