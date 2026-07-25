<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSettingsGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageFormattingSettings extends SettingsPage
{
    use InteractsWithSettingsGroup;

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.formatting_settings');
    }

    public function getTitle(): string
    {
        return __('labels.nav.formatting_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.control_panel');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected function settingsGroup(): string
    {
        return 'formatting';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.settings.formatting.section_currency'))
                    ->description(__('labels.settings.formatting.section_currency_description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('currency_symbol')
                                    ->label(__('labels.settings.formatting.currency_symbol'))
                                    ->required()
                                    ->maxLength(20)
                                    ->helperText(__('labels.settings.formatting.currency_symbol_helper')),
                                Select::make('currency_symbol_position')
                                    ->label(__('labels.settings.formatting.symbol_position'))
                                    ->options([
                                        'before' => __('labels.settings.formatting.symbol_position_before'),
                                        'after' => __('labels.settings.formatting.symbol_position_after'),
                                    ])
                                    ->required()
                                    ->native(false),
                                TextInput::make('thousands_separator')
                                    ->label(__('labels.settings.formatting.thousands_separator'))
                                    ->maxLength(1)
                                    ->required(),
                                TextInput::make('decimal_separator')
                                    ->label(__('labels.settings.formatting.decimal_separator'))
                                    ->maxLength(1)
                                    ->required(),
                                TextInput::make('decimal_places')
                                    ->label(__('labels.settings.formatting.decimal_places'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(6)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make(__('labels.settings.formatting.section_date'))
                    ->description(__('labels.settings.formatting.section_date_description'))
                    ->schema([
                        Select::make('date_format')
                            ->label(__('labels.settings.formatting.date_format'))
                            ->options(fn (): array => collect([
                                'd/m/Y',
                                'm/d/Y',
                                'Y-m-d',
                                'd-M-Y',
                                'd F Y',
                            ])->mapWithKeys(fn (string $format): array => [$format => now()->format($format) . " ({$format})"])->all())
                            ->required()
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
