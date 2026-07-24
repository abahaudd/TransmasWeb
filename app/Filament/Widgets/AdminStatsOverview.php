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
            Stat::make('Users', User::count()),
            Stat::make('Roles', Role::count()),
            Stat::make('Activity (24h)', Activity::where('created_at', '>=', now()->subDay())->count()),
        ];
    }
}
