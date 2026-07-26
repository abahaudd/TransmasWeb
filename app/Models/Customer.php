<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'address_id',
        'phone',
        'email',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Customer $customer): void {
            $customer->guardParentHierarchy();
        });

        static::creating(function (Customer $customer): void {
            $userName = auth()->user()?->name ?? 'system';

            $customer->created_by ??= $userName;
            $customer->updated_by ??= $userName;
        });

        static::updating(function (Customer $customer): void {
            $customer->updated_by = auth()->user()?->name ?? 'system';
        });

        static::deleting(function (Customer $customer): void {
            if (! $customer->isForceDeleting()) {
                $customer->deleted_by = auth()->user()?->name ?? 'system';
                $customer->saveQuietly();
            }
        });
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function branchEmployees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function companyDocuments(): MorphMany
    {
        return $this->morphMany(CompanyDocument::class, 'documentable');
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
                'parent_id' => __('errors.customer.invalid_parent'),
            ]);
        }

        if (! $this->exists) {
            return;
        }

        if ($parentId === (int) $this->getKey()) {
            throw ValidationException::withMessages([
                'parent_id' => __('errors.customer.self_parent'),
            ]);
        }

        if (in_array($parentId, $this->getDescendantIds(), true)) {
            throw ValidationException::withMessages([
                'parent_id' => __('errors.customer.circular_parent'),
            ]);
        }
    }
}
