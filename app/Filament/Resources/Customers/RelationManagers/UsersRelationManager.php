<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('labels.account_user.section_title');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('labels.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('username')
                ->label(__('labels.username'))
                ->required()
                ->maxLength(255)
                ->unique('users', 'username'),
            Grid::make(2)
                ->schema([
                    TextInput::make('email')
                        ->label(__('labels.email'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique('users', 'email'),
                    TextInput::make('phone')
                        ->label(__('labels.phone'))
                        ->tel()
                        ->maxLength(255),
                ])
                ->columnSpanFull(),
            Grid::make(2)
                ->schema([
                    DatePicker::make('start_date')
                        ->label(__('labels.account_user.create_date'))
                        ->default(now())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set): void {
                            if (blank($state)) {
                                return;
                            }

                            $validityDays = (int) Setting::getPayloadValue('customer', 'user_id', 'validity', 365);

                            $set('end_date', Carbon::parse($state)->addDays($validityDays)->toDateString());
                        })
                        ->native(false),
                    DatePicker::make('end_date')
                        ->label(__('labels.end_date'))
                        ->required()
                        ->default(fn () => now()->addDays(
                            (int) Setting::getPayloadValue('customer', 'user_id', 'validity', 365)
                        ))
                        ->native(false),
                ])
                ->columnSpanFull(),
            Grid::make(2)
                ->schema([
                    TextInput::make('password')
                        ->label(__('labels.password'))
                        ->password()
                        ->revealable()
                        ->required()
                        ->same('password_confirmation')
                        ->maxLength(255)
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                    TextInput::make('password_confirmation')
                        ->label(__('labels.verify_password'))
                        ->password()
                        ->revealable()
                        ->required()
                        ->dehydrated(false)
                        ->maxLength(255),
                ])
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('username')
                    ->label(__('labels.username'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('labels.account_user.create_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('labels.end_date'))
                    ->date()
                    ->icon('heroicon-o-pencil-square')
                    ->iconPosition(IconPosition::After)
                    ->tooltip(__('actions.edit_end_date'))
                    ->action(
                        Action::make('editEndDate')
                            ->fillForm(fn (User $record): array => [
                                'end_date' => $record->end_date,
                            ])
                            ->schema([
                                DatePicker::make('end_date')
                                    ->label(__('labels.end_date'))
                                    ->native(false),
                            ])
                            ->action(function (User $record, array $data): void {
                                $record->update([
                                    'end_date' => $data['end_date'] ?? null,
                                ]);

                                Notification::make()
                                    ->title(__('messages.end_date_updated'))
                                    ->success()
                                    ->send();
                            })
                    )
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('actions.add_user'))
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                Action::make('changePassword')
                    ->label(__('actions.reset_password'))
                    ->icon('heroicon-o-key')
                    ->iconButton()
                    ->tooltip(__('actions.reset_password'))
                    ->schema([
                        TextInput::make('password')
                            ->label(__('labels.password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password_confirmation')
                            ->maxLength(255),
                        TextInput::make('password_confirmation')
                            ->label(__('labels.verify_password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->dehydrated(false)
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'password' => Hash::make($data['password']),
                        ]);

                        Notification::make()
                            ->title(__('messages.password_changed'))
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->iconButton()
                    ->tooltip(__('actions.delete_user')),
            ]);
    }
}
