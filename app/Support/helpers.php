<?php

use App\Services\FormatService;

if (! function_exists('format_money')) {
    /**
     * Format an amount as currency using the app-wide formatting settings.
     */
    function format_money(float|int|string $amount, ?string $currencySymbol = null): string
    {
        return app(FormatService::class)->money($amount, $currencySymbol);
    }
}

if (! function_exists('format_number')) {
    /**
     * Format a plain number using the app-wide formatting settings.
     */
    function format_number(float|int|string $value, ?int $decimals = null): string
    {
        return app(FormatService::class)->number($value, $decimals);
    }
}

if (! function_exists('format_date')) {
    /**
     * Format a date using the app-wide date format setting.
     */
    function format_date(DateTimeInterface|string|null $date, ?string $format = null): string
    {
        return app(FormatService::class)->date($date, $format);
    }
}
