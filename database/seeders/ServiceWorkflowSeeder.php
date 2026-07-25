<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceComponent;
use App\Models\ServiceWorkflowStep;
use App\Models\Task;
use App\Services\ServiceCatalogService;
use Illuminate\Database\Seeder;

/**
 * Attach the appropriate service components and/or standalone tasks to each
 * of the seeded services' workflow, so their resolved task lists and
 * workflow cost estimates are populated out of the box.
 */
class ServiceWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = app(ServiceCatalogService::class);

        foreach ($this->workflowMap() as $serviceCode => $steps) {
            $service = Service::where('code', $serviceCode)->first();

            if (! $service) {
                continue;
            }

            $resolvedSteps = collect($steps)
                ->map(fn (array $step): ?array => $this->resolveStep($step))
                ->filter()
                ->values()
                ->all();

            if ($resolvedSteps === []) {
                continue;
            }

            $catalog->syncWorkflowSteps($service, $resolvedSteps);
        }
    }

    /**
     * @param  array{type: string, code: string}  $step
     */
    private function resolveStep(array $step): ?array
    {
        if ($step['type'] === ServiceWorkflowStep::TYPE_SERVICE_COMPONENT) {
            $component = ServiceComponent::where('code', $step['code'])->first();

            return $component ? [
                'step_type' => ServiceWorkflowStep::TYPE_SERVICE_COMPONENT,
                'step_id' => $component->id,
            ] : null;
        }

        $task = Task::where('code', $step['code'])->first();

        return $task ? [
            'step_type' => ServiceWorkflowStep::TYPE_TASK,
            'step_id' => $task->id,
        ] : null;
    }

    /**
     * @return array<string, array<int, array{type: string, code: string}>>
     */
    private function workflowMap(): array
    {
        $component = fn (string $code): array => ['type' => ServiceWorkflowStep::TYPE_SERVICE_COMPONENT, 'code' => $code];
        $task = fn (string $code): array => ['type' => ServiceWorkflowStep::TYPE_TASK, 'code' => $code];

        return [
            'IMM-ENTRY' => [
                $component('TG-ENTRY'),
            ],
            'IMM-RES' => [
                $component('TG-ENTRY'),
                $component('TG-MEDICAL'),
                $component('TG-EID'),
                $component('TG-STAMP'),
            ],
            'IMM-FAM' => [
                $task('T-FAM-01'),
                $component('TG-MEDICAL'),
                $component('TG-EID'),
                $task('T-FAM-02'),
                $task('T-FAM-03'),
                $component('TG-STAMP'),
            ],
            'IMM-LTV' => [
                $component('TG-GOLDEN'),
                $component('TG-EID'),
            ],
            'EMP-WORK' => [
                $component('TG-QUOTA'),
            ],
            'EMP-CONTRACT' => [
                $component('TG-CONTRACT'),
            ],
            'EMP-PROFILE' => [
                $component('TG-ESTAB'),
            ],
            'LEG-ID' => [
                $component('TG-EID'),
            ],
            'LEG-MED' => [
                $component('TG-MEDICAL'),
            ],
            'LEG-DRAFT' => [
                $component('TG-ATTEST'),
            ],
            'BIZ-LIC' => [
                $component('TG-TRADE'),
            ],
            'BIZ-TRADE' => [
                $task('T-TRADE-01'),
            ],
            'BIZ-TENANCY' => [
                $component('TG-TENANCY'),
            ],
        ];
    }
}
