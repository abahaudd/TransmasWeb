<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Address;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $address = $this->record->address;

        $data['address_line'] = $address?->address;
        $data['location'] = $address?->location;
        $data['territory'] = $address?->territory;
        $data['postal_code'] = $address?->postal_code;
        $data['country_id'] = $address?->country_id;
        $data['latitude'] = $address?->latitude;
        $data['longitude'] = $address?->longitude;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

        if ($this->record->address) {
            if ($hasAddressInput) {
                $this->record->address->update($addressData);
            } else {
                $this->record->address->delete();
                $data['address_id'] = null;
            }
        } elseif ($hasAddressInput) {
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
