<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource;
use Filament\Widgets\FilamentInfoWidget;
use App\Filament\Admin\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $websiteConfigs = DB::table('website_configs')
        ->select('var_name', 'var_value')
        ->get()
        ->pluck('var_value', 'var_name')
        ->toArray();
        

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->userMenuItems([
                MenuItem::make()
                ->label('Go to Busstop')
                ->url('/Busstop')
                ->icon('heroicon-o-arrow-down-on-square-stack'),
            ])
            ->brandName($websiteConfigs['site_name'])
            ->brandLogo(asset('storage/branding/logo.png'))
            ->darkModeBrandLogo(asset('storage/branding/logo_dark.png'))
            ->brandLogoHeight('40px')
            ->favicon(asset('storage/branding/favicon.png'))
            ->darkMode($websiteConfigs['site_dark_mode'] ?? false)
            ->colors([
                'primary' =>  $websiteConfigs['site_brand_color_primary'] ?? Color::Amber,
                'secondary' => $websiteConfigs['site_brand_color_secondary'] ?? Color::Gray,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverResources(app_path('Filament/User/Resources'), 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverPages(app_path('Filament/User/Pages'), 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\DashboardInfoWidget::class,
                Widgets\AccountWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ]);
    }
}
