<?php

namespace Database\Seeders;

use App\Models\SequenceNumberFormat;
use Illuminate\Database\Seeder;

class SequenceNumberFormatSeeder extends Seeder
{
    /**
     * Seed the sequence number formats needed by the app out of the box.
     * Admins can add more categories (invoice, receipt, ...) later.
     */
    public function run(): void
    {
        SequenceNumberFormat::firstOrCreate(
            ['category' => 'employee'],
            ['prefix' => 'EMP', 'separator' => '-', 'incrementer' => 0, 'length' => 5]
        );
    }
}
