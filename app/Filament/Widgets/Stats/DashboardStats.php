<?php

namespace App\Filament\Widgets\Stats;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(__('labels.widgets.properties'), '125')
                ->description(__('labels.widgets.properties_description', ['count' => 8]))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('labels.widgets.tenants'), '1,248')
                ->description(__('labels.widgets.tenants_description', ['percent' => 97]))
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make(__('labels.widgets.outstanding_rent'), 'AED 1.28M')
                ->description(__('labels.widgets.outstanding_rent_description', ['count' => 18]))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make(__('labels.widgets.maintenance'), '32')
                ->description(__('labels.widgets.maintenance_description', ['count' => 7]))
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),

        ];
    }
}
