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
        // Cache settings 10 menit (600 detik) agar perubahan logo lebih cepat terlihat
        $settings = cache()->remember('panel_settings_array_v2', 600, function () {
            try {
                return Setting::whereIn('key', ['site_logo', 'site_favicon', 'site_name'])->get()->pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        $faviconUrl = ($settings['site_favicon'] ?? null) ? Storage::disk('public')->url($settings['site_favicon']) : null;
        $siteName = $settings['site_name'] ?? 'Tokonudhin & Hj Lina';

        // Pastikan URL logo selalu ada di cache setiap kali provider ini dijalankan (jika cache kosong)
        if (!cache()->has('site_logo_url_cached') && isset($settings['site_logo'])) {
             cache()->put('site_logo_url_cached', Storage::disk('public')->url($settings['site_logo']), 3600);
        }
        $customCss = cache()->remember('filament_custom_css_content', 600, function () {
            $path = resource_path('css/filament-custom.css');
            return file_exists($path) ? file_get_contents($path) : '';
        });

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
            ->spa()
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Manajemen Produk'),
                NavigationGroup::make('Penjualan'),
                NavigationGroup::make('Settings')
                    ->collapsed(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('<style>' . $customCss . '</style>'),
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
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\SalesChart::class,
                \App\Filament\Widgets\CategorySalesChart::class,
                \App\Filament\Widgets\ProfitPercentageChart::class,
                \App\Filament\Widgets\StockTrendChart::class,
                \App\Filament\Widgets\LowStockAlertWidget::class,
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
