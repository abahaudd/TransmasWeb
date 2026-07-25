<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    // Users register with a username instead of a display name
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('labels.auth.username'))
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->autofocus();
    }

    /**
     * Self-registered users get the panel_user role so that
     * User::canAccessPanel() lets them in with a bare dashboard.
     */
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        $user->assignRole('panel_user');

        return $user;
    }
}
