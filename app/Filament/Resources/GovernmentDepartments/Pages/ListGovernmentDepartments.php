<?php

namespace App\Filament\Resources\GovernmentDepartments\Pages;

use App\Filament\Resources\GovernmentDepartments\GovernmentDepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGovernmentDepartments extends ListRecords
{
    protected static string $resource = GovernmentDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
