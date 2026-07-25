<?php

namespace App\Filament\Resources\Tasks;

use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Models\GovernmentDepartment;
use App\Models\ServiceComponent;
use App\Models\Task;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?int $navigationSort = 23;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.service_catalog');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.tasks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('labels.name'))
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Select::make('service_component_id')
                        ->label(__('labels.task.service_component'))
                        ->helperText(__('labels.task.service_component_helper'))
                        ->options(fn () => ServiceComponent::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload(),
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
                    TextInput::make('sequence')
                        ->label(__('labels.task.sequence'))
                        ->helperText(__('labels.task.sequence_helper'))
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Toggle::make('is_active')
                        ->label(__('labels.active'))
                        ->default(true),
                ]),
            Textarea::make('description')
                ->label(__('labels.task.description'))
                ->rows(3)
                ->columnSpanFull(),
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
                TextColumn::make('serviceComponent.name')
                    ->label(__('labels.task.service_component'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('code')
                    ->label(__('labels.code'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('cost')
                    ->label(__('labels.task.cost'))
                    ->formatStateUsing(fn (string $state): string => format_money($state))
                    ->sortable(),
                TextColumn::make('governmentDepartment.name')
                    ->label(__('labels.task.government_department'))
                    ->placeholder('-')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('labels.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_component_id')
                    ->label(__('labels.task.service_component'))
                    ->options(fn () => ServiceComponent::query()->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('government_department_id')
                    ->label(__('labels.task.government_department'))
                    ->options(fn () => GovernmentDepartment::query()->orderBy('name')->pluck('name', 'id')->all()),
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
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
