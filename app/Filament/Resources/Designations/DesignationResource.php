<?php

namespace App\Filament\Resources\Designations;

use App\Filament\Resources\Designations\Pages\CreateDesignation;
use App\Filament\Resources\Designations\Pages\EditDesignation;
use App\Filament\Resources\Designations\Pages\ListDesignations;
use App\Models\Department;
use App\Models\Designation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DesignationResource extends Resource
{
    protected static ?string $model = Designation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return 'HR Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('department_id')
                ->label('Department')
                ->helperText('Leave empty to make this designation available across departments.')
                ->options(fn () => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->preload(),
            TextInput::make('title')
                ->required()
                ->maxLength(150),
            TextInput::make('code')
                ->maxLength(20),
            Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->placeholder('-'),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('All departments'),
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
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDesignations::route('/'),
            'create' => CreateDesignation::route('/create'),
            'edit' => EditDesignation::route('/{record}/edit'),
        ];
    }
}
