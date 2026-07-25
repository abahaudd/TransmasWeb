<?php

namespace App\Filament\Resources\EmploymentTypes;

use App\Filament\Resources\EmploymentTypes\Pages\CreateEmploymentType;
use App\Filament\Resources\EmploymentTypes\Pages\EditEmploymentType;
use App\Filament\Resources\EmploymentTypes\Pages\ListEmploymentTypes;
use App\Models\EmploymentType;
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

class EmploymentTypeResource extends Resource
{
    protected static ?string $model = EmploymentType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'HR Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),
            Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
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
            'index' => ListEmploymentTypes::route('/'),
            'create' => CreateEmploymentType::route('/create'),
            'edit' => EditEmploymentType::route('/{record}/edit'),
        ];
    }
}
