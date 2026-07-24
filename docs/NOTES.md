git merge feature# Developer Notes

Working notes for the LaravelStarter foundation. Last updated: **2026-07-15**.

Original setup reference: https://www.youtube.com/watch?v=Fy0XCg1XBjQ
(functional administrative panels with Filament v4 inside a fresh Laravel 12 workspace).

---

## 1. Base installation (history)

The project was assembled roughly as follows — kept for reference; a fresh clone only
needs `composer run setup` (see README).

```bash
composer require filament/filament:"^4.0" bezhansalleh/filament-shield:"^4.0" -W
composer require spatie/laravel-medialibrary -W
composer require spatie/laravel-backup --ignore-platform-req=ext-pcntl -W   # pcntl is Linux-only
composer require spatie/laravel-activitylog spatie/laravel-health
composer require spatie/laravel-settings filament/spatie-laravel-settings-plugin

php artisan filament:install --panels
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan storage:link
php artisan migrate
npm install && npm run build
```

Notes:
- `opcodesio/log-viewer` was installed originally but **removed on 2026-07-15** (unused).
- Tailwind v4 is wired through `@tailwindcss/vite` in `vite.config.js`; `resources/css/app.css`
  uses `@import 'tailwindcss'` + `@source` globs over the Blade views.
- Local URLs: site at http://127.0.0.1:8000 — admin at http://127.0.0.1:8000/admin/login.

## 2. Architecture decisions

| Decision | Choice | Why |
|---|---|---|
| Module system | Plain `app/Modules/<Name>` convention (no nwidart) | No extra dependency; base `App\Support\ModuleServiceProvider` auto-loads routes/migrations/views/lang, and `AdminPanelProvider::discoverModules()` auto-discovers each module's Filament resources/pages/widgets |
| Settings | `spatie/laravel-settings` (typed classes) | The hand-rolled `Setting` model was removed; its table schema was already identical to spatie's. Settings classes: `GeneralSettings` (group `general`), `CompanySettings` (group `company`, incl. logo upload) |
| Audit log | `spatie/laravel-activitylog` | Opt-in per model via `App\Models\Concerns\LogsModelActivity`; `User` overrides options to log only `username`/`email` (never credentials/MFA secrets) |
| Roles | Shield + `Gate::before` super_admin bypass | `super_admin` implicitly passes every ability check (see `AppServiceProvider`) |
| Login identity | `username` column (unique) + email; display name = username | Custom `App\Filament\Auth\Login` accepts email **or** username; `User::getFilamentName()` returns username; Register/EditProfile pages override the name field |
| Panel access | `canAccessPanel()`: super_admin OR any role OR local env | Self-registered users receive `panel_user` in `App\Filament\Auth\Register::handleRegistration()` |
| Unused packages | Removed `opcodesio/log-viewer`; kept + wired backup & health | Decided 2026-07-15 |

## 3. Foundation wiring (how the pieces connect)

- **Auth features** are all panel config in `AdminPanelProvider`: `->login(Login::class)
  ->registration(Register::class) ->passwordReset() ->emailVerification()
  ->profile(EditProfile::class) ->multiFactorAuthentication([AppAuthentication::make()->recoverable()])
  ->databaseNotifications()`.
- **MFA storage**: `users.app_authentication_secret` (encrypted) and
  `users.app_authentication_recovery_codes` (encrypted:array), added by
  `2026_07_15_000001_add_mfa_columns_to_users_table`.
- **Notifications** go through the queue (`QUEUE_CONNECTION=database`) — a worker must run
  or the bell stays empty. `composer run dev` includes one.
- **Health checks** are registered in `AppServiceProvider::registerHealthChecks()`.
  Debug/Environment/OptimizedApp checks run only in production (they always fail locally).
  `UsedDiskSpaceCheck` is skipped on Windows (shells out to `df`).
- **Guest redirects** for non-panel `auth` routes (e.g. `/health`) go to `/admin/login`
  (`bootstrap/app.php` → `redirectGuestsTo`).
- **Schedules** live in `routes/console.php` (activitylog clean, backup clean/run,
  health check, health-history pruning).
- **Shield**: after adding resources/pages/widgets run
  `php artisan shield:generate --all --panel=admin` and assign the new permissions to roles.

## 4. CMS

- **Data model**: `pages` (title, slug, template, SEO fields, `is_published`, soft deletes)
  → hasMany `blocks` (type, name, JSON `data`, `position`, `is_active`).
- **Admin**: CMS → Pages resource (`app/Filament/Resources/CmsPages`); blocks are edited in
  a reorderable relation manager, with the block `data` payload as pretty-printed JSON.
- **Rendering**: `PageController` (`/` → slug `home`, `/{slug}` catch-all excluding
  admin/health/up/storage/livewire/vendor) → `CmsService` resolves the published page +
  active blocks → `cms/templates/{template}.blade.php` loops blocks and includes
  `cms/blocks/{type}.blade.php`. Unknown block types are skipped.
- **Front-end stack**: Blade + Tailwind 4 via Vite; header/footer partials under
  `resources/views/cms/partials/`; company identity/contact should come from `CompanySettings`.
- **Block types** (`Block::TYPES`): hero, stats, collections, steps, feature, testimonials,
  featured-products, clearance-rack, cta, rich_text, contact_form, faq.

## 5. Environment quirks (dev machine)

- **Norton AV intercepts HTTPS** — Composer is configured with a custom CA bundle at
  `%APPDATA%\Composer\cacert-with-norton.pem` (Norton root appended). If Composer SSL
  errors return, re-export "Norton Web/Mail Shield Root" from `Cert:\CurrentUser\Root`
  and rebuild that file.
- **Windows**: `UsedDiskSpaceCheck` unsupported (skipped in code); Pint/artisan run fine.
- **Long-running dev servers**: an old `php artisan serve` kept serving stale routes —
  restart `serve` after route/provider changes if behaviour looks stale.
- DB is **SQLite** (`database/database.sqlite`); the base users migration was edited
  (username column), so schema changes there require `php artisan migrate:fresh --seed`.

## 6. Known gaps / TODO

- [ ] **CmsPageSeeder still contains jewellery-company content** (GMJ Group). Pending task:
      rewrite home/about/policy pages to suit a software company.
- [ ] `resources/views/cms/partials/header.blade.php` references routes that are not
      defined in `routes/web.php` yet: `cms.account.profile`, `cms.account.password`,
      `cms.logout`, and `/admin/control-panel`; it also displays `$user->name`
      (column is now `username`) and a hard-coded logo/company name instead of
      `CompanySettings`. Views for the account pages exist (`cms/account/*.blade.php`)
      but have no routes/controllers.
- [ ] Notification **bell icon in the public site header** (next to the user icon) is not
      implemented yet — currently notifications appear only inside the admin panel.
- [ ] `App\Models\Cms\Enquiry` has **no migration** (`enquiries` table missing) and no
      admin resource; the `contact_form` block partial exists but posts nowhere.
- [ ] `Block::TYPES` lists product/e-commerce types (featured-products, clearance-rack,
      product sliders/cards) carried over from the previous project — decide whether they
      stay in the foundation or move to a future Catalog module.
- [ ] `welcome.blade.php` / `welcome_original.blade.php` are unused since `/` now renders
      the CMS home page — safe to delete once confirmed.
- [ ] Countries resource has no seeder (table is empty after a fresh migrate).
- [ ] Consider enabling the settings cache (`SETTINGS_CACHE_ENABLED=true`) once stable.

## 7. Useful commands

```bash
composer run dev                                   # server + queue + logs + vite
php artisan migrate:fresh --seed                   # rebuild DB (dev only!)
php artisan shield:generate --all --panel=admin    # regenerate permissions/policies
php artisan health:check --no-notification         # run health checks now
php artisan db:seed --class=CmsPageSeeder          # reseed CMS content only
./vendor/bin/pint --dirty                          # format changed files
```
