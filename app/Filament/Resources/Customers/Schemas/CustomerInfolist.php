<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('address.address')
                    ->label('Address'),
                TextEntry::make('phone_main'),
                TextEntry::make('phone_secondary'),
                TextEntry::make('email'),
                TextEntry::make('website'),
                TextEntry::make('parent.name')
                    ->label('Parent customer'),
            ]);
    }
}
