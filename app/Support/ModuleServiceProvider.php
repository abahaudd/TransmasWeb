<?php

namespace App\Support;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Base service provider for feature modules living in app/Modules/<Name>.
 *
 * A module extends this class, sets $name, registers the provider in
 * bootstrap/providers.php, and gets its routes, migrations, views and
 * translations loaded automatically. Filament resources, pages and widgets
 * under app/Modules/<Name>/Filament are discovered by the admin panel
 * (see AdminPanelProvider). See app/Modules/README.md for the layout.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /** The module's directory name under app/Modules, e.g. "Blog". */
    protected string $name;

    public function boot(): void
    {
        $path = app_path('Modules'.DIRECTORY_SEPARATOR.$this->name);
        $alias = Str::kebab($this->name);

        if (is_dir($path.'/Database/Migrations')) {
            $this->loadMigrationsFrom($path.'/Database/Migrations');
        }

        if (file_exists($path.'/Routes/web.php')) {
            $this->loadRoutesFrom($path.'/Routes/web.php');
        }

        if (file_exists($path.'/Routes/api.php')) {
            $this->loadRoutesFrom($path.'/Routes/api.php');
        }

        if (is_dir($path.'/Resources/views')) {
            $this->loadViewsFrom($path.'/Resources/views', $alias);
        }

        if (is_dir($path.'/Resources/lang')) {
            $this->loadTranslationsFrom($path.'/Resources/lang', $alias);
        }
    }
}
