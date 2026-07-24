<?php

namespace App\Filament\Resources\Branches\Tables;

use App\Models\Branch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->with('parent')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('name')
                    ->state(function (Branch $record): string {
                        if ($record->parent_id === null) {
                            return e($record->name);
                        }

                        return '&nbsp;&nbsp;&nbsp;&nbsp;&#8627;&nbsp;' . e($record->name)
                            . ' <span class="text-xs font-medium text-gray-500">Branch</span>';
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('address.location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_work_hour')
                    ->label('Start'),
                TextColumn::make('end_work_hour')
                    ->label('End'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->disabled(fn (): bool => ! filament()->auth()->user()?->can('branches.add'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View'),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit')
                    ->visible(fn (): bool => (bool) filament()->auth()->user()?->can('branches.add')),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete')
                    ->visible(fn (): bool => (bool) filament()->auth()->user()?->can('branches')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}