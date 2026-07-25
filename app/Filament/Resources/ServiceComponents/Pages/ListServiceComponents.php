<?php

namespace App\Filament\Resources\ServiceComponents\Pages;

use App\Filament\Resources\ServiceComponents\ServiceComponentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceComponents extends ListRecords
{
    protected static string $resource = ServiceComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
