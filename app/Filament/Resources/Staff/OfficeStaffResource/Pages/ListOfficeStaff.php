<?php

namespace App\Filament\Resources\Staff\OfficeStaffResource\Pages;

use Illuminate\Support\Arr;
use App\Filament\Resources\Staff\OfficeStaffResource;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Country;
use App\Models\Setting;
use App\Models\User;
use App\Services\UserService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;

class ListOfficeStaff extends ListRecords
{
    protected static string $resource = OfficeStaffResource::class;

    public function getTitle(): string
    {
        return 'Staff';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNewStaff')
                ->label('Add New Staff')
                ->modalHeading('Add New Staff')
                ->modalDescription('Create a staff account with minimal details. Role assignment is handled automatically.')
                ->modalWidth('4xl')
                ->form([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('first_name')
                                ->label('First Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required()
                                ->maxLength(255),
                            Select::make('branch_id')
                                ->label('Branch')
                                ->required()
                                ->options(fn () => Branch::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    if (blank($state)) {
                                        return;
                                    }

                                    $branch = Branch::query()->with('address')->find($state);

                                    if (! $branch) {
                                        return;
                                    }

                                    $set('address_line', $branch->address?->address);
                                    $set('location', $branch->address?->location);
                                    $set('territory', $branch->address?->territory);
                                    $set('postal_code', $branch->address?->postal_code);
                                    $set('country_id', $branch->address?->country_id);
                                    $set('phone', $branch->phone);
                                })
                                ->native(false)
                                ->columnSpan(2),
                            Select::make('staff_role')
                                ->label('Staff Type')
                                ->required()
                                ->options(Arr::only(UserService::staffTypeOptions(),[
                                    UserService::TYPE_OFFICE_STAFF,
                                    UserService::TYPE_GENERAL_STAFF,
                                ]))
                                ->default('office_staff')
                                ->native(false),
                            Select::make('manager_id')
                                ->label('Manager')
                                ->options(fn () => Employee::query()
                                    ->with('person')
                                    ->whereHas('user.roles', fn ($query) => $query->where('name', 'manager'))
                                    ->get()
                                    ->mapWithKeys(fn (Employee $employee): array => [
                                        $employee->id => trim(($employee->person?->first_name ?? '') . ' ' . ($employee->person?->last_name ?? '')) ?: ('Manager #' . $employee->id),
                                    ])
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->native(false),
                            TextInput::make('email')
                                ->label('Email (optional)')
                                ->email()
                                ->unique(table: User::class, column: 'email', ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Phone (optional)')
                                ->tel()
                                ->unique(table: User::class, column: 'phone', ignoreRecord: true)
                                ->maxLength(255),
                            TextInput::make('username')
                                ->label('Username')
                                ->required()
                                ->unique(table: User::class, column: 'username')
                                ->maxLength(255),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('address_line')
                                ->label(fn (): string => (string) (
                                    Setting::getPayloadValue('location', 'headings', 'address')
                                    ?? Setting::getPayloadValue('location', 'headings', 'address_line')
                                    ?? 'Address'
                                ))
                                ->maxLength(255)
                                ->columnSpan(2),
                            TextInput::make('location')
                                ->label(fn (): string => (string) (
                                    Setting::getPayloadValue('location', 'headings', 'location')
                                    ?? Setting::getPayloadValue('location', 'headings', 'city')
                                    ?? 'Location / City'
                                ))
                                ->required()
                                ->maxLength(255),
                            TextInput::make('territory')
                                ->label(fn (): string => (string) (
                                    Setting::getPayloadValue('location', 'headings', 'territory')
                                    ?? Setting::getPayloadValue('location', 'headings', 'state')
                                    ?? 'Territory'
                                ))
                                ->maxLength(255),
                            TextInput::make('postal_code')
                                ->label(fn (): string => (string) (
                                    Setting::getPayloadValue('location', 'headings', 'postal_code')
                                    ?? Setting::getPayloadValue('location', 'headings', 'zip')
                                    ?? 'Postal Code'
                                ))
                                ->maxLength(255),
                            Select::make('country_id')
                                ->label(fn (): string => (string) (
                                    Setting::getPayloadValue('location', 'headings', 'country')
                                    ?? 'Country'
                                ))
                                ->required()
                                ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->native(false),
                        ]),
                ])
                ->action(function (array $data): void {
                    $result = app(UserService::class)->createByType([
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'username' => $data['username'],
                        'temporary_password' => $data['password'],
                        'branch_id' => $data['branch_id'],
                        'manager_id' => $data['manager_id'] ?? null,
                        'address_line' => $data['address_line'],
                        'location' => $data['location'],
                        'territory' => $data['territory'] ?? null,
                        'postal_code' => $data['postal_code'] ?? null,
                        'country_id' => $data['country_id'],
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'user_type' => $data['staff_role'],
                    ]);

                    /** @var User $user */
                    $user = $result['user'];
                    $temporaryPassword = $result['temporary_password'];

                    Notification::make()
                        ->title('Staff account created')
                        ->body("User ID: {$user->username} | Temporary password: {$temporaryPassword}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
