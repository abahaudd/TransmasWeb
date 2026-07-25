<?php

namespace App\Filament\Resources\EmploymentStatuses;

use App\Filament\Resources\EmploymentStatuses\Pages\CreateEmploymentStatus;
use App\Filament\Resources\EmploymentStatuses\Pages\EditEmploymentStatus;
use App\Filament\Resources\EmploymentStatuses\Pages\ListEmploymentStatuses;
use App\Models\EmploymentStatus;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmploymentStatusResource extends Resource
{
    protected static ?string $model = EmploymentStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?int $navigationSort = 13;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.hr_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.employment_statuses');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('labels.name'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),
            Toggle::make('is_terminal')
                ->label(__('labels.employment_status.ends_employment'))
                ->helperText(__('labels.employment_status.ends_employment_helper'))
                ->default(false),
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
                IconColumn::make('is_terminal')
                    ->label(__('labels.employment_status.ends_employment'))
                    ->boolean()
                    ->sortable(),
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
            'index' => ListEmploymentStatuses::route('/'),
            'create' => CreateEmploymentStatus::route('/create'),
            'edit' => EditEmploymentStatus::route('/{record}/edit'),
        ];
    }
}
