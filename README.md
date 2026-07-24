# TransMasWeb

This is the Laravel versio of Transmas with MySQL as the DB.

## Feature overview

| Area | Implementation |
|---|---|
| Admin panel | Filament 4 at `/admin` |
| Authentication | Login (email **or** username), self-registration, password reset, email verification, profile page, authenticator-app MFA with recovery codes |
| Authorization | `spatie/laravel-permission` + Filament Shield (roles, permissions, policies, per-widget/page access) |
| Site settings | `spatie/laravel-settings` — typed settings classes with admin pages (Site Settings, Company Settings incl. logo upload) |
| Audit logging | `spatie/laravel-activitylog` — opt-in per model via the `LogsModelActivity` trait; read-only Activity Logs screen in the panel |
| Notifications | Filament database notifications (bell icon in the panel top bar), delivered through the queue |
| Dashboards | Role-based: widgets gate themselves with `canView()` and Shield widget permissions |
| CMS | Page + Block models, Filament resource for editing, block-partial Blade rendering on the public site |

| Media | `spatie/laravel-medialibrary` (avatars with thumbnail conversions) |
| Backups | `spatie/laravel-backup`, scheduled daily |
| Health | `spatie/laravel-health` — `/health` dashboard (signed-in users), checks scheduled every 5 minutes |

## Requirements

- PHP ^8.2 (project is developed on 8.3), Composer
- Node.js + npm (Vite 7, Tailwind CSS 4)
- SQLite (default) or MySQL

## Getting started

```bash
composer run setup     # composer install, .env, key, migrate, npm install + build
php artisan db:seed    # roles, permissions, super admin, CMS pages
php artisan storage:link
composer run dev       # serves app + queue worker + logs + vite, all in one
```

The default super admin is created by `AdminUserSeeder` from these `.env` values
(**change them before seeding anything real**):

```
ADMIN_NAME=Administrator
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=password
```

Sign in at `/admin`. Users log in with **email or username**.

After pulling in a new module (including Investment, if its permissions haven't been generated
in your environment yet), run:

```bash
php artisan shield:generate --all --panel=admin
```

then assign the new permissions to roles under **Control Panel → Roles**.

## Where things live

```
app/
├── Filament/               # Panel resources, pages, widgets, auth page overrides
│   ├── Auth/               # Login (email/username), Register, EditProfile
│   ├── Pages/              # ManageGeneralSettings, ManageCompanySettings
│   ├── Resources/          # Users, Roles, Permissions, Countries, ActivityLogs, CmsPages
│   └── Widgets/            # Role-gated dashboard widgets
├── Models/
│   ├── Cms/                # Page, Block, Enquiry
│   └── Concerns/           # LogsModelActivity (audit-trail trait)
├── Modules/                # Feature modules land here — see app/Modules/README.md
│   └── Investment/         # Brokers, accounts, symbols, transactions, holdings, CSV import
├── Services/               # CmsService (published-page resolution + template pick)
├── Settings/               # GeneralSettings, CompanySettings (spatie/laravel-settings)
└── Support/                # ModuleServiceProvider base class
database/
├── settings/               # Settings migrations (defaults for each settings group)
└── seeders/                # Role, Permission, AdminUser, CmsPage seeders
resources/views/cms/        # Public site: layout, header/footer partials, block partials
```

## The CMS

Pages are stored in the `pages` table and composed of ordered **blocks** (`blocks` table).
Each block has a `type` (hero, stats, steps, rich_text, faq, cta, …) and a JSON `data`
payload; each type maps to a Blade partial in `resources/views/cms/blocks/{type}.blade.php`.

- Manage pages and their blocks in the panel under **CMS → Pages** (blocks are a
  reorderable relation manager; block content is edited as JSON).
- Routing: `/` renders the page with slug `home`; any other published page is served at
  `/{slug}` by `PageController` via `CmsService`. System paths (`admin`, `health`, `up`,
  `storage`, `livewire`, `vendor`) are excluded from the catch-all.
- Templates: a page's `template` maps to `resources/views/cms/templates/{template}.blade.php`.
- Seed content comes from `CmsPageSeeder` (home, about, policy pages, and more).

Company identity shown on the public site (name, logo, address, phone, email, website) is
managed under **Control Panel → Company Settings**.

## Building a module

Modules are self-contained folders under `app/Modules/<Name>` — routes, migrations, models,
Filament resources, views. Extend `App\Support\ModuleServiceProvider`, register the provider
in `bootstrap/providers.php`, and the panel auto-discovers the module's Filament classes.
Full conventions: [app/Modules/README.md](app/Modules/README.md).

## Scheduled tasks

Defined in `routes/console.php` (run `php artisan schedule:work` locally):

| Time | Command |
|---|---|
| 01:00 daily | `activitylog:clean` |
| 01:30 daily | `backup:clean` |
| 02:00 daily | `backup:run` |
| every 5 min | `health:check` |
| daily | prune health-check result history |

## Testing & code style

```bash
composer test          # phpunit
./vendor/bin/pint      # code style (Laravel preset)
```

## Documentation

Developer notes, architecture decisions, and the current TODO list: [docs/NOTES.md](docs/NOTES.md).
