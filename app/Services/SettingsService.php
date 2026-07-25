<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * Generic accessor for the shared `settings` table, keyed by (group, name).
 *
 * This manages "one value per row" settings (e.g. group=general,
 * name=site_name, payload="ABC Document Services") — the convention already
 * used by the general/company settings previously served by Spatie's
 * laravel-settings package. It's a different convention from the
 * "dict payload per row" settings elsewhere in the app (e.g. group=location,
 * name=headings, payload={address:..., country:...}) exposed via
 * Setting::getPayloadValue() — both share the same table, so don't mix them
 * up when adding new settings.
 */
class SettingsService
{
    public function get(string $group, string $name, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('group', $group)->where('name', $name)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->payload ?? $default;
    }

    /**
     * All values in a group, keyed by name.
     *
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        return Setting::query()
            ->where('group', $group)
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->name => $setting->payload])
            ->all();
    }

    public function set(string $group, string $name, mixed $value, bool $locked = false): Setting
    {
        // Written via the query builder rather than Setting::updateOrCreate():
        // Eloquent's JSON cast skips encoding when the value is null (it
        // assigns a raw SQL NULL instead), which violates payload's NOT NULL
        // constraint. json_encode(null) correctly stores the JSON text
        // "null", matching how nullable settings are already stored.
        $now = now();

        DB::table('settings')->updateOrInsert(
            ['group' => $group, 'name' => $name],
            [
                'payload' => json_encode($value),
                'locked' => $locked,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        return Setting::query()->where('group', $group)->where('name', $name)->firstOrFail();
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMany(string $group, array $values, bool $locked = false): void
    {
        foreach ($values as $name => $value) {
            $this->set($group, $name, $value, $locked);
        }
    }

    /**
     * Resolve the company logo (group=company, name=logo_path) to a public
     * URL — absolute URLs/paths pass through, storage-relative paths are
     * resolved against the public disk.
     */
    public function companyLogoUrl(): ?string
    {
        $path = $this->get('company', 'logo_path');

        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
