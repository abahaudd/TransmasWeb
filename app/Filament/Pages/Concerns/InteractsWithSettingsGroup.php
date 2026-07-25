<?php

namespace App\Filament\Pages\Concerns;

use App\Services\SettingsService;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Throwable;

/**
 * Drop-in replacement for Filament\Pages\SettingsPage's data source: fills
 * and saves the page's form against SettingsService (settings table, keyed
 * by group/name) instead of a Spatie Settings DTO class. Everything else
 * (save action, transactions, unsaved-changes alert) still comes from the
 * vendor SettingsPage base class this trait is meant to be used alongside.
 */
trait InteractsWithSettingsGroup
{
    /**
     * The settings-table "group" this page manages.
     */
    abstract protected function settingsGroup(): string;

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill(
            app(SettingsService::class)->getGroup($this->settingsGroup())
        );

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        if (! $this->canEdit()) {
            return;
        }

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            app(SettingsService::class)->setMany($this->settingsGroup(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
        }
    }
}
