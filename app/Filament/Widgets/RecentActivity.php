<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivity extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    // Part of the role-based dashboard: only administrators see the audit feed
    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('labels.widgets.recent_activity'))
            ->query(Activity::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('labels.widgets.when'))
                    ->since(),
                TextColumn::make('log_name')
                    ->label(__('labels.widgets.log'))
                    ->badge(),
                TextColumn::make('description')
                    ->label(__('labels.activity_log.description')),
                TextColumn::make('causer.username')
                    ->label(__('labels.widgets.by'))
                    ->default(__('labels.activity_log.system')),
            ])
            ->paginated(false);
    }
}
