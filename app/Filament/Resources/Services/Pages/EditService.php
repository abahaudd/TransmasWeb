<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use App\Models\ServiceDocument;
use App\Models\ServiceWorkflowStep;
use App\Services\ServiceCatalogService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->icon(Heroicon::OutlinedEye),
            DeleteAction::make()->icon(Heroicon::OutlinedTrash),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Service $service */
        $service = $this->record;

        $data['workflow_steps'] = $service->workflowSteps()
            ->get()
            ->map(fn (ServiceWorkflowStep $step): array => [
                'id' => $step->id,
                'step_type' => $step->step_type,
                'step_id' => $step->step_id,
            ])
            ->all();

        $data['service_documents'] = $service->serviceDocuments()
            ->get()
            ->map(fn (ServiceDocument $document): array => [
                'id' => $document->id,
                'document_id' => $document->document_id,
                'is_mandatory' => $document->is_mandatory,
                'remarks' => $document->remarks,
            ])
            ->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Service $record */
        return app(ServiceCatalogService::class)->updateService(
            $record,
            $data,
            $data['workflow_steps'] ?? [],
            $data['service_documents'] ?? [],
        );
    }
}
