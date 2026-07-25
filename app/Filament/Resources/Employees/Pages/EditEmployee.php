<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Models\PersonAddress;
use App\Services\EmployeeService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Employee $employee */
        $employee = $this->record;
        $person = $employee->person;

        if ($person) {
            $data['first_name'] = $person->first_name;
            $data['last_name'] = $person->last_name;
            $data['gender'] = $person->gender;
            $data['birth_date'] = $person->birth_date?->toDateString();
            $data['nationality'] = $person->nationality;
            $data['national_id'] = $person->national_id;
            $data['phone'] = $person->phone;
            $data['mobile'] = $person->mobile;
            $data['email'] = $person->email;

            $data['addresses'] = $person->addresses()
                ->get()
                ->map(fn (PersonAddress $address): array => [
                    'id' => $address->id,
                    'address_type' => $address->address_type,
                    'address' => $address->address,
                    'location' => $address->location,
                    'territory' => $address->territory,
                    'postal_code' => $address->postal_code,
                    'country_id' => $address->country_id,
                    'is_primary' => $address->is_primary,
                    'remarks' => $address->remarks,
                ])
                ->all();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Employee $record */
        return app(EmployeeService::class)->updateEmployee($record, $data);
    }
}
