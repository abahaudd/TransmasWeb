<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Branch Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->columnSpan(2),
                                TextEntry::make('parent.name')
                                    ->label('Parent branch')
                                    ->columnSpan(2)
                                    ->placeholder('Main branch'),
                                TextEntry::make('phone'),
                                TextEntry::make('email')
                                    ->placeholder('-'),
                                ViewEntry::make('is_active')
                                    ->label('Status')
                                    ->view('filament.infolists.components.active-toggle')
                                    ->viewData([
                                        'isActive' => fn ($record): bool => (bool) $record?->is_active,
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make('Working Hours')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('start_work_hour')
                                    ->label('Start work hour')
                                    ->placeholder('-'),
                                TextEntry::make('end_work_hour')
                                    ->label('End work hour')
                                    ->placeholder('-'),
                                TextEntry::make('weekends')
                                    ->label('Weekend')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Address')
                    ->schema([
                        TextEntry::make('address.full_address_with_labels')
                            ->label('Full address')
                            ->placeholder('-')
                            ->formatStateUsing(fn (?string $state): string => nl2br(e($state ?? '')))
                            ->html(),
                    ]),
            ]);
    }
}