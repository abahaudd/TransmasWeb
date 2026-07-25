<?php

namespace App\Filament\Resources\SequenceNumberFormats\Pages;

use App\Filament\Resources\SequenceNumberFormats\SequenceNumberFormatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSequenceNumberFormat extends EditRecord
{
    protected static string $resource = SequenceNumberFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
