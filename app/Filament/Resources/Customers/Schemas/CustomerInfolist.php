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
                TextEntry::make('name')
                    ->label(__('labels.name')),
                TextEntry::make('address.address')
                    ->label(__('labels.address')),
                TextEntry::make('phone_main')
                    ->label(__('labels.phone')),
                TextEntry::make('phone_secondary')
                    ->label(__('labels.mobile')),
                TextEntry::make('email')
                    ->label(__('labels.email')),
                TextEntry::make('website')
                    ->label(__('labels.website')),
                TextEntry::make('parent.name')
                    ->label(__('labels.customer.parent_customer')),
            ]);
    }
}
