<?php

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\Login;
use App\Filament\Auth\Register;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\Stats\DashboardStats;
use App\Services\SettingsService;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ])
            ->databaseNotifications()
            ->brandName(fn () => app(SettingsService::class)->get('general', 'site_name', config('app.name')))
            ->brandLogo(fn () => app(SettingsService::class)->companyLogoUrl())
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationGroups([
                NavigationGroup::make('CMS')->icon('heroicon-o-newspaper'),
                NavigationGroup::make('Customer Management')->icon('heroicon-o-user-group'),
                NavigationGroup::make('HR Management')->icon('heroicon-o-briefcase'),
                NavigationGroup::make('Control Panel')->icon('heroicon-o-adjustments-horizontal'),
                NavigationGroup::make('Administration')->icon('heroicon-o-shield-check'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DashboardStats::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        return $this->discoverModules($panel);
    }

    /**
     * Auto-discover Filament resources, pages and widgets of every module
     * under app/Modules/<Name>/Filament. See app/Modules/README.md.
     */
    protected function discoverModules(Panel $panel): Panel
    {
        foreach (glob(app_path('Modules/*'), GLOB_ONLYDIR) ?: [] as $modulePath) {
            $moduleName = basename($modulePath);
            $namespace = "App\\Modules\\{$moduleName}\\Filament";

            if (is_dir("{$modulePath}/Filament/Resources")) {
                $panel->discoverResources(in: "{$modulePath}/Filament/Resources", for: "{$namespace}\\Resources");
            }

            if (is_dir("{$modulePath}/Filament/Pages")) {
                $panel->discoverPages(in: "{$modulePath}/Filament/Pages", for: "{$namespace}\\Pages");
            }

            if (is_dir("{$modulePath}/Filament/Widgets")) {
                $panel->discoverWidgets(in: "{$modulePath}/Filament/Widgets", for: "{$namespace}\\Widgets");
            }
        }

        return $panel;
    }
}
