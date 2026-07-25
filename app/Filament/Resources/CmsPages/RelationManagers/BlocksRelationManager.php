<?php

namespace App\Filament\Resources\CmsPages\RelationManagers;

use App\Models\Cms\Block;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'blocks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('labels.block.type'))
                    ->options(Block::TYPES)
                    ->required(),
                TextInput::make('name')
                    ->label(__('labels.block.name'))
                    ->helperText(__('labels.block.name_helper'))
                    ->maxLength(255),
                TextInput::make('position')
                    ->label(__('labels.block.position'))
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('labels.block.active'))
                    ->default(true),
                Textarea::make('data')
                    ->label(__('labels.block.content_json'))
                    ->rows(16)
                    ->rule('nullable|json')
                    ->formatStateUsing(fn ($state): ?string => filled($state)
                        ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                        : null)
                    ->dehydrateStateUsing(fn (?string $state): ?array => filled($state)
                        ? json_decode($state, true)
                        : null)
                    ->helperText(__('labels.block.content_json_helper'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('position')
                    ->label(__('labels.block.position'))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('labels.block.type'))
                    ->badge(),
                TextColumn::make('name')
                    ->label(__('labels.block.name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('labels.block.active'))
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
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
            ->defaultSort('position')
            ->reorderable('position');
    }
}
