<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('country'),
                TextEntry::make('country_code'),
                TextEntry::make('country_code_alpha3'),
                TextEntry::make('location_title'),
                TextEntry::make('territory_title'),
                TextEntry::make('postal_code_title'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
