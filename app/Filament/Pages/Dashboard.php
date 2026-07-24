<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
	protected static ?string $title = 'Dashboard';

	protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';

	protected string $view = 'filament.pages.dashboard';

	protected function getHeaderWidgets(): array
	{
		return [
			\App\Filament\Widgets\Stats\DashboardStats::class,
			\App\Modules\Dashboard\Filament\Widgets\RevenueChart::class,
		];
	}
}
