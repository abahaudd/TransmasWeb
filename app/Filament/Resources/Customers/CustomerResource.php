<?php

/**
 * Customer Resource
 *
 * Purpose:
 * - Registers Customer CRUD pages in Filament.
 * - Delegates form, infolist, and table definitions to dedicated schema/table classes.
 *
 * Revision History:
 * - 2026-06-20: Asif - Added documentation header and inline comments for maintainability.
 */

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Schemas\CustomerInfolist;
use App\Filament\Resources\Customers\Tables\CustomersTable;
use App\Models\Customer;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Filament resource definition for customers.
 */
class CustomerResource extends Resource
{
    /**
     * Backing Eloquent model for this resource.
     */
    protected static ?string $model = Customer::class;

    /**
     * Sidebar icon used for this resource.
     */
    protected static string|BackedEnum|null $navigationIcon = null;

    /**
     * Sort order within the navigation group.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Human-readable record title attribute.
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Sidebar group under which this resource appears.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.customer_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.customers');
    }

    /**
     * Record visibility scoping.
     *
     * Users with the customers.view_any permission (or the bare customers
     * module permission / super admins, via wildcard cascade) see every
     * customer. Everyone else only sees customers assigned to them through
     * their employee profile, plus customers not assigned to anyone.
     *
     * Note: customers.view_any is deliberately a sibling of customers.add —
     * NOT customers.add.view_any — because Spatie wildcard permissions make
     * customers.add imply everything nested beneath it.
     *
     * Applies to the list table and to record resolution on view/edit pages,
     * so out-of-scope customers 404 rather than render.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = filament()->auth()->user();

        if (! $user || $user->can('customers.view_any')) {
            return $query;
        }

        $employeeId = $user->profile_type === Employee::class ? (int) $user->profile_id : null;

        return $query->where(function (Builder $scoped) use ($employeeId): void {
            $scoped->whereDoesntHave('salesStaff');

            if ($employeeId !== null) {
                $scoped->orWhereHas('salesStaff', fn (Builder $staff) => $staff->whereKey($employeeId));
            }
        });
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
        return CustomerForm::configure($schema);
    }

    /**
     * Resource infolist schema (view).
     */
    public static function infolist(Schema $schema): Schema
    {
        return CustomerInfolist::configure($schema);
    }

    /**
     * Resource table schema (index listing).
     */
    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    /**
     * Relation managers rendered on the view/edit pages.
     */
    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    /**
     * Route map for index/create/view/edit pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'view' => ViewCustomer::route('/{record}'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
