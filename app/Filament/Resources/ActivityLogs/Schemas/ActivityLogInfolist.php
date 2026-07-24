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
                Section::make('Activity')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('When')
                            ->dateTime(),
                        TextEntry::make('log_name')
                            ->label('Log')
                            ->badge(),
                        TextEntry::make('event')
                            ->badge(),
                        TextEntry::make('description'),
                        TextEntry::make('subject_type')
                            ->label('Subject')
                            ->formatStateUsing(fn (?string $state, Activity $record): string => $state
                                ? class_basename($state).' #'.$record->subject_id
                                : '-'),
                        TextEntry::make('causer.username')
                            ->label('By')
                            ->default('System'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Changes')
                    ->schema([
                        TextEntry::make('properties')
                            ->label('Recorded properties')
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
