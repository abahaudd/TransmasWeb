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
                TextEntry::make('name')
                    ->label(__('labels.name')),
                TextEntry::make('country_code')
                    ->label(__('labels.country_fields.country_code')),
                TextEntry::make('country_code_alpha3')
                    ->label(__('labels.country_fields.country_code_alpha3')),
                TextEntry::make('location_title')
                    ->label(__('labels.country_fields.location_title')),
                TextEntry::make('territory_title')
                    ->label(__('labels.country_fields.territory_title')),
                TextEntry::make('postal_code_title')
                    ->label(__('labels.country_fields.postal_code_title')),
            ]);
    }
}
