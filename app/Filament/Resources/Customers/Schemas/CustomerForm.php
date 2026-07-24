<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use App\Models\Country;
use App\Models\Setting;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        $locationHeadings = Cache::remember('location_headings', 3600, function () {
            try {
                return Setting::where('group', 'location')->where('name', 'headings')->first()?->payload ?? [];
            } catch (\Exception $e) {
                return [];
            }
        });

        $addressComponents = [];

        if (isset($locationHeadings['address'])) {
            $addressComponents[] = TextInput::make('address_line')
                ->label($locationHeadings['address'])
                ->prefixIcon('heroicon-o-map-pin')
                ->columnSpanFull();
        }
        if (isset($locationHeadings['location'])) {
            $addressComponents[] = TextInput::make('location')
                ->label($locationHeadings['location'])
                ->prefixIcon('heroicon-o-building-library');
        }
        if (isset($locationHeadings['territory'])) {
            $addressComponents[] = TextInput::make('territory')
                ->label($locationHeadings['territory'])
                ->prefixIcon('heroicon-o-map');
        }
        if (isset($locationHeadings['postal_code'])) {
            $addressComponents[] = TextInput::make('postal_code')
                ->label($locationHeadings['postal_code'])
                ->prefixIcon('heroicon-o-hashtag');
        }
        if (isset($locationHeadings['country']) || isset($locationHeadings['country_id'])) {
            $countryLabel = $locationHeadings['country'] ?? $locationHeadings['country_id'];
            $addressComponents[] = Select::make('country_id')
                ->label($countryLabel)
                ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->preload()
                ->placeholder(__('help.select_option'))
                ->prefixIcon('heroicon-o-flag')
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('country_code')
                        ->label('Country calling code')
                        ->required(),
                    TextInput::make('country_code_alpha3')
                        ->required(),
                    TextInput::make('location_title'),
                    TextInput::make('territory_title'),
                    TextInput::make('postal_code_title'),
                ])
                ->createOptionUsing(function (array $data): int|string {
                    return Country::create($data)->getKey();
                })
                ->columnSpanFull();
        }
        if (isset($locationHeadings['latitude'])) {
            $addressComponents[] = TextInput::make('latitude')
                ->numeric()
                ->label($locationHeadings['latitude'])
                ->prefixIcon('heroicon-o-arrows-pointing-out');
        }
        if (isset($locationHeadings['longitude'])) {
            $addressComponents[] = TextInput::make('longitude')
                ->numeric()
                ->label($locationHeadings['longitude'])
                ->prefixIcon('heroicon-o-arrows-pointing-out');
        }

        return $schema
            ->components([
                Section::make('Customer Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->columnSpanFull(),
                                TextInput::make('phone_main')
                                    ->tel()
                                    ->prefixIcon('heroicon-o-phone'),
                                TextInput::make('phone_secondary')
                                    ->tel()
                                    ->prefixIcon('heroicon-o-device-phone-mobile'),
                                TextInput::make('email')
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope'),
                                TextInput::make('website')
                                    ->prefixIcon('heroicon-o-globe-alt'),
                                Select::make('parent_id')
                                    ->label('Parent customer')
                                    ->options(fn () => Customer::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->placeholder(__('help.select_option'))
                                    ->default(null)
                                    ->prefixIcon('heroicon-o-building-office-2')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Location Details')
                    ->schema([
                        Grid::make(2)
                            ->schema($addressComponents),
                    ])
                    ->hidden(empty($addressComponents)),
            ]);
    }
}
