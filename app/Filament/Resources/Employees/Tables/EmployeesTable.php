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
                    ->label(__('labels.employee.employee_code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('person.full_name')
                    ->label(__('labels.name'))
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('company.legal_name')
                    ->label(__('labels.employee.company'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('department.name')
                    ->label(__('labels.employee.department'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('designation.title')
                    ->label(__('labels.employee.designation'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('employmentType.name')
                    ->label(__('labels.employee.employment_type'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('employmentStatus.name')
                    ->label(__('labels.employee.employment_status'))
                    ->badge()
                    ->color(fn (?Employee $record): string => $record?->employmentStatus?->is_terminal ? 'danger' : 'success')
                    ->placeholder('-'),
                IconColumn::make('is_manager')
                    ->label(__('labels.employee.is_manager'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reportingTo.person.full_name')
                    ->label(__('labels.employee.reports_to'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('joining_date')
                    ->label(__('labels.employee.joining_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('labels.employee.company'))
                    ->options(fn () => Company::query()
                        ->orderBy('legal_name')
                        ->get()
                        ->mapWithKeys(fn (Company $company): array => [$company->id => $company->displayLabel()])
                        ->all())
                    ->searchable(),
                SelectFilter::make('department_id')
                    ->label(__('labels.employee.department'))
                    ->options(fn () => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('employment_type_id')
                    ->label(__('labels.employee.employment_type'))
                    ->options(fn () => EmploymentType::query()->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('employment_status_id')
                    ->label(__('labels.employee.employment_status'))
                    ->options(fn () => EmploymentStatus::query()->orderBy('name')->pluck('name', 'id')->all()),
                TernaryFilter::make('is_manager')
                    ->label(__('labels.employee.is_manager')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('employee_code');
    }
}
