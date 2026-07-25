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
                Section::make(__('labels.company.section_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('company_type')
                                    ->label(__('labels.company.company_type'))
                                    ->options(Company::typeOptions())
                                    ->default(Company::TYPE_COMPANY)
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-building-office-2'),
                                TextInput::make('company_code')
                                    ->label(__('labels.company.company_code'))
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-hashtag'),
                                Select::make('parent_id')
                                    ->label(__('labels.company.parent_company'))
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
                                    ->label(__('labels.status'))
                                    ->options(Company::statusOptions())
                                    ->default(Company::STATUS_ACTIVE)
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-check-circle'),
                                TextInput::make('legal_name')
                                    ->label(__('labels.company.legal_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull()
                                    ->prefixIcon('heroicon-o-identification'),
                                TextInput::make('trade_name')
                                    ->label(__('labels.company.trade_name'))
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-tag'),
                                TextInput::make('display_name')
                                    ->label(__('labels.company.display_name'))
                                    ->maxLength(200)
                                    ->helperText(__('labels.company.display_name_helper'))
                                    ->prefixIcon('heroicon-o-sparkles'),
                                TextInput::make('email')
                                    ->label(__('labels.email'))
                                    ->email()
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-envelope'),
                                TextInput::make('website')
                                    ->label(__('labels.website'))
                                    ->url()
                                    ->maxLength(200)
                                    ->prefixIcon('heroicon-o-globe-alt'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_registration_locale'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('tax_country_id')
                                    ->label(__('labels.company.tax_country'))
                                    ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('help.select_option'))
                                    ->prefixIcon('heroicon-o-flag'),
                                Select::make('timezone')
                                    ->label(__('labels.company.timezone'))
                                    ->options(fn () => collect(\DateTimeZone::listIdentifiers())
                                        ->mapWithKeys(fn (string $timezone): array => [$timezone => $timezone])
                                        ->all())
                                    ->searchable()
                                    ->prefixIcon('heroicon-o-clock'),
                                DatePicker::make('incorporation_date')
                                    ->label(__('labels.company.incorporation_date'))
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-calendar'),
                                DatePicker::make('financial_year_start')
                                    ->label(__('labels.company.financial_year_start'))
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-calendar-days'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_working_hours'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TimePicker::make('start_work_hour')
                                    ->label(__('labels.company.start_work_hour'))
                                    ->seconds(false)
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TimePicker::make('end_work_hour')
                                    ->label(__('labels.company.end_work_hour'))
                                    ->seconds(false)
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TextInput::make('weekends')
                                    ->label(__('labels.company.weekends'))
                                    ->maxLength(100)
                                    ->placeholder('e.g. Saturday, Sunday')
                                    ->prefixIcon('heroicon-o-calendar-days'),
                            ]),
                    ]),

                Section::make(__('labels.company.section_branding_notes'))
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('labels.company.logo'))
                            ->image()
                            ->directory('companies/logos')
                            ->avatar(),
                        Textarea::make('notes')
                            ->label(__('labels.notes'))
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
