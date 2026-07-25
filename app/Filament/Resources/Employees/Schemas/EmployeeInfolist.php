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
                Section::make('Personal Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('person.full_name')
                                    ->label('Name'),
                                TextEntry::make('person.email')
                                    ->label('Email')
                                    ->placeholder('-'),
                                TextEntry::make('person.phone')
                                    ->label('Phone')
                                    ->placeholder('-'),
                                TextEntry::make('person.mobile')
                                    ->label('Mobile')
                                    ->placeholder('-'),
                                TextEntry::make('person.nationality')
                                    ->placeholder('-'),
                                TextEntry::make('person.national_id')
                                    ->label('National ID')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Employment Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('company.legal_name')
                                    ->label('Company'),
                                TextEntry::make('employee_code')
                                    ->label('Employee Code'),
                                TextEntry::make('department.name')
                                    ->label('Department')
                                    ->placeholder('-'),
                                TextEntry::make('designation.title')
                                    ->label('Designation')
                                    ->placeholder('-'),
                                TextEntry::make('employmentType.name')
                                    ->label('Employment Type')
                                    ->badge()
                                    ->placeholder('-'),
                                TextEntry::make('employmentStatus.name')
                                    ->label('Employment Status')
                                    ->badge()
                                    ->placeholder('-'),
                                TextEntry::make('reportingTo.person.full_name')
                                    ->label('Reports To')
                                    ->placeholder('-'),
                                IconEntry::make('is_manager')
                                    ->label('Is Manager')
                                    ->boolean(),
                                TextEntry::make('joining_date')
                                    ->date(),
                                TextEntry::make('confirmation_date')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('end_date')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('termination_reason')
                                    ->placeholder('-'),
                            ]),
                        TextEntry::make('remarks')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Addresses')
                    ->schema([
                        RepeatableEntry::make('person.addresses')
                            ->label('')
                            ->schema([
                                TextEntry::make('address_type')
                                    ->label('Type')
                                    ->badge(),
                                TextEntry::make('address'),
                                TextEntry::make('location')
                                    ->placeholder('-'),
                                TextEntry::make('territory')
                                    ->placeholder('-'),
                                TextEntry::make('postal_code')
                                    ->placeholder('-'),
                                TextEntry::make('country.name')
                                    ->label('Country')
                                    ->placeholder('-'),
                                IconEntry::make('is_primary')
                                    ->label('Primary')
                                    ->boolean(),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn (Employee $record): bool => $record->person?->addresses->isNotEmpty()),
            ]);
    }
}
