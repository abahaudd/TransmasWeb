<?php

namespace App\Filament\Resources\GovernmentDepartments\Pages;

use App\Filament\Resources\GovernmentDepartments\GovernmentDepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGovernmentDepartment extends EditRecord
{
    protected static string $resource = GovernmentDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
