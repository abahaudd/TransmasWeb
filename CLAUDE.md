# Project Guidelines

## Translations are mandatory for all user-facing text

All user-facing strings in Filament resources, pages, widgets, and models — form/table/infolist field labels, section headings, navigation groups, navigation labels, page titles, breadcrumbs, action/button labels, tooltips, notification titles, and validation/exception messages — must go through Laravel's `__()` translator, never hardcoded literals. English (`lang/en/*.php`) is the only language required for now, but every string still needs a translation key so additional locales can be added later without another sweep through the codebase.

### File layout (`lang/en/`)

- **`labels.php`** — every field label, section heading, navigation group/resource/page label, and breadcrumb. This is the default home for new UI text.
- **`errors.php`** — validation messages and exception messages (e.g. `ValidationException::withMessages([...])`, custom `RuntimeException` messages).
- **`messages.php`** — success/info notification text (e.g. `Notification::make()->title(__('messages.account_created'))`).
- **`actions.php`** — action/button labels reused across multiple places (e.g. `add_user`, `reset_password`).
- **`help.php`** — placeholder/helper copy not tied to one specific field.

### `labels.php` structure

- Shared, reused-everywhere labels (`name`, `email`, `phone`, `status`, `created_at`, `view`, `edit`, ...) live at the **top level** of the array.
- Anything specific to one resource/domain lives **nested under that resource's key** (e.g. `labels.company.legal_name`, `labels.employee.employment_status`, `labels.settings.formatting.currency_symbol`).
- Navigation groups and per-resource nav labels live under `labels.nav.*` (e.g. `labels.nav.groups.control_panel`, `labels.nav.companies`).
- **Before adding a new top-level key, check it doesn't collide with an existing one.** PHP array literals silently let a later duplicate key overwrite an earlier one — e.g. a top-level `'phone' => 'Phone'` got silently clobbered by a later `'phone' => [...]` nested section during this work and had to be renamed to `phone_record`. Run this check after editing the file:
  ```
  php -r '
  $lines = file("lang/en/labels.php");
  $keys = [];
  foreach ($lines as $line) {
      if (preg_match("/^    \x27([a-zA-Z_]+)\x27\s*=>/", $line, $m)) $keys[] = $m[1];
  }
  foreach (array_count_values($keys) as $k => $c) { if ($c > 1) echo "DUPLICATE: $k ($c)\n"; }
  '
  ```

### Filament specifics

- `Filament\Navigation\NavigationGroup::make(__(...))` in the panel provider and every resource/page's `getNavigationGroup()` override **must use the exact same translation call** — Filament groups navigation items by matching label string, so a resource returning the raw string while the panel provider returns the translated one (or vice versa) creates a duplicate, ungrouped nav entry instead of joining the group.
- Static properties can't call functions (`protected static ?string $title = __('...')` is a PHP compile error). Use the instance/static getter override instead: `getTitle()`, `getNavigationLabel()`, `getModelLabel()`, `getPluralModelLabel()`.
- Values that are already admin-configurable through a settings table (e.g. the address-field headings driven by `Setting::where('group', 'location')`) are **not** hardcoded developer strings and should NOT be run through `__()` — leave those as-is.

### Formatting (currency/date) is a separate concern

Number/currency/date **display formatting** (thousands separator, decimal places, currency symbol, date format) is handled by `App\Services\FormatService` (facade: `App\Support\Facades\Format`, global helpers: `format_money()`, `format_number()`, `format_date()`), sourced from the `formatting` settings group — not by `__()`. Use `Format::money()` / `format_money()` etc. anywhere a monetary amount or date is displayed (views, PDFs/print, Blade), instead of a raw `number_format()` or `->format(...)` call, so every screen renders values identically.
