<?php

namespace App\Filament\Resources\ServiceComponents\Pages;

use App\Filament\Resources\ServiceComponents\ServiceComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceComponent extends EditRecord
{
    protected static string $resource = ServiceComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
