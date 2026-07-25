<?php

namespace Database\Seeders;

use App\Models\GovernmentDepartment;
use App\Models\ServiceComponent;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskCatalogSeeder extends Seeder
{
    /**
     * Seed real UAE government departments plus a starter set of reusable
     * service components/tasks (with researched indicative government fees
     * as `cost`) for the service catalog's workflow builder.
     *
     * Costs are the published/typical *government* fee for that single
     * step as of the source's last update — real-world typing/service
     * centre commissions vary and are not included; treat these as a
     * starting point to review, not a locked price list.
     */
    public function run(): void
    {
        $departments = $this->seedDepartments();
        $this->seedServiceComponents($departments);
        $this->seedStandaloneTasks($departments);
    }

    /**
     * @return array<string, GovernmentDepartment>
     */
    protected function seedDepartments(): array
    {
        $rows = [
            'ICP' => [
                'name' => 'Federal Authority for Identity, Citizenship, Customs & Port Security (ICP)',
                'remarks' => 'Federal authority for Emirates ID, entry permits, residency, and Golden/Green Visas.',
            ],
            'GDRFA' => [
                'name' => 'General Directorate of Residency and Foreigners Affairs (GDRFA) – Dubai',
                'remarks' => 'Processes entry permits, residence visa stamping, and immigration company cards in Dubai.',
            ],
            'MOHRE' => [
                'name' => 'Ministry of Human Resources and Emiratisation (MOHRE)',
                'remarks' => 'Labour quotas, work permits, employment contracts, and establishment cards (Tas\'heel).',
            ],
            'MOFA' => [
                'name' => 'Ministry of Foreign Affairs & International Cooperation (MOFA)',
                'remarks' => 'Attestation of personal and commercial documents (POAs, degrees, board resolutions).',
            ],
            'DHA' => [
                'name' => 'Dubai Health Authority (DHA)',
                'remarks' => 'Mandatory medical fitness testing for residency applications in Dubai.',
            ],
            'DET' => [
                'name' => 'Department of Economy and Tourism (DET) – Dubai',
                'remarks' => 'Trade name reservation, initial approval, and trade licence issuance (formerly DED).',
            ],
            'DLD' => [
                'name' => 'Dubai Land Department (DLD) – Ejari',
                'remarks' => 'Tenancy contract registration in Dubai.',
            ],
            'DMT' => [
                'name' => 'Department of Municipalities and Transport (DMT) – Tawtheeq, Abu Dhabi',
                'remarks' => 'Tenancy contract registration in Abu Dhabi.',
            ],
        ];

        $departments = [];

        foreach ($rows as $code => $data) {
            $departments[$code] = GovernmentDepartment::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'remarks' => $data['remarks'],
                    'is_active' => true,
                ]
            );
        }

        return $departments;
    }

    /**
     * @param array<string, GovernmentDepartment> $departments
     */
    protected function seedServiceComponents(array $departments): void
    {
        foreach ($this->serviceComponentCatalog($departments) as $componentData) {
            $component = ServiceComponent::firstOrCreate(
                ['code' => $componentData['code']],
                [
                    'name' => $componentData['name'],
                    'description' => $componentData['description'],
                    'is_active' => true,
                ]
            );

            foreach ($componentData['tasks'] as $sequence => $taskData) {
                Task::firstOrCreate(
                    ['code' => $taskData['code']],
                    [
                        'service_component_id' => $component->id,
                        'name' => $taskData['name'],
                        'description' => $taskData['description'],
                        'cost' => $taskData['cost'],
                        'government_department_id' => $taskData['department']?->id,
                        'sequence' => $sequence + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * @param array<string, GovernmentDepartment> $departments
     * @return array<int, array{code: string, name: string, description: string, tasks: array<int, array{code: string, name: string, description: string, cost: float, department: ?GovernmentDepartment}>}>
     */
    protected function serviceComponentCatalog(array $departments): array
    {
        return [
            [
                'code' => 'TG-ENTRY',
                'name' => 'Entry Permit Processing',
                'description' => 'Initial entry permit application and issuance, ahead of arrival/status change.',
                'tasks' => [
                    [
                        'code' => 'T-ENTRY-01',
                        'name' => 'Submit Entry Permit Application',
                        'description' => 'File the entry permit request via the GDRFA/ICP portal or an authorised typing centre.',
                        'cost' => 100,
                        'department' => $departments['GDRFA'],
                    ],
                    [
                        'code' => 'T-ENTRY-02',
                        'name' => 'Pay Entry Permit Issuance Fee',
                        'description' => 'Government issuance fee once the entry permit is approved (varies by permit type).',
                        'cost' => 200,
                        'department' => $departments['GDRFA'],
                    ],
                ],
            ],
            [
                'code' => 'TG-MEDICAL',
                'name' => 'Medical Fitness Test',
                'description' => 'Mandatory health screening required before residence visa stamping.',
                'tasks' => [
                    [
                        'code' => 'T-MED-01',
                        'name' => 'Book Medical Fitness Appointment',
                        'description' => 'Schedule the screening at a DHA/MOH-approved medical fitness centre.',
                        'cost' => 0,
                        'department' => $departments['DHA'],
                    ],
                    [
                        'code' => 'T-MED-02',
                        'name' => 'Attend Medical Screening & Blood Test',
                        'description' => 'Standard fitness test (blood test + chest X-ray) at the medical centre.',
                        'cost' => 320,
                        'department' => $departments['DHA'],
                    ],
                ],
            ],
            [
                'code' => 'TG-EID',
                'name' => 'Emirates ID Registration',
                'description' => 'Biometric capture and issuance of the mandatory Emirates ID card.',
                'tasks' => [
                    [
                        'code' => 'T-EID-01',
                        'name' => 'Submit Emirates ID Application',
                        'description' => 'File the application online or via an ICP-approved typing centre.',
                        'cost' => 100,
                        'department' => $departments['ICP'],
                    ],
                    [
                        'code' => 'T-EID-02',
                        'name' => 'Biometrics Capture (Fingerprints & Photo)',
                        'description' => 'In-person biometrics appointment at an ICP/EIDA service centre.',
                        'cost' => 0,
                        'department' => $departments['ICP'],
                    ],
                    [
                        'code' => 'T-EID-03',
                        'name' => 'Emirates ID Card Issuance Fee',
                        'description' => 'AED 100 per year of validity, plus delivery by Emirates Post.',
                        'cost' => 100,
                        'department' => $departments['ICP'],
                    ],
                ],
            ],
            [
                'code' => 'TG-STAMP',
                'name' => 'Residence Visa Stamping',
                'description' => 'Final stamping of the residence visa into the passport.',
                'tasks' => [
                    [
                        'code' => 'T-STAMP-01',
                        'name' => 'Submit Visa Stamping Application',
                        'description' => 'File the stamping request once medical and biometrics have cleared.',
                        'cost' => 100,
                        'department' => $departments['GDRFA'],
                    ],
                    [
                        'code' => 'T-STAMP-02',
                        'name' => 'Pay Residence Visa Stamping Fee',
                        'description' => 'Government stamping fee (typically AED 500–1,000 depending on visa class).',
                        'cost' => 500,
                        'department' => $departments['GDRFA'],
                    ],
                ],
            ],
            [
                'code' => 'TG-ATTEST',
                'name' => 'MOFA Document Attestation',
                'description' => 'Legalisation of personal/commercial documents for official use in the UAE.',
                'tasks' => [
                    [
                        'code' => 'T-ATTEST-01',
                        'name' => 'Attest Document at Originating Authority',
                        'description' => 'Local/notary attestation required before MOFA will accept the document.',
                        'cost' => 150,
                        'department' => $departments['MOFA'],
                    ],
                    [
                        'code' => 'T-ATTEST-02',
                        'name' => 'MOFA Attestation – Commercial Document (POA)',
                        'description' => 'Federal attestation fee for commercial documents such as a Power of Attorney.',
                        'cost' => 2000,
                        'department' => $departments['MOFA'],
                    ],
                ],
            ],
            [
                'code' => 'TG-TRADE',
                'name' => 'Trade Name & Licensing',
                'description' => 'Reservation, approval, and issuance of a mainland trade licence.',
                'tasks' => [
                    [
                        'code' => 'T-TRADE-01',
                        'name' => 'Reserve Trade Name',
                        'description' => 'Reserve the commercial name with DET before licensing.',
                        'cost' => 620,
                        'department' => $departments['DET'],
                    ],
                    [
                        'code' => 'T-TRADE-02',
                        'name' => 'Obtain DET Initial Approval',
                        'description' => 'No-objection confirmation of the proposed activity, legal form, and ownership.',
                        'cost' => 120,
                        'department' => $departments['DET'],
                    ],
                    [
                        'code' => 'T-TRADE-03',
                        'name' => 'Issue Trade Licence',
                        'description' => 'General trading mainland licence issuance (typically AED 12,500–15,000).',
                        'cost' => 13000,
                        'department' => $departments['DET'],
                    ],
                ],
            ],
            [
                'code' => 'TG-ESTAB',
                'name' => 'Establishment Card & Company Profile',
                'description' => 'Registers the company with labour and immigration authorities.',
                'tasks' => [
                    [
                        'code' => 'T-ESTAB-01',
                        'name' => 'Open MOHRE Establishment Card',
                        'description' => 'Register the company file with MOHRE using the trade licence and lease.',
                        'cost' => 650,
                        'department' => $departments['MOHRE'],
                    ],
                    [
                        'code' => 'T-ESTAB-02',
                        'name' => 'Register Company Immigration Card (GDRFA)',
                        'description' => 'Open the company\'s immigration file so it can sponsor employee visas.',
                        'cost' => 280,
                        'department' => $departments['GDRFA'],
                    ],
                ],
            ],
            [
                'code' => 'TG-TENANCY',
                'name' => 'Tenancy Registration',
                'description' => 'Official registration of a tenancy contract (Ejari in Dubai, Tawtheeq in Abu Dhabi).',
                'tasks' => [
                    [
                        'code' => 'T-TEN-01',
                        'name' => 'Register Ejari Contract (Dubai)',
                        'description' => 'Online registration/renewal via Dubai REST or the DLD website.',
                        'cost' => 178,
                        'department' => $departments['DLD'],
                    ],
                    [
                        'code' => 'T-TEN-02',
                        'name' => 'Register Tawtheeq Contract (Abu Dhabi)',
                        'description' => 'Landlord-side registration of the tenancy contract (annual renewal fee).',
                        'cost' => 50,
                        'department' => $departments['DMT'],
                    ],
                ],
            ],
            [
                'code' => 'TG-QUOTA',
                'name' => 'Work Permit Quota & Application',
                'description' => 'Employer-side quota approval and work permit filing for a new hire.',
                'tasks' => [
                    [
                        'code' => 'T-QUOTA-01',
                        'name' => 'Apply for Labour Quota Approval (Ta\'qeem)',
                        'description' => 'Ta\'qeem report and quota approval fee ahead of the work permit filing.',
                        'cost' => 478,
                        'department' => $departments['MOHRE'],
                    ],
                    [
                        'code' => 'T-QUOTA-02',
                        'name' => 'Submit Work Permit Application (Tas\'heel)',
                        'description' => 'Work permit issuance fee — ranges AED 250–3,450 by company classification (A/B/C).',
                        'cost' => 250,
                        'department' => $departments['MOHRE'],
                    ],
                ],
            ],
            [
                'code' => 'TG-CONTRACT',
                'name' => 'Labor Contract Drafting',
                'description' => 'Preparation and MOHRE registration of the employment offer and contract.',
                'tasks' => [
                    [
                        'code' => 'T-CONTRACT-01',
                        'name' => 'Draft Employment Offer Letter',
                        'description' => 'Prepare the formal offer letter ahead of the work permit application.',
                        'cost' => 50,
                        'department' => $departments['MOHRE'],
                    ],
                    [
                        'code' => 'T-CONTRACT-02',
                        'name' => 'Generate E-Signed Labor Contract',
                        'description' => 'Electronic MOHRE labour contract generation and signature.',
                        'cost' => 30,
                        'department' => $departments['MOHRE'],
                    ],
                ],
            ],
            [
                'code' => 'TG-GOLDEN',
                'name' => 'Golden Visa Processing',
                'description' => 'Long-term (10-year) residency for investors, professionals, and specified talents.',
                'tasks' => [
                    [
                        'code' => 'T-GOLDEN-01',
                        'name' => 'Verify Golden Visa Eligibility & Nomination',
                        'description' => 'Confirm the applicant meets the investor/professional/talent category criteria.',
                        'cost' => 0,
                        'department' => $departments['ICP'],
                    ],
                    [
                        'code' => 'T-GOLDEN-02',
                        'name' => 'Submit Golden Visa Application',
                        'description' => 'Application fee — typically AED 2,500–7,000 depending on category.',
                        'cost' => 2800,
                        'department' => $departments['ICP'],
                    ],
                    [
                        'code' => 'T-GOLDEN-03',
                        'name' => 'Golden Visa 10-Year Residency Issuance Fee',
                        'description' => 'Final long-term residency permit issuance.',
                        'cost' => 1000,
                        'department' => $departments['ICP'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Tasks that don't belong to a group — typically because they're a
     * single, service-specific step rather than part of a reusable bundle.
     *
     * @param array<string, GovernmentDepartment> $departments
     */
    protected function seedStandaloneTasks(array $departments): void
    {
        foreach ([
            [
                'code' => 'T-FAM-01',
                'name' => 'Open Family Sponsorship File',
                'description' => 'One-time family file opened under the sponsor before any dependent can be added.',
                'cost' => 100,
                'department' => $departments['ICP'],
            ],
            [
                'code' => 'T-FAM-02',
                'name' => 'Sponsor Spouse – Visa Issuance',
                'description' => 'Spouse residence visa issuance (attested marriage certificate required).',
                'cost' => 2500,
                'department' => $departments['GDRFA'],
            ],
            [
                'code' => 'T-FAM-03',
                'name' => 'Sponsor Child – Visa Issuance',
                'description' => 'Child residence visa issuance.',
                'cost' => 2000,
                'department' => $departments['GDRFA'],
            ],
        ] as $sequence => $taskData) {
            Task::firstOrCreate(
                ['code' => $taskData['code']],
                [
                    'service_component_id' => null,
                    'name' => $taskData['name'],
                    'description' => $taskData['description'],
                    'cost' => $taskData['cost'],
                    'government_department_id' => $taskData['department']?->id,
                    'sequence' => $sequence + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
