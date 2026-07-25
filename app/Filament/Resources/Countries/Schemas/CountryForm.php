<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('labels.name'))
                    ->required(),
                TextInput::make('country_code')
                    ->label(__('labels.country_fields.country_code'))
                    ->required(),
                TextInput::make('country_code_alpha3')
                    ->label(__('labels.country_fields.country_code_alpha3'))
                    ->required(),
                TextInput::make('location_title')
                    ->label(__('labels.country_fields.location_title')),
                TextInput::make('territory_title')
                    ->label(__('labels.country_fields.territory_title')),
                TextInput::make('postal_code_title')
                    ->label(__('labels.country_fields.postal_code_title')),
            ]);
    }
}
