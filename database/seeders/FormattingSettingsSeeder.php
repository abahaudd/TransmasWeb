<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class FormattingSettingsSeeder extends Seeder
{
    /**
     * Seed defaults for the app-wide display formatting settings
     * (group "formatting"), consumed by FormatService.
     */
    public function run(): void
    {
        $settings = app(SettingsService::class);

        foreach ([
            'currency_symbol' => 'AED',
            'currency_symbol_position' => 'before',
            'thousands_separator' => ',',
            'decimal_separator' => '.',
            'decimal_places' => 2,
            'date_format' => 'd/m/Y',
        ] as $name => $default) {
            if ($settings->get('formatting', $name) === null) {
                $settings->set('formatting', $name, $default);
            }
        }
    }
}
