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
                Section::make(__('labels.company.section_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('company_type')
                                    ->label(__('labels.company.company_type'))
                                    ->badge(),
                                TextEntry::make('status')
                                    ->label(__('labels.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => $state === Company::STATUS_ACTIVE ? 'success' : 'gray'),
                                TextEntry::make('legal_name')
                                    ->label(__('labels.company.legal_name')),
                                TextEntry::make('trade_name')
                                    ->label(__('labels.company.trade_name'))
                                    ->placeholder('-'),
                                TextEntry::make('display_name')
                                    ->label(__('labels.company.display_name'))
                                    ->placeholder('-'),
                                TextEntry::make('company_code')
                                    ->label(__('labels.company.company_code'))
                                    ->placeholder('-'),
                                TextEntry::make('parent.legal_name')
                                    ->label(__('labels.company.parent_company'))
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->label(__('labels.email'))
                                    ->placeholder('-'),
                                TextEntry::make('website')
                                    ->label(__('labels.website'))
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_registration_locale'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('taxCountry.name')
                                    ->label(__('labels.company.tax_country'))
                                    ->placeholder('-'),
                                TextEntry::make('timezone')
                                    ->label(__('labels.company.timezone'))
                                    ->placeholder('-'),
                                TextEntry::make('incorporation_date')
                                    ->label(__('labels.company.incorporation_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('financial_year_start')
                                    ->label(__('labels.company.financial_year_start'))
                                    ->date()
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_working_hours'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('start_work_hour')
                                    ->label(__('labels.company.start_work_hour'))
                                    ->time()
                                    ->placeholder('-'),
                                TextEntry::make('end_work_hour')
                                    ->label(__('labels.company.end_work_hour'))
                                    ->time()
                                    ->placeholder('-'),
                                TextEntry::make('weekends')
                                    ->label(__('labels.company.weekends'))
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_branding_notes'))
                    ->schema([
                        ImageEntry::make('logo')
                            ->label(__('labels.company.logo'))
                            ->circular()
                            ->visible(fn (Company $record): bool => filled($record->logo)),
                        TextEntry::make('notes')
                            ->label(__('labels.notes'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
