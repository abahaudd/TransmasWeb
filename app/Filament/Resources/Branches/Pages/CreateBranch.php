<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\BranchResource;
use App\Models\Address;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $addressData = [
            'address' => $data['address_line'] ?? null,
            'location' => $data['location'] ?? null,
            'territory' => $data['territory'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ];

        $hasAddressInput = collect($addressData)->contains(fn ($value) => filled($value));

        if ($hasAddressInput) {
            $address = Address::create($addressData);
            $data['address_id'] = $address->id;
        }

        unset(
            $data['address_line'],
            $data['location'],
            $data['territory'],
            $data['postal_code'],
            $data['country_id'],
            $data['latitude'],
            $data['longitude'],
        );

        return $data;
    }
}