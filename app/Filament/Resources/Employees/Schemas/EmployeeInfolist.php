<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Employee;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.employee.section_personal_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('person.full_name')
                                    ->label(__('labels.name')),
                                TextEntry::make('person.email')
                                    ->label(__('labels.email'))
                                    ->placeholder('-'),
                                TextEntry::make('person.phone')
                                    ->label(__('labels.phone'))
                                    ->placeholder('-'),
                                TextEntry::make('person.mobile')
                                    ->label(__('labels.mobile'))
                                    ->placeholder('-'),
                                TextEntry::make('person.nationality')
                                    ->label(__('labels.employee.nationality'))
                                    ->placeholder('-'),
                                TextEntry::make('person.national_id')
                                    ->label(__('labels.employee.national_id'))
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('labels.employee.section_employment_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('company.legal_name')
                                    ->label(__('labels.employee.company')),
                                TextEntry::make('employee_code')
                                    ->label(__('labels.employee.employee_code')),
                                TextEntry::make('department.name')
                                    ->label(__('labels.employee.department'))
                                    ->placeholder('-'),
                                TextEntry::make('designation.title')
                                    ->label(__('labels.employee.designation'))
                                    ->placeholder('-'),
                                TextEntry::make('employmentType.name')
                                    ->label(__('labels.employee.employment_type'))
                                    ->badge()
                                    ->placeholder('-'),
                                TextEntry::make('employmentStatus.name')
                                    ->label(__('labels.employee.employment_status'))
                                    ->badge()
                                    ->placeholder('-'),
                                TextEntry::make('reportingTo.person.full_name')
                                    ->label(__('labels.employee.reports_to'))
                                    ->placeholder('-'),
                                IconEntry::make('is_manager')
                                    ->label(__('labels.employee.is_manager'))
                                    ->boolean(),
                                TextEntry::make('joining_date')
                                    ->label(__('labels.employee.joining_date'))
                                    ->date(),
                                TextEntry::make('confirmation_date')
                                    ->label(__('labels.employee.confirmation_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('end_date')
                                    ->label(__('labels.end_date'))
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('termination_reason')
                                    ->label(__('labels.employee.termination_reason'))
                                    ->placeholder('-'),
                            ]),
                        TextEntry::make('remarks')
                            ->label(__('labels.remarks'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('labels.employee.section_addresses'))
                    ->schema([
                        RepeatableEntry::make('person.addresses')
                            ->label('')
                            ->schema([
                                TextEntry::make('address_type')
                                    ->label(__('labels.type'))
                                    ->badge(),
                                TextEntry::make('address')
                                    ->label(__('labels.address')),
                                TextEntry::make('location')
                                    ->label(__('labels.employee.city_location'))
                                    ->placeholder('-'),
                                TextEntry::make('territory')
                                    ->label(__('labels.employee.state_territory'))
                                    ->placeholder('-'),
                                TextEntry::make('postal_code')
                                    ->label(__('labels.employee.postal_code'))
                                    ->placeholder('-'),
                                TextEntry::make('country.name')
                                    ->label(__('labels.country'))
                                    ->placeholder('-'),
                                IconEntry::make('is_primary')
                                    ->label(__('labels.primary'))
                                    ->boolean(),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn (Employee $record): bool => $record->person?->addresses->isNotEmpty()),
            ]);
    }
}
