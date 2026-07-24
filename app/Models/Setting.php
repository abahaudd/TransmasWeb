<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'name',
        'locked',
        'payload',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'locked' => 'boolean',
        'payload' => 'array', // Automatically encodes/decodes JSON
    ];

    /**
     * In-request cache for the resolved display timezone.
     */
    protected static ?string $cachedTimezone = null;

    /**
     * Get the configured display timezone (IANA name) from the
     * settings table (group "location", name "timezone", payload["iana"]).
     * Falls back to the application timezone when unset or invalid.
     */
    public static function getTimezone(string $default = 'UTC'): string
    {
        if (static::$cachedTimezone !== null) {
            return static::$cachedTimezone;
        }

        $iana = null;

        try {
            $iana = static::getPayloadValue('location', 'timezone', 'iana');
        } catch (\Throwable) {
            $iana = null;
        }

        $timezone = is_string($iana) && $iana !== ''
            ? $iana
            : (config('app.timezone') ?: $default);

        try {
            new \DateTimeZone($timezone);
        } catch (\Throwable) {
            $timezone = $default;
        }

        return static::$cachedTimezone = $timezone;
    }

    /**
     * Get a setting value by group and name.
     *
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $group, string $name, $default = null)
    {
        $setting = static::where('group', $group)->where('name', $name)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->payload['value'] ?? $default;
    }

    /**
     * Get a keyed value from a setting payload without caching it.
     */
    public static function getPayloadValue(string $group, string $name, string $key, $default = null)
    {
        $setting = static::where('group', $group)->where('name', $name)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->payload[$key] ?? $default;
    }

    /**
     * Get the configured currency symbol.
     *
      * Looks in the "currency" group and returns payload["symbol"], preferring the default currency setting.
     */
    public static function getCurrencySymbol($default = null)
    {
        $setting = static::where('group', 'currency')
            ->where('name', 'default')
            ->first();

        if (! $setting) {
            $setting = static::where('group', 'currency')
                ->where('name', 'symbol')
                ->first();
        }

        if (! $setting) {
            $setting = static::where('group', 'currency')->first();
        }

        if (! $setting) {
            return $default;
        }

        return $setting->payload['symbol'] ?? $setting->payload['value'] ?? $default;
    }

    /**
     * Get the configured quote currency.
     */
    public static function getQuoteCurrency($default = null)
    {
        $setting = static::where('group', 'quote')
            ->where('name', 'default')
            ->first();

        if ($setting) {
            return $setting->payload['currency'] ?? $setting->payload['symbol'] ?? $setting->payload['value'] ?? $default;
        }

        $setting = static::where('group', 'quote')
            ->where('name', 'currency')
            ->first();

        if (! $setting) {
            return $default;
        }

        return $setting->payload['symbol'] ?? $setting->payload['value'] ?? $default;
    }

    /**
     * Get the configured quote validity period in weeks.
     */
    public static function getQuoteValidityWeeks(int $default = 1): int
    {
        $setting = static::where('group', 'quote')
            ->where('name', 'default')
            ->first();

        if ($setting) {
            return max(0, (int) ($setting->payload['validity'] ?? $setting->payload['weeks'] ?? $setting->payload['value'] ?? $default));
        }

        $setting = static::where('group', 'quote')
            ->where('name', 'validity')
            ->first();

        if (! $setting) {
            return $default;
        }

        return max(0, (int) ($setting->payload['weeks'] ?? $setting->payload['value'] ?? $default));
    }

    /**
     * Get the configured quote tax defaults.
     */
    public static function getQuoteDefaultTaxes(): array
    {
        $setting = static::where('group', 'quote')
            ->where('name', 'taxes')
            ->first();

        if ($setting) {
            return static::normalizeQuoteTaxes(is_array($setting->payload) ? $setting->payload : []);
        }

        $setting = static::where('group', 'quote')
            ->where('name', 'default')
            ->first();

        if (! $setting) {
            return [];
        }

        $payload = is_array($setting->payload) ? $setting->payload : [];

        if (isset($payload['taxes']) && is_array($payload['taxes'])) {
            return static::normalizeQuoteTaxes($payload['taxes']);
        }

        if (isset($payload['tax']) && is_array($payload['tax'])) {
            return static::normalizeQuoteTaxes($payload['tax']);
        }

        if (isset($payload['tax_name']) || isset($payload['tax_rate'])) {
            return [[
                'name' => (string) ($payload['tax_name'] ?? 'Tax'),
                'rate' => (string) ($payload['tax_rate'] ?? 0),
            ]];
        }

        return [];
    }

    /**
     * Normalize one or more configured quote taxes for the quote form.
     */
    protected static function normalizeQuoteTaxes(array $taxes): array
    {
        if (array_key_exists('name', $taxes) || array_key_exists('rate', $taxes)) {
            return [[
                'name' => (string) ($taxes['name'] ?? 'Tax'),
                'rate' => (string) ($taxes['rate'] ?? 0),
            ]];
        }

        return collect($taxes)
            ->filter(fn ($tax): bool => is_array($tax))
            ->map(fn (array $tax): array => [
                'name' => (string) ($tax['name'] ?? 'Tax'),
                'rate' => (string) ($tax['rate'] ?? 0),
            ])
            ->values()
            ->all();
    }

    /**
     * Get the configured quote other charge defaults.
     */
    public static function getQuoteDefaultCharges(): array
    {
        $setting = static::where('group', 'quote')
            ->where('name', 'default')
            ->first();

        if (! $setting) {
            return [
                'taxes' => [],
                'bank_charges' => 0,
                'insurance' => 0,
                'other_charges' => [],
            ];
        }

        $payload = is_array($setting->payload) ? $setting->payload : [];

        return [
            'taxes' => static::getQuoteDefaultTaxes(),
            'bank_charges' => $payload['bank_charges'] ?? 0,
            'insurance' => $payload['insurance'] ?? 0,
            'other_charges' => is_array($payload['other_charges'] ?? null) ? $payload['other_charges'] : [],
        ];
    }

    /**
     * Get the full company address from settings payload.
     *
     * Reads from group "company" and name "details", then combines:
     * address, city, emirate/Emirate, country.
     */
    public static function getCompanyAddress($default = null)
    {
        $setting = static::where('group', 'company')
            ->where('name', 'details')
            ->first();

        if (! $setting) {
            return $default;
        }

        $payload = is_array($setting->payload) ? $setting->payload : [];

        $parts = array_filter([
            $payload['address'] ?? null,
            $payload['city'] ?? null,
            $payload['emirate'] ?? ($payload['Emirate'] ?? null),
            $payload['country'] ?? null,
        ], static fn ($value): bool => filled($value));

        if (empty($parts)) {
            return $default;
        }

        return implode(', ', $parts);
    }

    /**
     * Set a setting value (creates or updates).
     *
     * @param string $group
     * @param string $name
     * @param mixed $value
     * @param bool $locked
     * @return self
     */
    public static function setValue(string $group, string $name, $value, bool $locked = false): self
    {
        return static::updateOrCreate(
            ['group' => $group, 'name' => $name],
            [
                'locked' => $locked,
                'payload' => ['value' => $value],
            ]
        );
    }
}