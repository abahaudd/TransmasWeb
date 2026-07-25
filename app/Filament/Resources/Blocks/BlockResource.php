<?php

namespace App\Filament\Resources\Blocks;

use App\Filament\Resources\Blocks\Pages\CreateBlock;
use App\Filament\Resources\Blocks\Pages\EditBlock;
use App\Filament\Resources\Blocks\Pages\ListBlocks;
use App\Models\Cms\Block;
use App\Models\Cms\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BlockResource extends Resource
{
    protected static ?string $model = Block::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationLabel(): string
    {
        return 'Blocks';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page_id')
                    ->label('Page')
                    ->options(fn (): array => Page::query()->orderBy('title')->pluck('title', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->options(Block::TYPES)
                    ->required(),
                TextInput::make('name')
                    ->maxLength(255),
                TextInput::make('position')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page.title')
                    ->label('Page')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('position')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->orderBy('page_id')
                ->orderBy('position'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlocks::route('/'),
            'create' => CreateBlock::route('/create'),
            'edit' => EditBlock::route('/{record}/edit'),
        ];
    }
}
