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
            ->heading('Recent Activity')
            ->query(Activity::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->since(),
                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge(),
                TextColumn::make('description'),
                TextColumn::make('causer.username')
                    ->label('By')
                    ->default('System'),
            ])
            ->paginated(false);
    }
}
