<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\Departments\Pages\EditDepartment;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Models\Company;
use App\Models\Department;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'HR Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label('Company')
                ->helperText('Leave empty to make this department available to every company.')
                ->options(fn () => Company::query()
                    ->orderBy('legal_name')
                    ->get()
                    ->mapWithKeys(fn (Company $company): array => [$company->id => $company->displayLabel()])
                    ->all())
                ->searchable()
                ->preload(),
            TextInput::make('name')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->placeholder('-'),
                TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->placeholder('All companies'),
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
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}
