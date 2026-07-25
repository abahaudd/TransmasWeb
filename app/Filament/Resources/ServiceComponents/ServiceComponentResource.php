<?php

namespace App\Filament\Resources\ServiceComponents;

use App\Filament\Resources\ServiceComponents\Pages\CreateServiceComponent;
use App\Filament\Resources\ServiceComponents\Pages\EditServiceComponent;
use App\Filament\Resources\ServiceComponents\Pages\ListServiceComponents;
use App\Filament\Resources\ServiceComponents\RelationManagers\TasksRelationManager;
use App\Models\ServiceComponent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceComponentResource extends Resource
{
    protected static ?string $model = ServiceComponent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 21;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.service_catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.service_components');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('labels.name'))
                ->required()
                ->maxLength(150),
            TextInput::make('code')
                ->label(__('labels.code'))
                ->maxLength(20),
            Textarea::make('description')
                ->label(__('labels.service_component.description'))
                ->rows(3)
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label(__('labels.active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('labels.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('labels.code'))
                    ->placeholder('-'),
                TextColumn::make('tasks_count')
                    ->label(__('labels.nav.tasks'))
                    ->counts('tasks'),
                IconColumn::make('is_active')
                    ->label(__('labels.active'))
                    ->boolean()
                    ->sortable(),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceComponents::route('/'),
            'create' => CreateServiceComponent::route('/create'),
            'edit' => EditServiceComponent::route('/{record}/edit'),
        ];
    }
}
