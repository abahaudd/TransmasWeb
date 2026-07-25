<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use App\Models\Country;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('company_type')
                                    ->label('Type')
                                    ->options(Company::typeOptions())
                                    ->default(Company::TYPE_COMPANY)
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-building-office-2'),
                                TextInput::make('company_code')
                                    ->label('Company Code')
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-hashtag'),
                                Select::make('parent_id')
                                    ->label('Parent Company')
                                    ->options(fn (?Company $record) => Company::query()
                                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                        ->orderBy('legal_name')
                                        ->get()
                                        ->mapWithKeys(fn (Company $company): array => [$company->id => $company->displayLabel()])
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('help.select_option'))
                                    ->prefixIcon('heroicon-o-building-office'),
                                Select::make('status')
                                    ->options(Company::statusOptions())
                                    ->default(Company::STATUS_ACTIVE)
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-check-circle'),
                                TextInput::make('legal_name')
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-o-identification'),
                                TextInput::make('trade_name')
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-tag'),
                                TextInput::make('display_name')
                                    ->maxLength(200)
                                    ->helperText('Shown throughout the app when set; falls back to trade or legal name.')
                                    ->prefixIcon('heroicon-o-sparkles'),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-envelope'),
                                TextInput::make('website')
                                    ->url()
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-globe-alt'),
                            ]),
                    ]),

                Section::make('Registration & Locale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('tax_country_id')
                                    ->label('Tax Country')
                                    ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('help.select_option'))
                                    ->prefixIcon('heroicon-o-flag'),
                                Select::make('timezone')
                                    ->options(fn () => collect(\DateTimeZone::listIdentifiers())
                                        ->mapWithKeys(fn (string $timezone): array => [$timezone => $timezone])
                                        ->all())
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-clock'),
                                DatePicker::make('incorporation_date')
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-calendar'),
                                DatePicker::make('financial_year_start')
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-calendar-days'),
                            ]),
                    ]),

                Section::make('Working Hours')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TimePicker::make('start_work_hour')
                                    ->seconds(false)
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TimePicker::make('end_work_hour')
                                    ->seconds(false)
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TextInput::make('weekends')
                                    ->maxLength(100)
                                    ->placeholder('e.g. Saturday, Sunday')
                                    ->prefixIcon('heroicon-o-calendar-days'),
                            ]),
                    ]),

                Section::make('Branding & Notes')
                    ->schema([
                        FileUpload::make('logo')
                            ->image()
                            ->directory('companies/logos')
                            ->avatar(),
                        Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
