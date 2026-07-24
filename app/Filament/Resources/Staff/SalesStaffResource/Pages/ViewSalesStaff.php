<?php

namespace App\Filament\Resources\Staff\SalesStaffResource\Pages;

use App\Filament\Resources\Staff\SalesStaffResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesStaff extends ViewRecord
{
    protected static string $resource = SalesStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
