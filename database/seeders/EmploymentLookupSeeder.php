<?php

namespace Database\Seeders;

use App\Models\EmploymentStatus;
use App\Models\EmploymentType;
use Illuminate\Database\Seeder;

class EmploymentLookupSeeder extends Seeder
{
    /**
     * Seed the default employment types/statuses so the configurable
     * lookups aren't empty out of the box. Admins can add more later.
     */
    public function run(): void
    {
        foreach ([
            'Permanent',
            'Contract',
            'Temporary',
            'Part Time',
            'Intern',
            'Consultant',
        ] as $name) {
            EmploymentType::firstOrCreate(['name' => $name]);
        }

        foreach ([
            'Probation' => false,
            'Active' => false,
            'On Leave' => false,
            'Suspended' => false,
            'Resigned' => true,
            'Terminated' => true,
            'Retired' => true,
            'Deceased' => true,
        ] as $name => $isTerminal) {
            EmploymentStatus::firstOrCreate(['name' => $name], ['is_terminal' => $isTerminal]);
        }
    }
}
