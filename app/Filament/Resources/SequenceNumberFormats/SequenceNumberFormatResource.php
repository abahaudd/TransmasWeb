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
        return __('labels.nav.groups.control_panel');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.sequence_number_formats');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('category')
                ->label(__('labels.sequence_number_format.category'))
                ->helperText(__('labels.sequence_number_format.category_helper'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(200),
            TextInput::make('prefix')
                ->label(__('labels.sequence_number_format.prefix'))
                ->maxLength(200),
            TextInput::make('separator')
                ->label(__('labels.sequence_number_format.separator'))
                ->maxLength(50)
                ->default('-'),
            TextInput::make('incrementer')
                ->label(__('labels.sequence_number_format.current_number'))
                ->helperText(__('labels.sequence_number_format.current_number_helper'))
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
            TextInput::make('length')
                ->label(__('labels.sequence_number_format.zero_pad_length'))
                ->helperText(__('labels.sequence_number_format.zero_pad_length_helper'))
                ->numeric()
                ->minValue(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label(__('labels.sequence_number_format.category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefix')
                    ->label(__('labels.sequence_number_format.prefix'))
                    ->placeholder('-'),
                TextColumn::make('separator')
                    ->label(__('labels.sequence_number_format.separator'))
                    ->placeholder('-'),
                TextColumn::make('incrementer')
                    ->label(__('labels.sequence_number_format.current_number'))
                    ->sortable(),
                TextColumn::make('length')
                    ->label(__('labels.sequence_number_format.zero_pad_length'))
                    ->placeholder('-'),
                TextColumn::make('next_preview')
                    ->label(__('labels.sequence_number_format.next_number_preview'))
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
