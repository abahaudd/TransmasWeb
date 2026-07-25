<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Models\Company;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        [$orderedIds, $depths] = self::hierarchy();

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($orderedIds) {
                $query->with(['parent']);

                if ($orderedIds !== []) {
                    $query->orderByRaw('FIELD(companies.id, ' . implode(',', $orderedIds) . ')');
                }
            })
            ->columns([
                TextColumn::make('legal_name')
                    ->label(__('labels.company.legal_name'))
                    ->html()
                    ->formatStateUsing(fn (string $state, Company $record): string => self::renderIndentedName($state, $depths[$record->id] ?? 0))
                    ->searchable(),
                TextColumn::make('company_type')
                    ->label(__('labels.company.company_type'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_code')
                    ->label(__('labels.company.company_code'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('parent.legal_name')
                    ->label(__('labels.company.parent_company'))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('labels.status'))
                    ->badge()
                    ->color(fn (string $state): string => $state === Company::STATUS_ACTIVE ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('labels.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website')
                    ->label(__('labels.website'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_work_hour')
                    ->label(__('labels.company.start_work_hour'))
                    ->time()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_work_hour')
                    ->label(__('labels.company.end_work_hour'))
                    ->time()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('weekends')
                    ->label(__('labels.company.weekends'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('labels.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('labels.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_type')
                    ->label(__('labels.company.company_type'))
                    ->options(Company::typeOptions()),
                SelectFilter::make('status')
                    ->label(__('labels.status'))
                    ->options(Company::statusOptions()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.view')),
                EditAction::make()
                    ->iconButton()
                    ->tooltip(__('labels.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Walk the parent/child adjacency list into a flat, depth-first order —
     * each branch appears directly beneath its parent — and a depth map used
     * to indent the name column. Records whose parent is missing/filtered
     * out (e.g. soft-deleted) still surface, as top-level rows.
     *
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    private static function hierarchy(): array
    {
        $rows = Company::query()->withTrashed()->orderBy('legal_name')->get(['id', 'parent_id']);

        $childrenByParent = [];

        foreach ($rows as $row) {
            $childrenByParent[$row->parent_id ?? 0][] = $row->id;
        }

        $orderedIds = [];
        $depths = [];
        $visited = [];

        $visit = function (int $parentId, int $depth) use (&$visit, &$childrenByParent, &$orderedIds, &$depths, &$visited): void {
            foreach ($childrenByParent[$parentId] ?? [] as $id) {
                if (isset($visited[$id])) {
                    continue;
                }

                $visited[$id] = true;
                $orderedIds[] = $id;
                $depths[$id] = $depth;
                $visit($id, $depth + 1);
            }
        };

        $visit(0, 0);

        foreach ($rows as $row) {
            if (! isset($visited[$row->id])) {
                $orderedIds[] = $row->id;
                $depths[$row->id] = 0;
            }
        }

        return [$orderedIds, $depths];
    }

    /**
     * Prefix branch rows with an indent and a small "L" turn icon pointing
     * at the name, so the parent/child relationship reads as a tree.
     */
    private static function renderIndentedName(string $name, int $depth): string
    {
        $name = e($name);

        if ($depth === 0) {
            return $name;
        }

        $icon = Blade::render(sprintf(
            '<x-filament::icon icon="%s" class="w-4 h-4 inline-block align-text-bottom" />',
            Heroicon::OutlinedArrowTurnDownRight->getIconForSize(IconSize::Small),
        ));

        $indent = ($depth - 1) * 20;

        return sprintf(
            '<span style="padding-left:%dpx;display:inline-flex;align-items:center;gap:4px;"><span style="color:var(--primary-500);display:inline-flex;">%s</span>%s</span>',
            $indent,
            $icon,
            $name,
        );
    }
}
