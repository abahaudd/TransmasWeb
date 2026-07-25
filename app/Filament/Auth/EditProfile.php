<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class EditProfile extends BaseEditProfile
{
    // Users are identified by username instead of a display name
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('labels.auth.username'))
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }
}
