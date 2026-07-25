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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('labels.service.section_details'))
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('labels.name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?Service $record): void {
                                        if ($record) {
                                            return;
                                        }

                                        if ($suggestion = self::suggestCode($get('service_category_id'), $state)) {
                                            $set('code', $suggestion);
                                        }
                                    }),
                                Select::make('service_category_id')
                                    ->label(__('labels.service.category'))
                                    ->options(fn () => ServiceCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?Service $record): void {
                                        if ($record) {
                                            return;
                                        }

                                        if ($suggestion = self::suggestCode($state, $get('name'))) {
                                            $set('code', $suggestion);
                                        }
                                    }),
                                TextInput::make('code')
                                    ->label(__('labels.code'))
                                    ->helperText(__('labels.service.code_helper'))
                                    ->maxLength(30),
                                Select::make('status')
                                    ->label(__('labels.status'))
                                    ->options(Service::statusOptions())
                                    ->default(Service::STATUS_ACTIVE)
                                    ->required()
                                    ->native(false),
                                TextInput::make('cost')
                                    ->label(__('labels.service.cost'))
                                    ->helperText(__('labels.service.cost_helper'))
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AED'),
                                TextInput::make('price')
                                    ->label(__('labels.service.price'))
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('AED'),
                            ]),
                        Textarea::make('description')
                            ->label(__('labels.service.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('labels.service.section_workflow'))
                    ->description(__('labels.service.workflow_description'))
                    ->collapsible()
                    ->schema([
                        Repeater::make('workflow_steps')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                Hidden::make('step_type'),
                                Select::make('activity')
                                    ->label(__('labels.service.step'))
                                    ->options(fn () => self::activityOptions())
                                    ->allowHtml()
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateHydrated(function (Select $component, Get $get): void {
                                        $type = $get('step_type');
                                        $id = $get('step_id');

                                        if (filled($type) && filled($id)) {
                                            $component->state("{$type}:{$id}");
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        [$type, $id] = array_pad(explode(':', (string) $state, 2), 2, null);

                                        $set('step_type', $type);
                                        $set('step_id', filled($id) ? (int) $id : null);
                                    })
                                    ->dehydrated(false)
                                    ->createOptionForm([
                                        Select::make('quick_type')
                                            ->label(__('labels.service.quick_add_type'))
                                            ->options(ServiceWorkflowStep::typeOptions())
                                            ->default(ServiceWorkflowStep::TYPE_TASK)
                                            ->required()
                                            ->live()
                                            ->native(false),
                                        TextInput::make('name')
                                            ->label(__('labels.name'))
                                            ->required()
                                            ->maxLength(200),
                                        TextInput::make('code')
                                            ->label(__('labels.code'))
                                            ->maxLength(20),
                                        TextInput::make('cost')
                                            ->label(__('labels.task.cost'))
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('AED')
                                            ->visible(fn (Get $get): bool => $get('quick_type') === ServiceWorkflowStep::TYPE_TASK),
                                        Textarea::make('description')
                                            ->label(__('labels.service.quick_add_description'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->createOptionUsing(function (array $data): string {
                                        if (($data['quick_type'] ?? null) === ServiceWorkflowStep::TYPE_TASK) {
                                            $task = Task::create([
                                                'name' => $data['name'],
                                                'code' => $data['code'] ?: null,
                                                'cost' => $data['cost'] ?? 0,
                                                'description' => $data['description'] ?: null,
                                                'is_active' => true,
                                            ]);

                                            return ServiceWorkflowStep::TYPE_TASK . ':' . $task->id;
                                        }

                                        $component = ServiceComponent::create([
                                            'name' => $data['name'],
                                            'code' => $data['code'] ?: null,
                                            'description' => $data['description'] ?: null,
                                            'is_active' => true,
                                        ]);

                                        return ServiceWorkflowStep::TYPE_SERVICE_COMPONENT . ':' . $component->id;
                                    })
                                    ->createOptionModalHeading(__('labels.service.quick_add_heading'))
                                    ->columnSpanFull(),
                                Hidden::make('step_id'),
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
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                $set('cost', self::calculateWorkflowCost($state ?? []));
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Suggest a service code from the selected category's code plus the
     * first word of the service name, e.g. category "IMM" + name "Entry
     * Permits" => "IMM-ENTRY". Editable afterwards — just a starting point.
     */
    private static function suggestCode(int|string|null $categoryId, ?string $name): ?string
    {
        $categoryCode = filled($categoryId) ? ServiceCategory::find($categoryId)?->code : null;

        $firstWord = filled($name) ? Str::of($name)->trim()->explode(' ')->first() : null;
        $suffix = filled($firstWord) ? Str::upper(Str::slug($firstWord, '')) : null;

        $parts = array_filter([$categoryCode, $suffix], fn (?string $part): bool => filled($part));

        return $parts === [] ? null : implode('-', $parts);
    }

    /**
     * A single searchable list combining tasks and service components, each
     * prefixed with an icon so the two kinds of workflow step stay visually
     * distinguishable while sharing one search field.
     *
     * @return array<string, string>
     */
    private static function activityOptions(): array
    {
        $taskIcon = Blade::render(sprintf(
            '<x-filament::icon icon="%s" class="w-4 h-4 inline-block align-text-bottom mr-1 text-gray-400" />',
            Heroicon::OutlinedListBullet->getIconForSize(IconSize::Small),
        ));

        $componentIcon = Blade::render(sprintf(
            '<x-filament::icon icon="%s" class="w-4 h-4 inline-block align-text-bottom mr-1 text-gray-400" />',
            Heroicon::OutlinedRectangleStack->getIconForSize(IconSize::Small),
        ));

        $options = [];

        foreach (Task::query()->orderBy('name')->get(['id', 'name']) as $task) {
            $options[ServiceWorkflowStep::TYPE_TASK . ':' . $task->id] = $taskIcon . e($task->name);
        }

        foreach (ServiceComponent::query()->orderBy('name')->get(['id', 'name']) as $component) {
            $options[ServiceWorkflowStep::TYPE_SERVICE_COMPONENT . ':' . $component->id] = $componentIcon . e($component->name);
        }

        return $options;
    }

    /**
     * Sum the cost of every selected task / service component across the
     * workflow repeater's current (in-memory, possibly unsaved) state.
     *
     * @param  array<int, array{step_type?: string|null, step_id?: int|string|null}>  $items
     */
    private static function calculateWorkflowCost(array $items): string
    {
        $total = 0.0;

        foreach ($items as $item) {
            $type = $item['step_type'] ?? null;
            $id = $item['step_id'] ?? null;

            if (blank($type) || blank($id)) {
                continue;
            }

            $total += match ($type) {
                ServiceWorkflowStep::TYPE_TASK => (float) (Task::find($id)?->cost ?? 0),
                ServiceWorkflowStep::TYPE_SERVICE_COMPONENT => (float) (ServiceComponent::find($id)?->totalCost() ?? 0),
                default => 0.0,
            };
        }

        return (string) $total;
    }
}
