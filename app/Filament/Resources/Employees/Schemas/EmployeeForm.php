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
                Section::make('Personal Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->maxLength(255),
                                Select::make('gender')
                                    ->options([
                                        1 => 'Male',
                                        2 => 'Female',
                                        3 => 'Other',
                                    ])
                                    ->native(false),
                                DatePicker::make('birth_date')
                                    ->native(false),
                                TextInput::make('nationality')
                                    ->maxLength(255),
                                TextInput::make('national_id')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel(),
                                TextInput::make('mobile')
                                    ->tel(),
                                TextInput::make('email')
                                    ->email(),
                            ]),
                    ]),

                Section::make('Employment Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('company_id')
                                    ->label('Company')
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
                                    ->label('Employee Code')
                                    ->disabled()
                                    ->visible(fn (string $operation): bool => $operation === 'edit')
                                    ->helperText('Auto-generated on save.'),
                                Select::make('department_id')
                                    ->label('Department')
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
                                    ->label('Designation')
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
                                    ->label('Employment Type')
                                    ->options(fn () => EmploymentType::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->native(false),
                                Select::make('employment_status_id')
                                    ->label('Employment Status')
                                    ->options(fn () => EmploymentStatus::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                                    ->default(fn () => EmploymentStatus::where('name', 'Active')->value('id'))
                                    ->searchable()
                                    ->native(false),
                                Select::make('reporting_to_id')
                                    ->label('Reports To')
                                    ->options(fn (?Employee $record): array => Employee::query()
                                        ->with('person')
                                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                        ->get()
                                        ->mapWithKeys(fn (Employee $employee): array => [$employee->id => $employee->fullName()])
                                        ->all())
                                    ->searchable()
                                    ->preload(),
                                Select::make('user_id')
                                    ->label('Linked User Account')
                                    ->options(fn () => User::query()->orderBy('username')->pluck('username', 'id')->all())
                                    ->searchable()
                                    ->preload(),
                                Toggle::make('is_manager')
                                    ->label('Is Manager')
                                    ->default(false),
                                DatePicker::make('joining_date')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),
                                DatePicker::make('confirmation_date')
                                    ->native(false),
                                DatePicker::make('end_date')
                                    ->native(false),
                                TextInput::make('termination_reason')
                                    ->maxLength(100)
                                    ->visible(fn (Get $get): bool => filled($get('end_date'))),
                            ]),
                        Textarea::make('remarks')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                Section::make('Addresses')
                    ->description('An employee may have more than one address on file, e.g. current and permanent.')
                    ->schema([
                        Repeater::make('addresses')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('address_type')
                                            ->label('Type')
                                            ->options(PersonAddress::typeOptions())
                                            ->default(PersonAddress::TYPE_CURRENT)
                                            ->required()
                                            ->native(false),
                                        Toggle::make('is_primary')
                                            ->label('Primary'),
                                        TextInput::make('address')
                                            ->label('Address')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        TextInput::make('location')
                                            ->label('City / Location')
                                            ->maxLength(255),
                                        TextInput::make('territory')
                                            ->label('State / Territory')
                                            ->maxLength(255),
                                        TextInput::make('postal_code')
                                            ->label('Postal Code')
                                            ->maxLength(50),
                                        Select::make('country_id')
                                            ->label('Country')
                                            ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('remarks')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['address_type'] ?? null)
                            ->addActionLabel('Add address')
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
