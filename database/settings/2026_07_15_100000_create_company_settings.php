<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.company_name', config('app.name', 'LaravelStarter'));
        $this->migrator->add('company.logo_path', null);
        $this->migrator->add('company.address', '100 Main Street, Suite 400, Dubai, UAE');
        $this->migrator->add('company.phone', '+971 4 000 0000');
        $this->migrator->add('company.email', 'hello@example.com');
        $this->migrator->add('company.website', 'https://www.example.com');
    }
};
