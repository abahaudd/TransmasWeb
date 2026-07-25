<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\EmploymentType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'person', 'company', 'department', 'designation', 'employmentType', 'employmentStatus', 'reportingTo.person',
            ]))
            ->columns([
                TextColumn::make('employee_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('person.full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('designation.title')
                    ->label('Designation')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('employmentType.name')
                    ->label('Type')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('employmentStatus.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?Employee $record): string => $record?->employmentStatus?->is_terminal ? 'danger' : 'success')
                    ->placeholder('-'),
                IconColumn::make('is_manager')
                    ->label('Manager')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reportingTo.person.full_name')
                    ->label('Reports To')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('joining_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->options(fn () => Company::query()
                        ->orderBy('legal_name')
                        ->get()
                        ->mapWithKeys(fn (Company $company): array => [$company->id => $company->displayLabel()])
                        ->all())
                    ->searchable(),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(fn () => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('employment_type_id')
                    ->label('Employment Type')
                    ->options(fn () => EmploymentType::query()->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('employment_status_id')
                    ->label('Employment Status')
                    ->options(fn () => EmploymentStatus::query()->orderBy('name')->pluck('name', 'id')->all()),
                TernaryFilter::make('is_manager')
                    ->label('Is Manager'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View'),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('employee_code');
    }
}
