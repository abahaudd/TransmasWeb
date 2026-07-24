<?php

namespace App\Filament\Resources\Staff;

use App\Filament\Resources\Staff\OfficeStaffResource\Pages\CreateOfficeStaff;
use App\Filament\Resources\Staff\OfficeStaffResource\Pages\ListOfficeStaff;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OfficeStaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'Staff Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Staff';
    }

    public static function canViewAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add.view');
    }

    public static function canView($record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add.view');
    }

    public static function canCreate(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add');
    }

    public static function canEdit($record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff.add');
    }

    public static function canDelete($record): bool
    {
        return (bool) filament()->auth()->user()?->can('staff');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('staff');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('roles', fn (Builder $query): Builder => $query->where('name', 'office_staff'));
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table, showEmailColumn: false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfficeStaff::route('/'),
            'create' => CreateOfficeStaff::route('/create'),
        ];
    }
}
