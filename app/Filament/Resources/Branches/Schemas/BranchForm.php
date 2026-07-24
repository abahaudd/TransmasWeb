<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Models\Branch;
use App\Models\Country;
use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        $locationHeadings = Cache::remember('location_headings', 3600, function () {
            try {
                return Setting::where('group', 'location')->where('name', 'headings')->first()?->payload ?? [];
            } catch (\Exception $exception) {
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
                ->placeholder(__('filament-forms::components.select.placeholder'))
                ->searchable()
                ->preload()
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
                Section::make('Branch Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->columnSpanFull(),
                                Select::make('parent_id')
                                    ->label('Parent branch')
                                    ->options(function (?Branch $record): array {
                                        $excludedIds = [];

                                        if ($record) {
                                            $excludedIds = [$record->getKey(), ...$record->getDescendantIds()];
                                        }

                                        return Branch::query()
                                            ->when(! empty($excludedIds), fn ($query) => $query->whereNotIn('id', $excludedIds))
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                            ->placeholder(__('filament-forms::components.select.placeholder'))
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Leave empty to mark this as the main branch.')
                                    ->columnSpanFull(),
                                TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-phone'),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-envelope'),
                                TimePicker::make('start_work_hour')
                                    ->seconds(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TimePicker::make('end_work_hour')
                                    ->seconds(false)
                                    ->prefixIcon('heroicon-o-clock'),
                                TextInput::make('weekends')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-calendar-days'),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
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