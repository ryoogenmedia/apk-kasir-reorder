@php
    // Ambil data dari cache pengaturan utama
    $settings = cache('panel_settings_array_v2');
    $logo = $settings['site_logo'] ?? null;
    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
    $isLogin = request()->routeIs('filament.admin.auth.login');
@endphp

@if($logoUrl)
    @if($isLogin)
        <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <img src="{{ $logoUrl }}" alt="Logo" style="height: 80px; width: auto; border-radius: 12px; object-fit: contain;">
        </div>
    @else
        <div style="padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
            <img src="{{ $logoUrl }}" alt="Logo" style="height: 40px; width: auto; border-radius: 8px; object-fit: contain;">
        </div>
    @endif
@else
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">
            {{ substr(config('app.name'), 0, 1) }}
        </div>
        <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ config('app.name') }}
        </span>
    </div>
@endif
