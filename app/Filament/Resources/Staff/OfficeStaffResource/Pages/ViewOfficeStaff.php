<?php

namespace App\Filament\Resources\Staff\OfficeStaffResource\Pages;

use App\Filament\Resources\Staff\OfficeStaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOfficeStaff extends ViewRecord
{
    protected static string $resource = OfficeStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
