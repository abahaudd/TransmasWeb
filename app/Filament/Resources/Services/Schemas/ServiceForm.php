<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceComponent;
use App\Models\ServiceWorkflowStep;
use App\Models\Task;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.service.section_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('labels.name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                                Select::make('service_category_id')
                                    ->label(__('labels.service.category'))
                                    ->options(fn () => ServiceCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('code')
                                    ->label(__('labels.code'))
                                    ->maxLength(30),
                                Select::make('status')
                                    ->label(__('labels.status'))
                                    ->options(Service::statusOptions())
                                    ->default(Service::STATUS_ACTIVE)
                                    ->required()
                                    ->native(false),
                                TextInput::make('cost')
                                    ->label(__('labels.service.cost'))
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AED')
                                    ->required(),
                                TextInput::make('price')
                                    ->label(__('labels.service.price'))
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AED')
                                    ->required(),
                            ]),
                        Textarea::make('description')
                            ->label(__('labels.service.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('labels.service.section_workflow'))
                    ->description(__('labels.service.workflow_description'))
                    ->schema([
                        Repeater::make('workflow_steps')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('step_type')
                                            ->label(__('labels.service.step_type'))
                                            ->options(ServiceWorkflowStep::typeOptions())
                                            ->required()
                                            ->live()
                                            ->native(false),
                                        Select::make('step_id')
                                            ->label(__('labels.service.step'))
                                            ->options(function (Get $get): array {
                                                return match ($get('step_type')) {
                                                    ServiceWorkflowStep::TYPE_TASK => Task::query()->orderBy('name')->pluck('name', 'id')->all(),
                                                    ServiceWorkflowStep::TYPE_SERVICE_COMPONENT => ServiceComponent::query()->orderBy('name')->pluck('name', 'id')->all(),
                                                    default => [],
                                                };
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                $type = $state['step_type'] ?? null;
                                $id = $state['step_id'] ?? null;

                                if (blank($type) || blank($id)) {
                                    return null;
                                }

                                $model = $type === ServiceWorkflowStep::TYPE_TASK
                                    ? Task::find($id)
                                    : ServiceComponent::find($id);

                                if (! $model) {
                                    return null;
                                }

                                $typeLabel = ServiceWorkflowStep::typeOptions()[$type] ?? $type;

                                return "{$typeLabel}: {$model->name}";
                            })
                            ->addActionLabel(__('labels.service.add_step'))
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
