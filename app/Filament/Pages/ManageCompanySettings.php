<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSettingsGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageCompanySettings extends SettingsPage
{
    use InteractsWithSettingsGroup;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.company_settings');
    }

    public function getTitle(): string
    {
        return __('labels.nav.company_settings');
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
        return 'company';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.settings.company.section_identity'))
                    ->description(__('labels.settings.company.section_identity_description'))
                    ->schema([
                        TextInput::make('company_name')
                            ->label(__('labels.settings.company.company_name'))
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('logo_path')
                            ->label(__('labels.settings.company.logo'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->imageEditor()
                            ->helperText(__('labels.settings.company.logo_helper')),
                    ])
                    ->columnSpanFull(),
                Section::make(__('labels.settings.company.section_contact'))
                    ->description(__('labels.settings.company.section_contact_description'))
                    ->schema([
                        Textarea::make('address')
                            ->label(__('labels.address'))
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label(__('labels.phone'))
                            ->tel(),
                        TextInput::make('email')
                            ->label(__('labels.email'))
                            ->email(),
                        TextInput::make('website')
                            ->label(__('labels.website'))
                            ->url(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
