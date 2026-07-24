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
                    ->required(),
                TextInput::make('country_code')
                    ->required(),
                TextInput::make('country_code_alpha3')
                    ->required(),
                TextInput::make('location_title'),
                TextInput::make('territory_title'),
                TextInput::make('postal_code_title'),
            ]);
    }
}
