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

    protected static ?string $title = 'Formatting Settings';

    protected static ?string $navigationLabel = 'Formatting Settings';

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
        return 'formatting';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Currency')
                    ->description('Controls how monetary amounts are displayed app-wide via the Format helper.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('currency_symbol')
                                    ->label('Currency symbol')
                                    ->required()
                                    ->maxLength(20)
                                    ->helperText('The official new AED symbol (U+20C3) can be pasted here once your system font supports it; plain "AED" is used until then.'),
                                Select::make('currency_symbol_position')
                                    ->label('Symbol position')
                                    ->options([
                                        'before' => 'Before amount (AED 1,234.50)',
                                        'after' => 'After amount (1,234.50 AED)',
                                    ])
                                    ->required()
                                    ->native(false),
                                TextInput::make('thousands_separator')
                                    ->label('Thousands separator')
                                    ->maxLength(1)
                                    ->required(),
                                TextInput::make('decimal_separator')
                                    ->label('Decimal separator')
                                    ->maxLength(1)
                                    ->required(),
                                TextInput::make('decimal_places')
                                    ->label('Decimal places')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(6)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Date')
                    ->description('Controls how dates are displayed app-wide via the Format helper.')
                    ->schema([
                        Select::make('date_format')
                            ->label('Date format')
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
