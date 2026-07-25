<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Models\Company;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parent']))
            ->columns([
                TextColumn::make('legal_name')
                    ->label(__('labels.company.legal_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_type')
                    ->label(__('labels.company.company_type'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_code')
                    ->label(__('labels.company.company_code'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('parent.legal_name')
                    ->label(__('labels.company.parent_company'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('labels.status'))
                    ->badge()
                    ->color(fn (string $state): string => $state === Company::STATUS_ACTIVE ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('labels.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website')
                    ->label(__('labels.website'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_work_hour')
                    ->label(__('labels.company.start_work_hour'))
                    ->time()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_work_hour')
                    ->label(__('labels.company.end_work_hour'))
                    ->time()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('weekends')
                    ->label(__('labels.company.weekends'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('company_type')
                    ->label(__('labels.company.company_type'))
                    ->options(Company::typeOptions()),
                SelectFilter::make('status')
                    ->label(__('labels.status'))
                    ->options(Company::statusOptions()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
