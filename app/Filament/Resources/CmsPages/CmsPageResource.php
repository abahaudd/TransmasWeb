<?php

namespace App\Filament\Resources\CmsPages;

use App\Filament\Resources\CmsPages\Pages\CreateCmsPage;
use App\Filament\Resources\CmsPages\Pages\EditCmsPage;
use App\Filament\Resources\CmsPages\Pages\ListCmsPages;
use App\Filament\Resources\CmsPages\RelationManagers\BlocksRelationManager;
use App\Filament\Resources\CmsPages\Schemas\CmsPageForm;
use App\Filament\Resources\CmsPages\Tables\CmsPagesTable;
use App\Models\Cms\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CmsPageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $slug = 'cms-pages';

    public static function getModelLabel(): string
    {
        return __('labels.cms_page.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('labels.nav.pages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('labels.nav.groups.cms');
    }

    public static function getNavigationLabel(): string
    {
        return __('labels.nav.pages');
    }

    public static function form(Schema $schema): Schema
    {
        return CmsPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CmsPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BlocksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCmsPages::route('/'),
            'create' => CreateCmsPage::route('/create'),
            'edit' => EditCmsPage::route('/{record}/edit'),
        ];
    }
}
