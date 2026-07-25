<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCatalogSeeder extends Seeder
{
    /**
     * Seed the starter service catalog: the four UAE government-services
     * categories and the services within each. cost/price are left at 0 —
     * an admin fills those in via the Services CRUD once known.
     */
    public function run(): void
    {
        foreach ($this->catalog() as $categoryData) {
            $category = ServiceCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['services'] as $serviceData) {
                Service::firstOrCreate(
                    ['code' => $serviceData['code']],
                    [
                        'service_category_id' => $category->id,
                        'name' => $serviceData['name'],
                        'description' => $serviceData['description'],
                        'cost' => 0,
                        'price' => 0,
                        'status' => Service::STATUS_ACTIVE,
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array{code: string, name: string, description: string, services: array<int, array{code: string, name: string, description: string}>}>
     */
    protected function catalog(): array
    {
        return [
            [
                'code' => 'IMM',
                'name' => 'Immigration & Residency Services (Individual & Family)',
                'description' => 'Entry, stay, and residency of individuals in the UAE, largely managed through portals like Amer (Dubai) or the ICP (Federal Authority for Identity, Citizenship, Customs and Port Security).',
                'services' => [
                    [
                        'code' => 'IMM-ENTRY',
                        'name' => 'Entry Permits',
                        'description' => 'Tourist visas, visit visas, and initial entry permits for employment.',
                    ],
                    [
                        'code' => 'IMM-RES',
                        'name' => 'Residency Management',
                        'description' => 'New residency visas, renewals, visa status adjustments, and visa cancellations.',
                    ],
                    [
                        'code' => 'IMM-FAM',
                        'name' => 'Family Sponsorship',
                        'description' => 'Form preparation for sponsoring spouses, children, parents, or domestic workers.',
                    ],
                    [
                        'code' => 'IMM-LTV',
                        'name' => 'Long-Term Visas',
                        'description' => 'Premium residency requests such as Golden Visas and Green Visas.',
                    ],
                ],
            ],
            [
                'code' => 'EMP',
                'name' => 'Employment & Labor Services (Corporate)',
                'description' => 'Corporate services managing the employer-employee relationship, processed directly through MOHRE (Ministry of Human Resources and Emiratisation) or Tasheel systems.',
                'services' => [
                    [
                        'code' => 'EMP-WORK',
                        'name' => 'Work Permits',
                        'description' => 'Application typing for new company quotas, quota updates, and mission work permits.',
                    ],
                    [
                        'code' => 'EMP-CONTRACT',
                        'name' => 'Labor Contracts',
                        'description' => 'Electronic generation of formal offer letters and employment contracts.',
                    ],
                    [
                        'code' => 'EMP-PROFILE',
                        'name' => 'Company Profiles',
                        'description' => 'Opening, updating, or renewing Establishment Cards and company profiles within labor portals.',
                    ],
                ],
            ],
            [
                'code' => 'LEG',
                'name' => 'Legal & Identification Services (Mandatory Compliance)',
                'description' => 'Mandatory compliance services that every resident or business owner must complete to maintain legal status in the country.',
                'services' => [
                    [
                        'code' => 'LEG-ID',
                        'name' => 'Identity Management',
                        'description' => 'Applications, renewals, and replacements for the mandatory Emirates ID.',
                    ],
                    [
                        'code' => 'LEG-MED',
                        'name' => 'Medical Fitness Processing',
                        'description' => 'Booking and typing applications for mandatory residency health screenings (via DHA or EHS).',
                    ],
                    [
                        'code' => 'LEG-DRAFT',
                        'name' => 'Legal Drafting & Attestation',
                        'description' => 'Drafting formal power of attorney (POA), board resolutions, and arranging document attestation through the Ministry of Foreign Affairs (MOFA).',
                    ],
                ],
            ],
            [
                'code' => 'BIZ',
                'name' => 'Business Licensing & Municipal Gateways',
                'description' => 'Services for businesses looking to establish or maintain their operations, connecting with Department of Economy and Tourism (DET) portals and local municipalities.',
                'services' => [
                    [
                        'code' => 'BIZ-LIC',
                        'name' => 'Commercial Licensing',
                        'description' => 'Typing for new trade license applications, annual renewals, and amendments.',
                    ],
                    [
                        'code' => 'BIZ-TRADE',
                        'name' => 'Trade Name Registration',
                        'description' => 'Reserving or changing corporate and commercial names.',
                    ],
                    [
                        'code' => 'BIZ-TENANCY',
                        'name' => 'Property & Tenancy Registration',
                        'description' => 'Processing tenancy contracts, such as Ejari in Dubai or Tawtheeq in Abu Dhabi.',
                    ],
                ],
            ],
        ];
    }
}
