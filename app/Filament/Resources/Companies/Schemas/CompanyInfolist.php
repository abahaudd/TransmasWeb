<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('company_type')
                                    ->label('Type')
                                    ->badge(),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === Company::STATUS_ACTIVE ? 'success' : 'gray'),
                                TextEntry::make('legal_name'),
                                TextEntry::make('trade_name')
                                    ->placeholder('-'),
                                TextEntry::make('display_name')
                                    ->placeholder('-'),
                                TextEntry::make('company_code')
                                    ->label('Company Code')
                                    ->placeholder('-'),
                                TextEntry::make('parent.legal_name')
                                    ->label('Parent Company')
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->placeholder('-'),
                                TextEntry::make('website')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Registration & Locale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('taxCountry.name')
                                    ->label('Tax Country')
                                    ->placeholder('-'),
                                TextEntry::make('timezone')
                                    ->placeholder('-'),
                                TextEntry::make('incorporation_date')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('financial_year_start')
                                    ->date()
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Branding & Notes')
                    ->schema([
                        ImageEntry::make('logo')
                            ->circular()
                            ->visible(fn (Company $record): bool => filled($record->logo)),
                        TextEntry::make('notes')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
