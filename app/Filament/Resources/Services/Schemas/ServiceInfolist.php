<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use App\Models\ServiceWorkflowStep;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->label('')
                            ->schema([
                                TextEntry::make('step_type')
                                    ->label(__('labels.service.step_type'))
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => ServiceWorkflowStep::typeOptions()[$state] ?? $state),
                                TextEntry::make('step.name')
                                    ->label(__('labels.service.step')),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn (Service $record): bool => $record->workflowSteps->isNotEmpty()),
            ]);
    }
}
