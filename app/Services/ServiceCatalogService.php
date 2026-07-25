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
     * Create a Service together with its ordered workflow steps.
     *
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>> $workflowSteps Each item: ['id' => ?int, 'step_type' => 'task'|'service_component', 'step_id' => int]
     */
    public function createService(array $data, array $workflowSteps = []): Service
    {
        return DB::transaction(function () use ($data, $workflowSteps): Service {
            $service = Service::create(Arr::except($data, ['workflow_steps']));

            $this->syncWorkflowSteps($service, $workflowSteps);

            return $service->fresh(self::RELATIONS);
        });
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, array<string, mixed>>|null $workflowSteps Omit (null) to leave the workflow untouched.
     */
    public function updateService(Service $service, array $data, ?array $workflowSteps = null): Service
    {
        return DB::transaction(function () use ($service, $data, $workflowSteps): Service {
            $service->update(Arr::except($data, ['workflow_steps']));

            if ($workflowSteps !== null) {
                $this->syncWorkflowSteps($service, $workflowSteps);
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
