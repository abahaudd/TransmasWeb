<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\Country;
use App\Models\GovernmentRegistration;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GovernmentRegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'governmentRegistrations';

    protected static ?string $title = 'Government Registrations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    TextInput::make('registration_type')
                        ->label('Registration Type')
                        ->required()
                        ->maxLength(50)
                        ->datalist(GovernmentRegistration::COMMON_TYPES)
                        ->helperText('Pick a common type or type your own — no schema change needed.'),
                    TextInput::make('registration_number')
                        ->required()
                        ->maxLength(100),
                    Select::make('country_id')
                        ->label('Country')
                        ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload(),
                    TextInput::make('issuing_authority')
                        ->maxLength(200),
                    DatePicker::make('issue_date')
                        ->native(false),
                    DatePicker::make('expiry_date')
                        ->native(false),
                    Select::make('status')
                        ->options(GovernmentRegistration::statusOptions())
                        ->default(GovernmentRegistration::STATUS_ACTIVE)
                        ->required()
                        ->native(false),
                    FileUpload::make('document_path')
                        ->label('Document')
                        ->directory('companies/government-registrations'),
                ]),
            Textarea::make('remarks')
                ->columnSpanFull()
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('registration_number')
            ->columns([
                TextColumn::make('registration_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->label('Number')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->placeholder('-'),
                TextColumn::make('issuing_authority')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issue_date')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->placeholder('-')
                    ->sortable()
                    ->color(fn (GovernmentRegistration $record): ?string => $record->isExpired() ? 'danger' : null),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GovernmentRegistration::STATUS_ACTIVE => 'success',
                        GovernmentRegistration::STATUS_EXPIRED, GovernmentRegistration::STATUS_CANCELLED => 'danger',
                        GovernmentRegistration::STATUS_PENDING => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GovernmentRegistration::statusOptions()),
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
