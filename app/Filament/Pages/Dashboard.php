<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
	protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';

	protected string $view = 'filament.pages.dashboard';

	public function getTitle(): string
	{
		return __('labels.dashboard.title');
	}

	public static function getNavigationLabel(): string
	{
		return __('labels.dashboard.title');
	}

	protected function getHeaderWidgets(): array
	{
		return [
			\App\Filament\Widgets\Stats\DashboardStats::class,
			\App\Modules\Dashboard\Filament\Widgets\RevenueChart::class,
		];
	}
}
