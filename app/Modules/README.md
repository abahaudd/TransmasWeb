# Modules

This foundation application is extended by dropping feature modules into `app/Modules/<Name>`.
Everything a module needs — routing, persistence, admin UI, permissions — plugs into
services the foundation already provides (auth via Filament + Shield, settings via
spatie/laravel-settings, auditing via spatie/laravel-activitylog, database notifications,
role-based dashboard widgets).

## Module layout

```
app/Modules/Blog/
├── BlogServiceProvider.php      # extends App\Support\ModuleServiceProvider
├── Models/                      # Eloquent models (use App\Models\Concerns\LogsModelActivity for auditing)
├── Filament/
│   ├── Resources/               # auto-discovered by the admin panel
│   ├── Pages/                   # auto-discovered by the admin panel
│   └── Widgets/                 # auto-discovered; use canView() for role-based dashboards
├── Database/
│   └── Migrations/              # auto-loaded by the module provider
├── Routes/
│   ├── web.php                  # auto-loaded if present
│   └── api.php                  # auto-loaded if present
├── Resources/
│   ├── views/                   # registered under the kebab-case module alias, e.g. view('blog::index')
│   └── lang/                    # registered under the same alias
└── Settings/                    # optional spatie/laravel-settings classes (register in config/settings.php)
```

## Creating a module

1. Create `app/Modules/<Name>/` with the folders you need (all are optional).
2. Add a provider:

```php
namespace App\Modules\Blog;

use App\Support\ModuleServiceProvider;

class BlogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Blog';
}
```

3. Register it in `bootstrap/providers.php`.
4. Run `php artisan migrate` to apply the module's migrations.
5. Run `php artisan shield:generate --all --panel=admin` so Shield creates
   permissions for the module's Filament resources/pages/widgets, then assign
   them to roles under Control Panel → Roles.

## Conventions

- **Auditing**: use the `App\Models\Concerns\LogsModelActivity` trait on models that
  should appear in the Activity Logs screen.
- **Dashboard**: expose module KPIs as Filament widgets; gate them with `canView()`
  (and/or Shield widget permissions) so each role sees its own dashboard.
- **Notifications**: send Filament database notifications
  (`Notification::make()->...->sendToDatabase($user)`) — they appear under the
  bell icon in the panel top bar.
- **Settings**: module settings go in a `Settings` class registered in
  `config/settings.php` with a settings migration in `database/settings`,
  plus a `SettingsPage` under `Filament/Pages` for editing.
