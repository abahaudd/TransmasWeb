<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('labels.name'))
                    ->required(),
                TextInput::make('guard_name')
                    ->label(__('labels.permission.guard_name'))
                    ->required(),
            ]);
    }
}
