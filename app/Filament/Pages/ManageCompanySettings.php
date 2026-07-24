<?php

namespace App\Filament\Pages;

use App\Settings\CompanySettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageCompanySettings extends SettingsPage
{
    protected static string $settings = CompanySettings::class;

    protected static ?string $title = 'Company Settings';

    protected static ?string $navigationLabel = 'Company Settings';

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
                Section::make('Identity')
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
                Section::make('Contact details')
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
            ]);
    }
}
