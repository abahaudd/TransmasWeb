<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\InteractsWithSettingsGroup;
use App\Services\SettingsService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageGeneralSettings extends SettingsPage
{
    use InteractsWithSettingsGroup;

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.site_settings');
    }

    public function getTitle(): string
    {
        return __('labels.nav.site_settings');
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
        return 'general';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $companyData = app(SettingsService::class)->getGroup('company');

        return array_merge($data, [
            'company_name' => $companyData['company_name'] ?? null,
            'logo_path' => $companyData['logo_path'] ?? null,
            'address' => $companyData['address'] ?? null,
            'phone' => $companyData['phone'] ?? null,
            'email' => $companyData['email'] ?? null,
            'website' => $companyData['website'] ?? null,
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        app(SettingsService::class)->setMany('company', [
            'company_name' => $data['company_name'] ?? null,
            'logo_path' => $data['logo_path'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
        ]);

        unset(
            $data['company_name'],
            $data['logo_path'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['website'],
        );

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.settings.general.section_site'))
                    ->description(__('labels.settings.general.section_site_description'))
                    ->schema([
                        TextInput::make('site_name')
                            ->label(__('labels.settings.general.site_name'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('site_description')
                            ->label(__('labels.settings.general.site_description'))
                            ->rows(3),
                        TextInput::make('support_email')
                            ->label(__('labels.settings.general.support_email'))
                            ->email(),
                    ])
                    ->columnSpanFull(),
                Section::make(__('labels.settings.general.section_company'))
                    ->description(__('labels.settings.general.section_company_description'))
                    ->schema([
                        TextInput::make('company_name')
                            ->label(__('labels.settings.general.company_name'))
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('logo_path')
                            ->label(__('labels.settings.general.logo'))
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->imageEditor()
                            ->helperText(__('labels.settings.general.logo_helper')),
                    ])
                    ->columnSpanFull(),
                Section::make(__('labels.settings.general.section_contact'))
                    ->description(__('labels.settings.general.section_contact_description'))
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
                Section::make(__('labels.settings.general.section_localization'))
                    ->schema([
                        Select::make('timezone')
                            ->label(__('labels.settings.general.timezone'))
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(fn (string $tz) => [$tz => $tz]))
                            ->searchable()
                            ->required(),
                        TextInput::make('locale')
                            ->label(__('labels.settings.general.default_locale'))
                            ->required()
                            ->maxLength(10),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
