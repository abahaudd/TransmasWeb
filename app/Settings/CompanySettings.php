<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public string $company_name;

    public ?string $logo_path;

    public ?string $address;

    public ?string $phone;

    public ?string $email;

    public ?string $website;

    public static function group(): string
    {
        return 'company';
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path
            ? asset('storage/'.ltrim($this->logo_path, '/'))
            : null;
    }
}
