<?php

/**
 * Employee Resource
 *
 * Purpose:
 * - Single, common CRUD page for all staff (managers, office staff, sales
 *   staff, ...). Replaces the previous role-filtered
 *   ManagersResource/OfficeStaffResource/SalesStaffResource trio — staff
 *   type is now expressed via department/designation/is_manager rather than
 *   three separate resources.
 */

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Pages\ViewEmployee;
use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Schemas\EmployeeInfolist;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'employee_code';

    public static function getNavigationGroup(): ?string
    {
        return 'HR Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Employees';
    }

    public static function canViewAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add.view');
    }

    public static function canView(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add.view');
    }

    public static function canCreate(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff');
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'view' => ViewEmployee::route('/{record}'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
