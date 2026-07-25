<?php

namespace App\Filament\Resources\Countries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->searchable(),
                TextColumn::make('country_code')
                    ->label(__('labels.country_fields.country_code'))
                    ->searchable(),
                TextColumn::make('country_code_alpha3')
                    ->label(__('labels.country_fields.country_code_alpha3'))
                    ->searchable(),
                TextColumn::make('location_title')
                    ->label(__('labels.country_fields.location_title'))
                    ->searchable(),
                TextColumn::make('territory_title')
                    ->label(__('labels.country_fields.territory_title'))
                    ->searchable(),
                TextColumn::make('postal_code_title')
                    ->label(__('labels.country_fields.postal_code_title'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->tooltip(__('labels.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
