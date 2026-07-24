<?php

namespace App\Filament\Widgets\Stats;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Properties', '125')
                ->description('8 added this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Tenants', '1,248')
                ->description('97% occupied')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Outstanding Rent', 'AED 1.28M')
                ->description('18 overdue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Maintenance', '32')
                ->description('7 urgent')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),

        ];
    }
}
