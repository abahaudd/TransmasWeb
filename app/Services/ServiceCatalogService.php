<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceWorkflowStep;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

/**
 * Programmatic API for the service catalog: service categories, services,
 * tasks/service components, and each service's configurable, ordered workflow.
 */
class ServiceCatalogService
{
    protected const RELATIONS = [
        'category',
        'workflowSteps.step',
        'serviceDocuments.document',
    ];

    public function getAllServices(): Collection
    {
        return Service::with(self::RELATIONS)->orderBy('name')->get();
    }

    public function getActiveServices(): Collection
    {
        return Service::with(self::RELATIONS)
            ->where('status', Service::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();
    }

    public function getServiceById(int $id): ?Service
    {
        return Service::with(self::RELATIONS)->find($id);
    }

    /**
     * Create a Service together with its ordered workflow steps and required documents.
     *
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>> $workflowSteps Each item: ['id' => ?int, 'step_type' => 'task'|'service_component', 'step_id' => int]
     * @param array<int, array<string, mixed>> $serviceDocuments Each item: ['id' => ?int, 'document_id' => int, 'is_mandatory' => bool, 'remarks' => ?string]
     */
    public function createService(array $data, array $workflowSteps = [], array $serviceDocuments = []): Service
    {
        return DB::transaction(function () use ($data, $workflowSteps, $serviceDocuments): Service {
            $service = Service::create(Arr::except($data, ['workflow_steps', 'service_documents']));

            $this->syncWorkflowSteps($service, $workflowSteps);
            $this->syncServiceDocuments($service, $serviceDocuments);

            return $service->fresh(self::RELATIONS);
        });
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>>|null $workflowSteps Omit (null) to leave the workflow untouched.
     * @param array<int, array<string, mixed>>|null $serviceDocuments Omit (null) to leave the required documents untouched.
     */
    public function updateService(Service $service, array $data, ?array $workflowSteps = null, ?array $serviceDocuments = null): Service
    {
        return DB::transaction(function () use ($service, $data, $workflowSteps, $serviceDocuments): Service {
            $service->update(Arr::except($data, ['workflow_steps', 'service_documents']));

            if ($workflowSteps !== null) {
                $this->syncWorkflowSteps($service, $workflowSteps);
            }

            if ($serviceDocuments !== null) {
                $this->syncServiceDocuments($service, $serviceDocuments);
            }

            return $service->fresh(self::RELATIONS);
        });
    }

    public function deleteService(Service $service): bool
    {
        return (bool) $service->delete();
    }

    /**
     * Replace a service's workflow to match the submitted, ordered list.
     * Existing steps are matched by 'id' (update type/target/sequence);
     * anything no longer present is removed. Array order becomes sequence.
     *
     * @param array<int, array<string, mixed>> $steps
     */
    public function syncWorkflowSteps(Service $service, array $steps): void
    {
        $keptIds = [];

        foreach (array_values($steps) as $index => $stepData) {
            $id = $stepData['id'] ?? null;
            $payload = [
                'step_type' => $stepData['step_type'],
                'step_id' => (int) $stepData['step_id'],
                'sequence' => $index,
            ];

            $existing = $id ? $service->workflowSteps()->whereKey($id)->first() : null;

            if ($existing) {
                $existing->update($payload);
                $keptIds[] = $existing->getKey();

                continue;
            }

            $keptIds[] = $service->workflowSteps()->create($payload)->getKey();
        }

        $service->workflowSteps()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * Replace a service's required-documents list to match the submitted
     * set. Existing rows are matched by 'id' (update mandatory flag/remarks);
     * anything no longer present is removed.
     *
     * @param array<int, array<string, mixed>> $documents
     */
    public function syncServiceDocuments(Service $service, array $documents): void
    {
        $keptIds = [];

        foreach (array_values($documents) as $index => $documentData) {
            $id = $documentData['id'] ?? null;
            $payload = [
                'document_id' => (int) $documentData['document_id'],
                'is_mandatory' => (bool) ($documentData['is_mandatory'] ?? true),
                'remarks' => $documentData['remarks'] ?? null,
                'sequence' => $index,
            ];

            $existing = $id ? $service->serviceDocuments()->whereKey($id)->first() : null;

            if ($existing) {
                $existing->update($payload);
                $keptIds[] = $existing->getKey();

                continue;
            }

            $keptIds[] = $service->serviceDocuments()->create($payload)->getKey();
        }

        $service->serviceDocuments()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * The ordered, expanded task execution plan for a service (service-component
     * steps expanded into their member tasks).
     */
    public function resolveWorkflow(Service $service): SupportCollection
    {
        return $service->resolvedTasks();
    }

    /**
     * Sum of the resolved workflow's task costs, for comparison against the
     * service's stored `cost` attribute.
     */
    public function calculateWorkflowCost(Service $service): float
    {
        return (float) $service->workflowCost();
    }

    /**
     * @return array<string, string>
     */
    public function workflowStepTypeOptions(): array
    {
        return ServiceWorkflowStep::typeOptions();
    }
}
