<?php

namespace App\Filament\Resources\SequenceNumberFormats\Pages;

use App\Filament\Resources\SequenceNumberFormats\SequenceNumberFormatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSequenceNumberFormats extends ListRecords
{
    protected static string $resource = SequenceNumberFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
