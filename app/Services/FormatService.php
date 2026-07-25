<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * Single source of truth for display formatting (currency, numbers, dates)
 * so every view, print/PDF template, and Blade component renders values the
 * same way. Backed by the "formatting" settings group (see
 * FormattingSettingsSeeder / ManageFormattingSettings) rather than hardcoded
 * literals, so admins can change symbol/separators/date format without a
 * deploy. Bound as a singleton (see AppServiceProvider) so the settings
 * group is only fetched once per request.
 */
class FormatService
{
    /**
     * @var array<string, mixed>|null
     */
    protected ?array $settings = null;

    public function __construct(protected SettingsService $settingsService)
    {
    }

    /**
     * Format an amount as currency, e.g. "AED 1,234.50".
     */
    public function money(float|int|string $amount, ?string $currencySymbol = null): string
    {
        $number = $this->number($amount);
        $symbol = $currencySymbol ?? (string) $this->setting('currency_symbol', 'AED');

        if (blank($symbol)) {
            return $number;
        }

        $position = (string) $this->setting('currency_symbol_position', 'before');

        return $position === 'after' ? "{$number} {$symbol}" : "{$symbol} {$number}";
    }

    /**
     * Format a plain number using the configured separators, e.g. "1,234.50".
     */
    public function number(float|int|string $value, ?int $decimals = null): string
    {
        $decimals ??= (int) $this->setting('decimal_places', 2);

        return number_format(
            (float) $value,
            $decimals,
            (string) $this->setting('decimal_separator', '.'),
            (string) $this->setting('thousands_separator', ',')
        );
    }

    /**
     * Format a date using the configured app-wide date format.
     */
    public function date(DateTimeInterface|string|null $date, ?string $format = null): string
    {
        if (blank($date)) {
            return '';
        }

        $format ??= (string) $this->setting('date_format', 'd/m/Y');

        $carbon = $date instanceof DateTimeInterface ? Carbon::instance($date) : Carbon::parse($date);

        return $carbon->format($format);
    }

    protected function setting(string $name, mixed $default): mixed
    {
        $this->settings ??= $this->settingsService->getGroup('formatting');

        $value = $this->settings[$name] ?? null;

        return $value === null ? $default : $value;
    }
}
