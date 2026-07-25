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
                Section::make('Company')
                    ->description('Shown in the public site header and footer.')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->imageEditor()
                            ->helperText('Displayed in the site navigation next to the company name.'),
                    ])
                    ->columnSpanFull(),
                Section::make('Company Contact details')
                    ->description('Shown in the public site footer.')
                    ->schema([
                        Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->tel(),
                        TextInput::make('email')
                            ->email(),
                        TextInput::make('website')
                            ->url(),
                    ])
                    ->columns(3)
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
