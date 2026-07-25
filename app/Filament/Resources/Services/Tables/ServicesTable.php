<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('category')->withCount('workflowSteps'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('labels.service.category'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('code')
                    ->label(__('labels.code'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label(__('labels.service.cost'))
                    ->formatStateUsing(fn (string $state): string => format_money($state))
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('labels.service.price'))
                    ->formatStateUsing(fn (string $state): string => format_money($state))
                    ->sortable(),
                TextColumn::make('workflow_steps_count')
                    ->label(__('labels.service.section_workflow'))
                    ->counts('workflowSteps'),
                TextColumn::make('status')
                    ->label(__('labels.status'))
                    ->badge()
                    ->color(fn (string $state): string => $state === Service::STATUS_ACTIVE ? 'success' : 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_category_id')
                    ->label(__('labels.service.category'))
                    ->options(fn () => ServiceCategory::query()->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('status')
                    ->label(__('labels.status'))
                    ->options(Service::statusOptions()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
