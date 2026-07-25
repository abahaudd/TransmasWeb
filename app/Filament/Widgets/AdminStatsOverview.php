<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Part of the role-based dashboard: only administrators see these stats
    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('labels.widgets.users'), User::count()),
            Stat::make(__('labels.widgets.roles'), Role::count()),
            Stat::make(__('labels.widgets.activity_24h'), Activity::where('created_at', '>=', now()->subDay())->count()),
        ];
    }
}
