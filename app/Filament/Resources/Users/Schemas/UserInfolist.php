<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('username')
                    ->label(__('labels.username')),
                TextEntry::make('email')
                    ->label(__('labels.email_address')),
                TextEntry::make('email_verified_at')
                    ->label(__('labels.user.email_verified_at'))
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->label(__('labels.created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(__('labels.updated_at'))
                    ->dateTime(),
            ]);
    }
}
