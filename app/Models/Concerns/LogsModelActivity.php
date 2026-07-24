<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Default activity-logging behaviour for foundation and module models.
 *
 * Apply this trait to any model that should be audited. Override
 * getActivitylogOptions() on the model to customise what is logged
 * (e.g. to exclude sensitive attributes).
 */
trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName(class_basename(static::class));
    }
}
