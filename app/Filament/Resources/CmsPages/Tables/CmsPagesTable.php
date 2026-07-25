<?php

namespace App\Filament\Resources\CmsPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CmsPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('labels.cms_page.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('labels.cms_page.slug'))
                    ->searchable()
                    ->prefix('/'),
                TextColumn::make('template')
                    ->label(__('labels.cms_page.template'))
                    ->badge(),
                IconColumn::make('is_published')
                    ->label(__('labels.cms_page.published'))
                    ->boolean(),
                TextColumn::make('blocks_count')
                    ->label(__('labels.cms_page.blocks'))
                    ->counts('blocks'),
                TextColumn::make('updated_at')
                    ->label(__('labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label(__('labels.cms_page.published')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('title');
    }
}
