<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        try {
            $settings = Setting::whereIn('key', ['site_logo', 'site_favicon', 'site_name'])->get()->pluck('value', 'key');
        } catch (\Exception $e) {
            $settings = collect();
        }

        $logoUrl = $settings->get('site_logo') ? Storage::disk('public')->url($settings->get('site_logo')) : null;
        $faviconUrl = $settings->get('site_favicon') ? Storage::disk('public')->url($settings->get('site_favicon')) : null;
        $siteName = $settings->get('site_name') ?? 'Tokonudhin & Hj Lina';

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->profile()
            ->brandName($siteName)
            ->brandLogo(fn () => view('filament.logo'))
            ->favicon($faviconUrl)
            ->darkMode(false)
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Manajemen Produk'),
                NavigationGroup::make('Penjualan'),
                NavigationGroup::make('Settings')
                    ->collapsed(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('<style>' . file_get_contents(resource_path('css/filament-custom.css')) . '</style>'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => new HtmlString(view('filament.components.realtime-clock')->render()),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
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
    }
}
