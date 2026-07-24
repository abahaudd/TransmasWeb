<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\Phone;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PhonesRelationManager extends RelationManager
{
    protected static string $relationship = 'phones';

    protected static ?string $title = 'Phones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    Select::make('phone_type')
                        ->label('Type')
                        ->options(Phone::typeOptions())
                        ->required()
                        ->native(false),
                    TextInput::make('contact_name')
                        ->maxLength(100),
                    TextInput::make('country_code')
                        ->maxLength(10)
                        ->placeholder('+971'),
                    TextInput::make('phone_number')
                        ->required()
                        ->maxLength(30)
                        ->tel(),
                    TextInput::make('extension')
                        ->maxLength(10),
                    Toggle::make('is_primary')
                        ->label('Primary')
                        ->default(false),
                ]),
            TextInput::make('remarks')
                ->maxLength(255)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('phone_number')
            ->columns([
                TextColumn::make('phone_type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('country_code')
                    ->label('Country Code'),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('extension')
                    ->placeholder('-'),
                TextColumn::make('contact_name')
                    ->placeholder('-'),
                IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean(),
                TextColumn::make('remarks')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('phone_type')
                    ->label('Type')
                    ->options(Phone::typeOptions()),
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
            ]);
    }
}
