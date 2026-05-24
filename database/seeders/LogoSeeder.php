<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class LogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default shop settings
        Setting::updateOrCreate(['key' => 'shop_name'], ['value' => 'Toko Udin dan Hj Lina']);
        Setting::updateOrCreate(['key' => 'shop_address'], ['value' => 'Jl.mesjid nurul iman no.08']);
        Setting::updateOrCreate(['key' => 'shop_phone'], ['value' => '081342763816']);

        // Pastikan folder branding ada
        if (!Storage::disk('public')->exists('branding')) {
            Storage::disk('public')->makeDirectory('branding');
        }

        // Copy Logo
        $logoSource = public_path('gambar-logo/logo.png');
        if (file_exists($logoSource)) {
            $logoName = 'logo_' . time() . '.png';
            Storage::disk('public')->put('branding/' . $logoName, file_get_contents($logoSource));
            
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => 'branding/' . $logoName]
            );
            $this->command->info("Logo berhasil di-seed.");
        } else {
            $this->command->warn("File logo.png tidak ditemukan di public/gambar-logo/");
        }

        // Copy Favicon
        $faviconSource = public_path('gambar-logo/favicon.png');
        if (file_exists($faviconSource)) {
            $faviconName = 'favicon_' . time() . '.png';
            Storage::disk('public')->put('branding/' . $faviconName, file_get_contents($faviconSource));
            
            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => 'branding/' . $faviconName]
            );
            $this->command->info("Favicon berhasil di-seed.");
        } else {
            $this->command->warn("File favicon.png tidak ditemukan di public/gambar-logo/");
        }

        // Clear cache
        cache()->forget('panel_settings_array_v2');
        cache()->forget('site_logo_url_cached');
    }
}
