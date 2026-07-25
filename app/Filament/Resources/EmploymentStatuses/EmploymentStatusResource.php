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
        return 'HR Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),
            Toggle::make('is_terminal')
                ->label('Ends employment')
                ->helperText('e.g. Resigned, Terminated, Retired, Deceased — excluded from active headcount.')
                ->default(false),
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
                IconColumn::make('is_terminal')
                    ->label('Ends employment')
                    ->boolean()
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
            'index' => ListEmploymentStatuses::route('/'),
            'create' => CreateEmploymentStatus::route('/create'),
            'edit' => EditEmploymentStatus::route('/{record}/edit'),
        ];
    }
}
