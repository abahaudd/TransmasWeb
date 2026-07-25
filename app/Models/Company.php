<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_COMPANY = 'Company';
    public const TYPE_BRANCH = 'Branch';
    public const TYPE_WAREHOUSE = 'Warehouse';
    public const TYPE_FACTORY = 'Factory';
    public const TYPE_OFFICE = 'Office';

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_INACTIVE = 'Inactive';

    protected $fillable = [
        'parent_id',
        'company_type',
        'company_code',
        'legal_name',
        'trade_name',
        'display_name',
        'status',
        'email',
        'website',
        'tax_country_id',
        'currency_id',
        'language_id',
        'timezone',
        'start_work_hour',
        'end_work_hour',
        'weekends',
        'incorporation_date',
        'financial_year_start',
        'logo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'incorporation_date' => 'date',
            'financial_year_start' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Company $company): void {
            $company->guardParentHierarchy();
        });
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_COMPANY => 'Company',
            self::TYPE_BRANCH => 'Branch',
            self::TYPE_WAREHOUSE => 'Warehouse',
            self::TYPE_FACTORY => 'Factory',
            self::TYPE_OFFICE => 'Office',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Best available label for a company record, falling back through the
     * display, trade, and legal names in order of preference.
     */
    public function displayLabel(): string
    {
        return $this->display_name ?: ($this->trade_name ?: $this->legal_name);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function taxCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'tax_country_id');
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    public function governmentRegistrations(): HasMany
    {
        return $this->hasMany(GovernmentRegistration::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return array<int>
     */
    public function getDescendantIds(): array
    {
        if (! $this->exists) {
            return [];
        }

        $descendantIds = [];
        $pendingParentIds = [$this->getKey()];

        while (! empty($pendingParentIds)) {
            $childIds = self::query()
                ->whereIn('parent_id', $pendingParentIds)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            $newChildIds = array_values(array_diff($childIds, $descendantIds));

            if (empty($newChildIds)) {
                break;
            }

            $descendantIds = array_values(array_unique([...$descendantIds, ...$newChildIds]));
            $pendingParentIds = $newChildIds;
        }

        return $descendantIds;
    }

    protected function guardParentHierarchy(): void
    {
        if (! filled($this->parent_id)) {
            return;
        }

        $parentId = (int) $this->parent_id;

        if (! self::query()->whereKey($parentId)->exists()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Selected parent company is invalid.',
            ]);
        }

        if (! $this->exists) {
            return;
        }

        if ($parentId === (int) $this->getKey()) {
            throw ValidationException::withMessages([
                'parent_id' => 'A company cannot be its own parent.',
            ]);
        }

        if (in_array($parentId, $this->getDescendantIds(), true)) {
            throw ValidationException::withMessages([
                'parent_id' => 'Invalid parent company. You cannot assign a child company/branch as parent.',
            ]);
        }
    }
}
