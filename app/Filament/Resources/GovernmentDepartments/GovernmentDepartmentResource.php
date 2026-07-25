<?php

namespace App\Filament\Resources\GovernmentDepartments;

use App\Filament\Resources\GovernmentDepartments\Pages\CreateGovernmentDepartment;
use App\Filament\Resources\GovernmentDepartments\Pages\EditGovernmentDepartment;
use App\Filament\Resources\GovernmentDepartments\Pages\ListGovernmentDepartments;
use App\Models\GovernmentDepartment;
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

class GovernmentDepartmentResource extends Resource
{
    protected static ?string $model = GovernmentDepartment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?int $navigationSort = 24;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.service_catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.government_departments');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('labels.name'))
                ->required()
                ->maxLength(200),
            TextInput::make('code')
                ->label(__('labels.code'))
                ->maxLength(30),
            Textarea::make('remarks')
                ->label(__('labels.government_department.remarks'))
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

    public static function getPages(): array
    {
        return [
            'index' => ListGovernmentDepartments::route('/'),
            'create' => CreateGovernmentDepartment::route('/create'),
            'edit' => EditGovernmentDepartment::route('/{record}/edit'),
        ];
    }
}
