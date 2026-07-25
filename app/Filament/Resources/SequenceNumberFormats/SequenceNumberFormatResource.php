<?php

namespace App\Filament\Resources\SequenceNumberFormats;

use App\Filament\Resources\SequenceNumberFormats\Pages\CreateSequenceNumberFormat;
use App\Filament\Resources\SequenceNumberFormats\Pages\EditSequenceNumberFormat;
use App\Filament\Resources\SequenceNumberFormats\Pages\ListSequenceNumberFormats;
use App\Models\SequenceNumberFormat;
use App\Services\SequenceNumberService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SequenceNumberFormatResource extends Resource
{
    protected static ?string $model = SequenceNumberFormat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHashtag;

    protected static ?int $navigationSort = 14;

    protected static ?string $recordTitleAttribute = 'category';

    public static function getNavigationGroup(): ?string
    {
        return 'Control Panel';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sequence Number Formats';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('category')
                ->helperText('e.g. invoice, receipt, employee — the key code passed to SequenceNumberService::next().')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(200),
            TextInput::make('prefix')
                ->maxLength(200),
            TextInput::make('separator')
                ->maxLength(50)
                ->default('-'),
            TextInput::make('incrementer')
                ->label('Current Number')
                ->helperText('The last number issued. The next call to next() returns this value + 1.')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
            TextInput::make('length')
                ->label('Zero-pad Length')
                ->helperText('Total digit width of the numeric part, zero-padded. Leave blank for no padding.')
                ->numeric()
                ->minValue(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefix')
                    ->placeholder('-'),
                TextColumn::make('separator')
                    ->placeholder('-'),
                TextColumn::make('incrementer')
                    ->label('Current Number')
                    ->sortable(),
                TextColumn::make('length')
                    ->label('Zero-pad Length')
                    ->placeholder('-'),
                TextColumn::make('next_preview')
                    ->label('Next Number Preview')
                    ->state(fn (SequenceNumberFormat $record): string => app(SequenceNumberService::class)
                        ->current($record->category) ?? '-'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSequenceNumberFormats::route('/'),
            'create' => CreateSequenceNumberFormat::route('/create'),
            'edit' => EditSequenceNumberFormat::route('/{record}/edit'),
        ];
    }
}
