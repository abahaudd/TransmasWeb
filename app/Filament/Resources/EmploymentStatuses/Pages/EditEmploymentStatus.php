<?php

namespace App\Filament\Resources\EmploymentStatuses\Pages;

use App\Filament\Resources\EmploymentStatuses\EmploymentStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmploymentStatus extends EditRecord
{
    protected static string $resource = EmploymentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
