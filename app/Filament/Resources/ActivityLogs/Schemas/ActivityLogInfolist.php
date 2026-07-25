<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Activitylog\Models\Activity;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.activity_log.section_activity'))
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('labels.activity_log.when'))
                            ->dateTime(),
                        TextEntry::make('log_name')
                            ->label(__('labels.activity_log.log'))
                            ->badge(),
                        TextEntry::make('event')
                            ->label(__('labels.activity_log.event'))
                            ->badge(),
                        TextEntry::make('description')
                            ->label(__('labels.activity_log.description')),
                        TextEntry::make('subject_type')
                            ->label(__('labels.activity_log.subject'))
                            ->formatStateUsing(fn (?string $state, Activity $record): string => $state
                                ? class_basename($state).' #'.$record->subject_id
                                : '-'),
                        TextEntry::make('causer.username')
                            ->label(__('labels.activity_log.by'))
                            ->default(__('labels.activity_log.system')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make(__('labels.activity_log.section_changes'))
                    ->schema([
                        TextEntry::make('properties')
                            ->label(__('labels.activity_log.recorded_properties'))
                            ->state(fn (Activity $record): string => json_encode(
                                $record->properties,
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                            ))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
