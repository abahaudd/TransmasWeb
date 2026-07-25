<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Employee */
        return app(EmployeeService::class)->createEmployee($data);
    }
}
