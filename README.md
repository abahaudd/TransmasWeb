# LaravelStarter

A foundation application built on **Laravel 12 + Filament 4**. It ships with the plumbing every
application needs — authentication, roles & permissions, site settings, audit logging,
notifications, dashboards, backups, health checks, and a block-based CMS — so feature modules
can be dropped on top without re-building the basics.

This instance (**fintrak**) uses that foundation to build a personal finance application; the
first feature module on top of it is **Investment tracking** — stock/ETF holdings, transaction
history, and brokerage CSV import — described below.

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
| **Investment tracking** | Brokers, accounts, symbols, transactions, and computed holdings; brokerage CSV import with per-broker column/code mapping; portfolio dashboard widgets — see [Investment tracking](#investment-tracking) below |
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

## Investment tracking

Located at `app/Modules/Investment` and registered like any other module (see
[Building a module](#building-a-module)). Everything lives under the **Investments**
navigation group in the admin panel.

**Data model** (`app/Modules/Investment/Models`):

| Model | Purpose |
|---|---|
| `Broker` | A brokerage firm, owned by a user (e.g. Robinhood, Fidelity) |
| `Account` | An account held at a broker (currency, opened/closed dates) |
| `Symbol` | A tradable security (ticker, exchange, asset type); auto-created on first reference |
| `Transaction` | The ledger — buy, sell, dividend, interest, deposit, withdrawal, fee, transfer in/out |
| `Holding` | **Computed**, not hand-edited: current quantity/cost basis per account+symbol, rebuilt automatically from `Transaction` history (weighted-average cost) whenever a transaction is saved or deleted |
| `ImportProfile` | Per-broker CSV mapping: source column → canonical field, and source transaction code → canonical type |
| `ImportBatch` | Audit log of a CSV import run: row counts and any per-row errors |

Brokers, Accounts, Symbols, Transactions, and Import Profiles have full CRUD screens. Holdings
and Import Batches are List/View only, since both are system-generated records.

**Holdings math** (`Support\HoldingsRecalculator`, triggered by `Observers\TransactionObserver`):
`buy` and `transfer_in` add quantity and cost; `sell` and `transfer_out` remove quantity at the
current average cost. Other transaction types are cash-only and don't move a position.

**CSV import** (`Support\CsvTransactionImporter`, triggered from the **Import CSV** action on
the Transactions list): parses a brokerage export according to the selected `ImportProfile`,
handling accounting-style currency formatting (`($1,234.56)` = negative) and per-code type
mapping — including transaction codes whose meaning depends on the amount's sign (e.g. Robinhood's
`ACH` row is a deposit or withdrawal depending on whether the amount is positive or negative).
Rows are de-duplicated by content hash, so re-importing the same file is a no-op. Unrecognized
transaction codes are skipped and recorded as an error on the `ImportBatch` rather than guessed.
A **Robinhood** profile ships pre-configured; add more via **Investments → Import Profiles** — no
code changes needed for a new brokerage's column layout.

**Dashboard widgets**: portfolio value / cost basis / gain-loss, allocation by asset type, and
recent transactions.

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
