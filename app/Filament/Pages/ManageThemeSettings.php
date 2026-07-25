<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSettingsGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageThemeSettings extends SettingsPage
{
    use InteractsWithSettingsGroup;

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.theme_settings');
    }

    public function getTitle(): string
    {
        return __('labels.nav.theme_settings');
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
        return 'theme';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.settings.theme.section_palette'))
                    ->description(__('labels.settings.theme.section_palette_description'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('primary_color')
                                    ->label(__('labels.settings.theme.primary_color'))
                                    ->required(),
                                ColorPicker::make('gray_color')
                                    ->label(__('labels.settings.theme.gray_color'))
                                    ->helperText(__('labels.settings.theme.gray_color_helper'))
                                    ->required(),
                                ColorPicker::make('success_color')
                                    ->label(__('labels.settings.theme.success_color'))
                                    ->required(),
                                ColorPicker::make('warning_color')
                                    ->label(__('labels.settings.theme.warning_color'))
                                    ->required(),
                                ColorPicker::make('danger_color')
                                    ->label(__('labels.settings.theme.danger_color'))
                                    ->required(),
                                ColorPicker::make('info_color')
                                    ->label(__('labels.settings.theme.info_color'))
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('labels.settings.theme.section_layout'))
                    ->description(__('labels.settings.theme.section_layout_description'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('page_background_color')
                                    ->label(__('labels.settings.theme.page_background_color'))
                                    ->helperText(__('labels.settings.theme.page_background_color_helper'))
                                    ->required(),
                                ColorPicker::make('menu_background_color')
                                    ->label(__('labels.settings.theme.menu_background_color'))
                                    ->helperText(__('labels.settings.theme.menu_background_color_helper'))
                                    ->required(),
                                ColorPicker::make('card_background_color')
                                    ->label(__('labels.settings.theme.card_background_color'))
                                    ->helperText(__('labels.settings.theme.card_background_color_helper'))
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
