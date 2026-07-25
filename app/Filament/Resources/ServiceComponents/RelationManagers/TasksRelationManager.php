<?php

namespace App\Filament\Resources\ServiceComponents\RelationManagers;

use App\Models\GovernmentDepartment;
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
use Illuminate\Database\Eloquent\Model;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('labels.nav.tasks');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('labels.name'))
                ->required()
                ->maxLength(200)
                ->columnSpanFull(),
            TextInput::make('code')
                ->label(__('labels.code'))
                ->maxLength(20),
            TextInput::make('cost')
                ->label(__('labels.task.cost'))
                ->numeric()
                ->default(0)
                ->prefix('AED')
                ->required(),
            Select::make('government_department_id')
                ->label(__('labels.task.government_department'))
                ->helperText(__('labels.task.government_department_helper'))
                ->options(fn () => GovernmentDepartment::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->preload(),
            Toggle::make('is_active')
                ->label(__('labels.active'))
                ->default(true),
            Textarea::make('description')
                ->label(__('labels.task.description'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('labels.code'))
                    ->placeholder('-'),
                TextColumn::make('cost')
                    ->label(__('labels.task.cost'))
                    ->formatStateUsing(fn (string $state): string => format_money($state)),
                TextColumn::make('governmentDepartment.name')
                    ->label(__('labels.task.government_department'))
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label(__('labels.active'))
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
            ->defaultSort('sequence')
            ->reorderable('sequence');
    }
}
