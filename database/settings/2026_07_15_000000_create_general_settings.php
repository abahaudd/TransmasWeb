<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', config('app.name', 'LaravelStarter'));
        $this->migrator->add('general.site_description', null);
        $this->migrator->add('general.support_email', null);
        $this->migrator->add('general.timezone', 'UTC');
        $this->migrator->add('general.locale', 'en');
    }
};
