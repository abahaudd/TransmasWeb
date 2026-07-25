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
use Illuminate\Database\Eloquent\Model;

class GovernmentRegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'governmentRegistrations';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('labels.government_registration.section_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    TextInput::make('registration_type')
                        ->label(__('labels.government_registration.registration_type'))
                        ->required()
                        ->maxLength(50)
                        ->datalist(GovernmentRegistration::COMMON_TYPES)
                        ->helperText(__('labels.government_registration.registration_type_helper')),
                    TextInput::make('registration_number')
                        ->label(__('labels.government_registration.registration_number'))
                        ->required()
                        ->maxLength(100),
                    Select::make('country_id')
                        ->label(__('labels.government_registration.country'))
                        ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload(),
                    TextInput::make('issuing_authority')
                        ->label(__('labels.government_registration.issuing_authority'))
                        ->maxLength(200),
                    DatePicker::make('issue_date')
                        ->label(__('labels.government_registration.issue_date'))
                        ->native(false),
                    DatePicker::make('expiry_date')
                        ->label(__('labels.government_registration.expiry_date'))
                        ->native(false),
                    Select::make('status')
                        ->label(__('labels.status'))
                        ->options(GovernmentRegistration::statusOptions())
                        ->default(GovernmentRegistration::STATUS_ACTIVE)
                        ->required()
                        ->native(false),
                    FileUpload::make('document_path')
                        ->label(__('labels.government_registration.document'))
                        ->directory('companies/government-registrations'),
                ]),
            Textarea::make('remarks')
                ->label(__('labels.remarks'))
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
                    ->label(__('labels.government_registration.registration_type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->label(__('labels.government_registration.registration_number'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('labels.government_registration.country'))
                    ->placeholder('-'),
                TextColumn::make('issuing_authority')
                    ->label(__('labels.government_registration.issuing_authority'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issue_date')
                    ->label(__('labels.government_registration.issue_date'))
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(__('labels.government_registration.expiry_date'))
                    ->date()
                    ->placeholder('-')
                    ->sortable()
                    ->color(fn (GovernmentRegistration $record): ?string => $record->isExpired() ? 'danger' : null),
                TextColumn::make('status')
                    ->label(__('labels.status'))
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
                    ->label(__('labels.status'))
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
