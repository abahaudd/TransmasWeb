<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label(__('labels.username'))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->label(__('labels.email_address'))
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label(__('labels.user.email_verified_at')),
                TextInput::make('password')
                    ->label(__('labels.password'))
                    ->password()
                    ->required(),
            ]);
    }
}
