<?php

namespace App\Filament\Resources\Staff\ManagersResource\Pages;

use App\Filament\Resources\Staff\ManagersResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewManager extends ViewRecord
{
    protected static string $resource = ManagersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
