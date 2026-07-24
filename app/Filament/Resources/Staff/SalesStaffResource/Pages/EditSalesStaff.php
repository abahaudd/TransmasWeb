<?php

namespace App\Filament\Resources\Staff\SalesStaffResource\Pages;

use App\Filament\Resources\Staff\SalesStaffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalesStaff extends EditRecord
{
    protected static string $resource = SalesStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
