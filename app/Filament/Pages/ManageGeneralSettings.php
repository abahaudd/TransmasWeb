<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageGeneralSettings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Control Panel';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Site')
                    ->description('Identity of this application, available to all modules.')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('site_description')
                            ->label('Site description')
                            ->rows(3),
                        TextInput::make('support_email')
                            ->label('Support email')
                            ->email(),
                    ])
                    ->columnSpanFull(),
                Section::make('Localization')
                    ->schema([
                        Select::make('timezone')
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(fn (string $tz) => [$tz => $tz]))
                            ->searchable()
                            ->required(),
                        TextInput::make('locale')
                            ->label('Default locale')
                            ->required()
                            ->maxLength(10),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
