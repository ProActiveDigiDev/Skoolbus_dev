<?php

namespace App\Providers\Filament;

use Carbon\Carbon;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Widgets\BookingCalendarWidget;
use Filament\Navigation\NavigationItem;
use App\Filament\User\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $websiteConfigs = DB::table('website_configs')
        ->select('var_name', 'var_value')
        ->get()
        ->pluck('var_value', 'var_name')
        ->toArray();
        
        return $panel
            ->id('Busstop')
            ->path('Busstop')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->brandName('Skoolbus')
            ->brandLogo(asset('storage/branding/logo.png'))
            ->darkModeBrandLogo(asset('storage/branding/logo_dark.png'))
            ->brandLogoHeight('40px')
            ->favicon(asset('storage/branding/favicon.png'))
            ->colors([
                'primary' =>  $websiteConfigs['site_brand_color_primary'] ?? Color::Amber,
                'secondary' => $websiteConfigs['site_brand_color_secondary'] ?? Color::Gray,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->authGuard('web')
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                // Widgets\DashboardInfoWidget::class,
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
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                FilamentFullCalendarPlugin::make()
                    ->selectable(true)
                    ->editable()
                    ->timezone('Africa/Johannesburg')
                    ->config([
                        'height' => 'auto',
                        'timeFormat' => null,
                        'eventTextColor' => '#000',
                        'validRange' => ['start' => Carbon::now()->addDays(1)->format('Y-m-d')],
                        'editable' => false,
                    ])
            ]);
    }
}
