<?php

namespace App\Filament\Resources\Staff\ManagersResource\Pages;

use App\Filament\Resources\Staff\ManagersResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditManager extends EditRecord
{
    protected static string $resource = ManagersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
