<?php

/**
 * Branch Resource
 *
 * Purpose:
 * - Registers Branch CRUD pages in Filament.
 * - Delegates form, infolist, and table definitions to dedicated schema/table classes.
 *
 * Revision History:
 * - 2026-06-20: Asif - Added documentation header and inline comments for maintainability.
 */

namespace App\Filament\Resources\Branches;

use App\Filament\Resources\Branches\Pages\CreateBranch;
use App\Filament\Resources\Branches\Pages\EditBranch;
use App\Filament\Resources\Branches\Pages\ListBranches;
use App\Filament\Resources\Branches\Pages\ViewBranch;
use App\Filament\Resources\Branches\Schemas\BranchForm;
use App\Filament\Resources\Branches\Schemas\BranchInfolist;
use App\Filament\Resources\Branches\Tables\BranchesTable;
use App\Models\Branch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Filament resource definition for branches.
 */
class BranchResource extends Resource
{
    /**
     * Backing Eloquent model for this resource.
     */
    protected static ?string $model = Branch::class;

    /**
     * Sidebar icon used for this resource.
     */
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * Sort order within the navigation group.
     */
    protected static ?int $navigationSort = 2;

    /**
     * Human-readable record title attribute.
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Sidebar group under which this resource appears.
     */
    public static function getNavigationGroup(): ?string
    {
        return 'Control Panel';
    }

    public static function canViewAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('customer.add.view');
    }

    public static function canView(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('customer.add.view');
    }

    public static function canCreate(): bool
    {
        return (bool) filament()->auth()->user()?->can('customer.add');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('customer.add');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) filament()->auth()->user()?->can('customer');
    }

    public static function canDeleteAny(): bool
    {
        return (bool) filament()->auth()->user()?->can('customer');
    }

    /**
     * Resource form schema (create/edit).
     */
    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    /**
     * Resource infolist schema (view).
     */
    public static function infolist(Schema $schema): Schema
    {
        return BranchInfolist::configure($schema);
    }

    /**
     * Resource table schema (index listing).
     */
    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
    }

    /**
     * No relation managers are registered for this resource.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Route map for index/create/view/edit pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'view' => ViewBranch::route('/{record}'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}