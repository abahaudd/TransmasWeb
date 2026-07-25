<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use App\Models\ServiceWorkflowStep;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.service.section_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('labels.name')),
                                TextEntry::make('category.name')
                                    ->label(__('labels.service.category'))
                                    ->placeholder('-'),
                                TextEntry::make('code')
                                    ->label(__('labels.code'))
                                    ->placeholder('-'),
                                TextEntry::make('status')
                                    ->label(__('labels.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => $state === Service::STATUS_ACTIVE ? 'success' : 'gray'),
                                TextEntry::make('cost')
                                    ->label(__('labels.service.cost'))
                                    ->formatStateUsing(fn (string $state): string => format_money($state)),
                                TextEntry::make('price')
                                    ->label(__('labels.service.price'))
                                    ->formatStateUsing(fn (string $state): string => format_money($state)),
                                TextEntry::make('workflow_cost_estimate')
                                    ->label(__('labels.service.workflow_cost_estimate'))
                                    ->helperText(__('labels.service.workflow_cost_estimate_helper'))
                                    ->state(fn (Service $record): string => format_money($record->workflowCost())),
                            ]),
                        TextEntry::make('description')
                            ->label(__('labels.service.description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('labels.service.section_workflow'))
                    ->schema([
                        RepeatableEntry::make('workflowSteps')
                            ->hiddenLabel()
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('step.name')
                                            ->hiddenLabel()
                                            ->icon(fn (ServiceWorkflowStep $record) => match ($record->step_type) {
                                                ServiceWorkflowStep::TYPE_TASK => Heroicon::OutlinedListBullet,
                                                ServiceWorkflowStep::TYPE_SERVICE_COMPONENT => Heroicon::OutlinedRectangleStack,
                                                default => null,
                                            })
                                            ->iconColor('primary')
                                            ->weight(FontWeight::Medium)
                                            ->html()
                                            ->formatStateUsing(function (ServiceWorkflowStep $record, string $state): string {
                                                $department = $record->step_type === ServiceWorkflowStep::TYPE_TASK
                                                    ? $record->step?->governmentDepartment?->name
                                                    : null;

                                                $html = e($state);

                                                if (filled($department)) {
                                                    $html .= '<span class="block text-xs font-normal text-gray-400">' . e($department) . '</span>';
                                                }

                                                return $html;
                                            })
                                            ->columnSpan(3),
                                        TextEntry::make('cost')
                                            ->hiddenLabel()
                                            ->state(fn (ServiceWorkflowStep $record): string => match ($record->step_type) {
                                                ServiceWorkflowStep::TYPE_TASK => (string) ($record->step?->cost ?? 0),
                                                ServiceWorkflowStep::TYPE_SERVICE_COMPONENT => $record->step?->totalCost() ?? '0',
                                                default => '0',
                                            })
                                            ->formatStateUsing(fn (string $state): string => format_money($state))
                                            ->alignEnd()
                                            ->columnSpan(1),
                                    ]),
                                Section::make()
                                    ->schema([
                                        RepeatableEntry::make('step.tasks')
                                            ->hiddenLabel()
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->hiddenLabel()
                                                            ->icon(Heroicon::OutlinedListBullet)
                                                            ->iconColor('gray')
                                                            ->columnSpan(3),
                                                        TextEntry::make('cost')
                                                            ->hiddenLabel()
                                                            ->formatStateUsing(fn (string $state): string => format_money($state))
                                                            ->alignEnd()
                                                            ->columnSpan(1),
                                                    ]),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed()
                                    ->compact()
                                    ->visible(fn (ServiceWorkflowStep $record): bool => $record->step_type === ServiceWorkflowStep::TYPE_SERVICE_COMPONENT),
                            ]),
                    ])
                    ->visible(fn (Service $record): bool => $record->workflowSteps->isNotEmpty()),
            ]);
    }
}
