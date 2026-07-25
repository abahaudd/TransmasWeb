<?php

namespace App\Filament\Resources\CmsPages\Pages;

use App\Filament\Resources\CmsPages\CmsPageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCmsPage extends EditRecord
{
    protected static string $resource = CmsPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(__('labels.cms_page.view_page'))
                ->url(fn (): string => url('/'.($this->getRecord()->slug === 'home' ? '' : $this->getRecord()->slug)), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}
