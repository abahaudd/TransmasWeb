<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class ThemeSettingsSeeder extends Seeder
{
    /**
     * Seed defaults for the app-wide panel color palette (group "theme"),
     * read by AdminPanelProvider::panel() — never hardcoded there.
     */
    public function run(): void
    {
        $settings = app(SettingsService::class);

        foreach ([
            'primary_color' => '#2CA58D',
            'gray_color' => '#6B7280',
            'success_color' => '#16A34A',
            'warning_color' => '#F2B33D',
            'danger_color' => '#E8735A',
            'info_color' => '#8B7FD1',
            // Deliberately separate from gray_color: gray_color only feeds
            // Filament's generated gray palette (borders/muted text); these
            // are exact, independently controllable layout surfaces, so
            // tuning one never moves another.
            'page_background_color' => '#ccced3',
            'menu_background_color' => '#ccced3',
            'card_background_color' => '#FFFFFF',
        ] as $name => $default) {
            if ($settings->get('theme', $name) === null) {
                $settings->set('theme', $name, $default);
            }
        }
    }
}
