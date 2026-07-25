<?php

namespace App\Support\Facades;

use App\Services\FormatService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string money(float|int|string $amount, ?string $currencySymbol = null)
 * @method static string number(float|int|string $value, ?int $decimals = null)
 * @method static string date(\DateTimeInterface|string|null $date, ?string $format = null)
 *
 * @see \App\Services\FormatService
 */
class Format extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FormatService::class;
    }
}
