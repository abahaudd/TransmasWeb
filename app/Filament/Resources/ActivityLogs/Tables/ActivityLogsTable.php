<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('labels.activity_log.when'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label(__('labels.activity_log.log'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('labels.activity_log.event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label(__('labels.activity_log.description'))
                    ->searchable()
                    ->limit(60),
                TextColumn::make('subject_type')
                    ->label(__('labels.activity_log.subject'))
                    ->formatStateUsing(fn (?string $state, Activity $record): string => $state
                        ? class_basename($state).' #'.$record->subject_id
                        : '-'),
                TextColumn::make('causer.username')
                    ->label(__('labels.activity_log.by'))
                    ->default(__('labels.activity_log.system')),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('labels.activity_log.log'))
                    ->options(fn (): array => Activity::query()
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->all()),
                SelectFilter::make('event')
                    ->label(__('labels.activity_log.event'))
                    ->options([
                        'created' => __('labels.activity_log.events.created'),
                        'updated' => __('labels.activity_log.events.updated'),
                        'deleted' => __('labels.activity_log.events.deleted'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->tooltip(__('labels.view')),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
