<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'person_id',
        'user_id',
        'employee_code',
        'department_id',
        'designation_id',
        'employment_type_id',
        'employment_status_id',
        'is_manager',
        'reporting_to_id',
        'joining_date',
        'confirmation_date',
        'end_date',
        'termination_reason',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_manager' => 'boolean',
            'joining_date' => 'date',
            'confirmation_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Employee $employee): void {
            $employee->guardReportingHierarchy();
        });

        static::creating(function (Employee $employee): void {
            $employee->created_by ??= auth()->id();
            $employee->updated_by ??= auth()->id();
        });

        static::updating(function (Employee $employee): void {
            $employee->updated_by = auth()->id();
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function reportingTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reporting_to_id');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(self::class, 'reporting_to_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function fullName(): string
    {
        return $this->person?->full_name ?? ('Employee #' . $this->getKey());
    }

    /**
     * All employee ids that (directly or transitively) report to this one.
     *
     * @return array<int>
     */
    public function getReportChainIds(): array
    {
        if (! $this->exists) {
            return [];
        }

        $reportIds = [];
        $pendingManagerIds = [$this->getKey()];

        while (! empty($pendingManagerIds)) {
            $childIds = self::query()
                ->whereIn('reporting_to_id', $pendingManagerIds)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            $newChildIds = array_values(array_diff($childIds, $reportIds));

            if (empty($newChildIds)) {
                break;
            }

            $reportIds = array_values(array_unique([...$reportIds, ...$newChildIds]));
            $pendingManagerIds = $newChildIds;
        }

        return $reportIds;
    }

    protected function guardReportingHierarchy(): void
    {
        if (! filled($this->reporting_to_id)) {
            return;
        }

        $managerId = (int) $this->reporting_to_id;

        if (! self::query()->whereKey($managerId)->exists()) {
            throw ValidationException::withMessages([
                'reporting_to_id' => __('errors.employee.invalid_manager'),
            ]);
        }

        if (! $this->exists) {
            return;
        }

        if ($managerId === (int) $this->getKey()) {
            throw ValidationException::withMessages([
                'reporting_to_id' => __('errors.employee.self_report'),
            ]);
        }

        if (in_array($managerId, $this->getReportChainIds(), true)) {
            throw ValidationException::withMessages([
                'reporting_to_id' => __('errors.employee.circular_report'),
            ]);
        }
    }
}
