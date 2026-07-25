<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->with(['parent', 'salesStaff.person'])
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                    ->orderBy('name')
            )
            ->description(__('labels.customer.select_customers_for_assignment'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->state(function (Customer $record): string {
                        if ($record->parent_id === null) {
                            return e($record->name);
                        }

                        return '&nbsp;&nbsp;&nbsp;&nbsp;&#8627;&nbsp;' . e($record->name)
                            . ' <span class="text-xs font-medium text-gray-500">' . e(__('labels.customer.branch_badge')) . '</span>';
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_main')
                    ->label(__('labels.phone'))
                    ->searchable(),
                TextColumn::make('sales_staff')
                    ->label(__('labels.customer.sales_staff'))
                    ->state(fn (Customer $record): string => $record->salesStaff
                        ->map(fn (Employee $employee): string => trim(($employee->person?->first_name ?? '') . ' ' . ($employee->person?->last_name ?? '')))
                        ->filter()
                        ->join(', ') ?: '-'),
                TextColumn::make('created_at')
                    ->label(__('labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sales_staff_id')
                    ->label(__('labels.customer.sales_staff'))
                    ->options(fn (): array => Employee::query()
                        ->with('person')
                        ->whereHas('user.roles', fn (Builder $query): Builder => $query->where('name', 'sales_staff'))
                        ->get()
                        ->mapWithKeys(fn (Employee $employee): array => [
                            $employee->id => trim(($employee->person?->first_name ?? '') . ' ' . ($employee->person?->last_name ?? '')) ?: __('labels.customer.sales_staff_fallback', ['id' => $employee->id]),
                        ])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            filled($data['value'] ?? null),
                            fn (Builder $query): Builder => $query->whereHas(
                                'salesStaff',
                                fn (Builder $query): Builder => $query->whereKey((int) $data['value'])
                            )
                        ))
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.edit')),
                Action::make('createAccount')
                    ->icon('heroicon-o-user-plus')
                    ->iconButton()
                    ->tooltip(__('labels.customer.create_account'))
                    ->fillForm(function (Customer $record): array {
                        $existing = User::where('profile_type', Customer::class)
                            ->where('profile_id', $record->id)
                            ->first();

                        if ($existing) {
                            return [
                                'has_account'      => true,
                                'existing_user_id' => $existing->id,
                                'username'         => $existing->username,
                                'start_date'       => $existing->start_date?->toDateString(),
                                'end_date'         => $existing->end_date?->toDateString(),
                                'is_active'        => $existing->is_active,
                            ];
                        }

                        return [
                            'has_account'      => false,
                            'existing_user_id' => null,
                            'username'         => User::generateUsername($record->name),
                            'start_date'       => now(),
                            'end_date'         => now()->addDays(
                                (int) Setting::getPayloadValue('customer', 'user_id', 'validity', 365)
                            ),
                            'is_active'         => true,
                        ];
                    })
                    ->schema([
                        Placeholder::make('account_notice')
                            ->label('')
                            ->content(__('labels.account_user.has_account_notice'))
                            ->visible(fn (Get $get): bool => (bool) $get('has_account')),
                        Hidden::make('has_account'),
                        Hidden::make('existing_user_id'),
                        TextInput::make('username')
                            ->label(__('labels.account_user.user_id'))
                            ->required()
                            ->unique(
                                table: User::class,
                                column: 'username',
                                ignorable: fn (Get $get): ?User => User::find($get('existing_user_id')),
                            )
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label(__('labels.password'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => ! (bool) $get('has_account'))
                            ->helperText(fn (Get $get): ?string => $get('has_account') ? __('labels.account_user.leave_blank_password') : null)
                            ->maxLength(255),
                        DatePicker::make('start_date')
                            ->label(__('labels.start_date'))
                            ->required()
                            ->default(now()),
                        DatePicker::make('end_date')
                            ->label(__('labels.end_date'))
                            ->required()
                            ->default(fn () => now()->addDays(
                                (int) Setting::getPayloadValue('customer', 'user_id', 'validity', 365)
                            )),
                        Toggle::make('is_active')
                            ->label(__('labels.account_user.account_active'))
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->default(true)
                            ->visible(fn (Get $get): bool => (bool) $get('has_account')),
                    ])
                    ->action(function (Customer $record, array $data): void {
                        $existing = User::where('profile_type', Customer::class)
                            ->where('profile_id', $record->id)
                            ->first();

                        $payload = [
                            'name'       => $record->name,
                            'username'   => $data['username'],
                            'email'      => $record->email ?? null,
                            'start_date' => $data['start_date'],
                            'end_date'   => $data['end_date'],
                            'is_active'  => $data['is_active'] ?? true,
                        ];

                        if (filled($data['password'] ?? null)) {
                            $payload['password'] = Hash::make($data['password']);
                        }

                        if ($existing) {
                            $existing->update($payload);

                            Notification::make()
                                ->title(__('messages.account_updated'))
                                ->success()
                                ->send();

                            return;
                        }

                        $payload['password']     = Hash::make($data['password']);
                        $payload['profile_id']   = $record->id;
                        $payload['profile_type'] = Customer::class;

                        $user = User::create($payload);
                        $user->assignRole('customer');

                        Notification::make()
                            ->title(__('messages.account_created'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('assignSalesStaff')
                    ->label(__('labels.customer.assign_sales_staff'))
                    ->icon('heroicon-o-user-group')
                    ->modalHeading(__('labels.customer.assign_sales_staff'))
                    ->modalSubmitActionLabel(__('labels.customer.save_assignment'))
                    ->deselectRecordsAfterCompletion()
                    ->schema(function (BulkAction $action): array {
                            $selectedCustomerIds = $action->getSelectedRecords()
                                ->pluck('id')
                                ->map(fn ($id): int => (int) $id)
                                ->values()
                                ->all();

                            $customerOptions = function (?int $employeeId) use ($selectedCustomerIds): array {
                                $customerIds = collect($selectedCustomerIds);

                                if ($employeeId) {
                                    $customerIds = $customerIds->merge(
                                        Employee::query()
                                            ->find($employeeId)
                                            ?->customers()
                                            ->pluck('customers.id')
                                            ->all() ?? []
                                    );
                                }

                                return Customer::query()
                                    ->whereIn('id', $customerIds->unique()->values()->all())
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            };

                            $assignedCustomerIds = function (?int $employeeId) use ($selectedCustomerIds): array {
                                $customerIds = collect($selectedCustomerIds);

                                if ($employeeId) {
                                    $customerIds = $customerIds->merge(
                                        Employee::query()
                                            ->find($employeeId)
                                            ?->customers()
                                            ->pluck('customers.id')
                                            ->all() ?? []
                                    );
                                }

                                return $customerIds
                                    ->map(fn ($id): int => (int) $id)
                                    ->unique()
                                    ->values()
                                    ->all();
                            };

                            return [
                                Select::make('employee_id')
                                    ->label(__('labels.customer.sales_staff'))
                                    ->required()
                                    ->options(fn (): array => Employee::query()
                                        ->with('person')
                                        ->whereHas('user.roles', fn (Builder $query): Builder => $query->where('name', 'sales_staff'))
                                        ->get()
                                        ->mapWithKeys(fn (Employee $employee): array => [
                                            $employee->id => trim(($employee->person?->first_name ?? '') . ' ' . ($employee->person?->last_name ?? '')) ?: __('labels.customer.sales_staff_fallback', ['id' => $employee->id]),
                                        ])
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) use ($assignedCustomerIds): void {
                                        $set('customer_ids', $assignedCustomerIds(filled($state) ? (int) $state : null));
                                    })
                                    ->native(false),
                                CheckboxList::make('customer_ids')
                                    ->label(__('labels.customer.assigned_customers'))
                                    ->helperText(__('labels.customer.uncheck_to_remove'))
                                    ->options(fn (Get $get): array => $customerOptions(filled($get('employee_id')) ? (int) $get('employee_id') : null))
                                    ->default($selectedCustomerIds)
                                    ->columns(1)
                                    ->bulkToggleable(),
                            ];
                    })
                    ->action(function (BulkAction $action): void {
                            $data = $action->getData();
                            $employee = Employee::query()->findOrFail((int) $data['employee_id']);
                            $selectedCustomerIds = collect($data['customer_ids'] ?? [])
                                ->map(fn ($id): int => (int) $id)
                                ->unique()
                                ->values()
                                ->all();

                            DB::transaction(function () use ($employee, $selectedCustomerIds): void {
                                DB::table('employee_customers')
                                    ->whereIn('customer_id', $selectedCustomerIds)
                                    ->delete();

                                $employee->customers()->sync($selectedCustomerIds);
                            });

                            Notification::make()
                                ->title(__('messages.sales_staff_assignments_updated'))
                                ->success()
                                ->send();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
