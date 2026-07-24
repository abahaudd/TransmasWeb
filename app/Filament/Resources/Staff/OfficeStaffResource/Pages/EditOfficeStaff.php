<?php

namespace App\Filament\Resources\Staff\OfficeStaffResource\Pages;

use App\Filament\Resources\Staff\OfficeStaffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeStaff extends EditRecord
{
    protected static string $resource = OfficeStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
