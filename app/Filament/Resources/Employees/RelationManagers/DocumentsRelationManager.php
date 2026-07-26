<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Models\Document;
use App\Models\EmployeeDocument;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'employeeDocuments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('labels.nav.documents');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    Select::make('document_id')
                        ->label(__('labels.document_type'))
                        ->options(fn () => Document::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('document_number')
                        ->label(__('labels.document_number'))
                        ->maxLength(100),
                    DatePicker::make('issue_date')
                        ->label(__('labels.issue_date'))
                        ->native(false),
                    DatePicker::make('expiry_date')
                        ->label(__('labels.expiry_date'))
                        ->native(false),
                    FileUpload::make('file_path')
                        ->label(__('labels.attachment'))
                        ->directory('employee-documents')
                        ->columnSpanFull(),
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
            ->recordTitleAttribute('document_number')
            ->columns([
                TextColumn::make('document.name')
                    ->label(__('labels.document_type'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('document_number')
                    ->label(__('labels.document_number'))
                    ->placeholder('-'),
                TextColumn::make('issue_date')
                    ->label(__('labels.issue_date'))
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(__('labels.expiry_date'))
                    ->date()
                    ->placeholder('-')
                    ->sortable()
                    ->color(fn (EmployeeDocument $record): ?string => $record->isExpired() ? 'danger' : null),
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
