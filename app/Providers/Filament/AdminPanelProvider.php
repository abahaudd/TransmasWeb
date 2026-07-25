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
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
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
            ->colors(function (): array {
                $theme = app(SettingsService::class)->getGroup('theme');

                return [
                    'primary' => Color::hex($theme['primary_color'] ?? '#2CA58D'),
                    'gray' => Color::hex($theme['gray_color'] ?? '#6B7280'),
                    'success' => Color::hex($theme['success_color'] ?? '#16A34A'),
                    'warning' => Color::hex($theme['warning_color'] ?? '#F2B33D'),
                    'danger' => Color::hex($theme['danger_color'] ?? '#E8735A'),
                    'info' => Color::hex($theme['info_color'] ?? '#8B7FD1'),
                ];
            })
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(self::themeOverrideStyles()),
            )
            ->navigationGroups([
                NavigationGroup::make(__('labels.nav.groups.cms'))->icon('heroicon-o-newspaper'),
                NavigationGroup::make(__('labels.nav.groups.customer_management'))->icon('heroicon-o-user-group'),
                NavigationGroup::make(__('labels.nav.groups.hr_management'))->icon('heroicon-o-briefcase'),
                NavigationGroup::make(__('labels.nav.groups.service_catalog'))->icon('heroicon-o-clipboard-document-list'),
                NavigationGroup::make(__('labels.nav.groups.control_panel'))->icon('heroicon-o-adjustments-horizontal'),
                NavigationGroup::make(__('labels.nav.groups.administration'))->icon('heroicon-o-shield-check'),
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
     * Small CSS overrides for things Filament's ->colors() palette can't
     * reach directly — sourced from the "theme" settings group, never
     * hardcoded here (only as a last-resort fallback default).
     *
     * - Filament derives the page background from an auto-generated
     *   "gray-50" shade, which stays near-white regardless of the base hue,
     *   so it's driven by its own dedicated `page_background_color` setting
     *   instead of `gray_color` (which only feeds the generated gray
     *   palette — borders, muted text, etc.) — tuning one must not move
     *   the other.
     * - Sidebar nav group labels (e.g. "Customer Management") ship hardcoded
     *   to Filament's gray palette (`.fi-sidebar-group-label`); overridden
     *   here to use the configured primary color instead.
     * - `.fi-sidebar` ships transparent on desktop (it lets `.fi-body` show
     *   through), so `menu_background_color` gives it an explicit,
     *   independently controllable background instead of just inheriting
     *   the page background.
     * - `card_background_color` covers every "card" surface Filament ships
     *   hardcoded to white: section/infolist panels, table containers, and
     *   stat widgets.
     * - Sidebar nav items: the active item is overridden from Filament's
     *   default (primary-colored text on a faint gray pill) to a solid
     *   primary-colored pill with white icon/text. Inactive icons are
     *   overridden to the *raw* `gray_color` hex directly — Filament's
     *   `Color::hex()` palette generator only extracts the hue from the
     *   input and always applies its own fixed, fairly high chroma on top
     *   (~0.17 at shade 500, even for perfectly achromatic input like
     *   #000000), so any non-preset color fed through it reads as tinted
     *   (this is what caused the "violet" icons — gray_color's hue landed
     *   in the violet range). Going straight to the flat hex avoids that.
     */
    protected static function themeOverrideStyles(): string
    {
        $theme = app(SettingsService::class)->getGroup('theme');

        $pageBackground = e($theme['page_background_color'] ?? '#ccced3');
        $primary = e($theme['primary_color'] ?? '#2CA58D');
        $gray = e($theme['gray_color'] ?? '#6B7280');
        $menuBackground = e($theme['menu_background_color'] ?? '#ccced3');
        $cardBackground = e($theme['card_background_color'] ?? '#FFFFFF');

        return <<<CSS
            <style>
                .fi-body{background-color:{$pageBackground} !important}
                .fi-sidebar-group-label{color:{$primary} !important}
                .fi-sidebar{background-color:{$menuBackground} !important}
                .fi-section-content-ctn,.fi-ta-ctn,.fi-wi-stats-overview-stat{background-color:{$cardBackground} !important}
                .fi-sidebar-item-btn > .fi-icon{color:{$gray} !important}
                .fi-sidebar-item.fi-active > .fi-sidebar-item-btn{background-color:{$primary} !important}
                .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon{color:#FFFFFF !important}
                .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label{color:#FFFFFF !important}
            </style>
            CSS;
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
