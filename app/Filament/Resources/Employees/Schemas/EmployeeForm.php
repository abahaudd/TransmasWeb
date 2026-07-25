<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Company;
use App\Models\Country;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\EmploymentType;
use App\Models\PersonAddress;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.employee.section_personal_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label(__('labels.employee.first_name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->label(__('labels.employee.last_name'))
                                    ->maxLength(255),
                                Select::make('gender')
                                    ->label(__('labels.employee.gender'))
                                    ->options([
                                        1 => __('labels.employee.gender_male'),
                                        2 => __('labels.employee.gender_female'),
                                        3 => __('labels.employee.gender_other'),
                                    ])
                                    ->native(false),
                                DatePicker::make('birth_date')
                                    ->label(__('labels.employee.birth_date'))
                                    ->native(false),
                                TextInput::make('nationality')
                                    ->label(__('labels.employee.nationality'))
                                    ->maxLength(255),
                                TextInput::make('national_id')
                                    ->label(__('labels.employee.national_id'))
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('labels.phone'))
                                    ->tel(),
                                TextInput::make('mobile')
                                    ->label(__('labels.mobile'))
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(__('labels.email'))
                                    ->email(),
                            ]),
                    ]),

                Section::make(__('labels.employee.section_employment_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('labels.employee.company'))
                                    ->options(fn () => Company::query()
                                        ->orderBy('legal_name')
                                        ->get()
                                        ->mapWithKeys(fn (Company $company): array => [$company->id => $company->displayLabel()])
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),
                                TextInput::make('employee_code')
                                    ->label(__('labels.employee.employee_code'))
                                    ->disabled()
                                    ->visible(fn (string $operation): bool => $operation === 'edit')
                                    ->helperText(__('labels.employee.employee_code_helper')),
                                Select::make('department_id')
                                    ->label(__('labels.employee.department'))
                                    ->options(fn (Get $get): array => Department::query()
                                        ->when(
                                            filled($get('company_id')),
                                            fn ($query) => $query->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $get('company_id')))
                                        )
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Select::make('designation_id')
                                    ->label(__('labels.employee.designation'))
                                    ->options(fn (Get $get): array => Designation::query()
                                        ->when(
                                            filled($get('department_id')),
                                            fn ($query) => $query->where('department_id', $get('department_id'))
                                        )
                                        ->orderBy('title')
                                        ->pluck('title', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload(),
                                Select::make('employment_type_id')
                                    ->label(__('labels.employee.employment_type'))
                                    ->options(fn () => EmploymentType::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->native(false),
                                Select::make('employment_status_id')
                                    ->label(__('labels.employee.employment_status'))
                                    ->options(fn () => EmploymentStatus::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                    ->default(fn () => EmploymentStatus::where('name', 'Active')->value('id'))
                                    ->searchable()
                                    ->native(false),
                                Select::make('reporting_to_id')
                                    ->label(__('labels.employee.reports_to'))
                                    ->options(fn (?Employee $record): array => Employee::query()
                                        ->with('person')
                                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                        ->get()
                                        ->mapWithKeys(fn (Employee $employee): array => [$employee->id => $employee->fullName()])
                                        ->all())
                                    ->searchable()
                                    ->preload(),
                                Select::make('user_id')
                                    ->label(__('labels.employee.linked_user_account'))
                                    ->options(fn () => User::query()->orderBy('username')->pluck('username', 'id')->all())
                                    ->searchable()
                                    ->preload(),
                                Toggle::make('is_manager')
                                    ->label(__('labels.employee.is_manager'))
                                    ->default(false),
                                DatePicker::make('joining_date')
                                    ->label(__('labels.employee.joining_date'))
                                    ->required()
                                    ->native(false)
                                    ->default(now()),
                                DatePicker::make('confirmation_date')
                                    ->label(__('labels.employee.confirmation_date'))
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->label(__('labels.end_date'))
                                    ->native(false),
                                TextInput::make('termination_reason')
                                    ->label(__('labels.employee.termination_reason'))
                                    ->maxLength(100)
                                    ->visible(fn (Get $get): bool => filled($get('end_date'))),
                            ]),
                        Textarea::make('remarks')
                            ->label(__('labels.remarks'))
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make(__('labels.employee.section_addresses'))
                    ->description(__('labels.employee.addresses_description'))
                    ->schema([
                        Repeater::make('addresses')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('address_type')
                                            ->label(__('labels.type'))
                                            ->options(PersonAddress::typeOptions())
                                            ->default(PersonAddress::TYPE_CURRENT)
                                            ->required()
                                            ->native(false),
                                        Toggle::make('is_primary')
                                            ->label(__('labels.primary')),
                                        TextInput::make('address')
                                            ->label(__('labels.address'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        TextInput::make('location')
                                            ->label(__('labels.employee.city_location'))
                                            ->maxLength(255),
                                        TextInput::make('territory')
                                            ->label(__('labels.employee.state_territory'))
                                            ->maxLength(255),
                                        TextInput::make('postal_code')
                                            ->label(__('labels.employee.postal_code'))
                                            ->maxLength(50),
                                        Select::make('country_id')
                                            ->label(__('labels.country'))
                                            ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('remarks')
                                            ->label(__('labels.remarks'))
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['address_type'] ?? null)
                            ->addActionLabel(__('labels.employee.add_address'))
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
