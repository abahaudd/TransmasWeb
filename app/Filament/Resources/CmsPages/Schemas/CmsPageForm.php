<?php

namespace App\Filament\Resources\CmsPages\Schemas;

use App\Models\Cms\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CmsPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('labels.cms_page.section_page'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('labels.cms_page.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, ?Page $record) {
                                if (! $record) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('labels.cms_page.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('labels.cms_page.slug_helper')),
                        Select::make('template')
                            ->label(__('labels.cms_page.template'))
                            ->options(Page::TEMPLATES)
                            ->default('default')
                            ->required(),
                        Toggle::make('is_published')
                            ->label(__('labels.cms_page.published')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make(__('labels.cms_page.section_seo'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('labels.cms_page.meta_title'))
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label(__('labels.cms_page.meta_description'))
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
