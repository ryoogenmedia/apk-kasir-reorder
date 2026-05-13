@php
    // Ambil data dari cache yang sudah disiapkan di AdminPanelProvider
    $logoUrl = cache('site_logo_url_cached');
    $isLogin = request()->routeIs('filament.admin.auth.login');
@endphp

@if($logoUrl)
    @if($isLogin)
        <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <img src="{{ $logoUrl }}" alt="Logo" style="height: 80px; width: auto; border-radius: 12px;">
        </div>
    @else
        <div style="padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
            <img src="{{ $logoUrl }}" alt="Logo" style="height: 40px; width: auto; border-radius: 8px;">
        </div>
    @endif
@else
    <span class="text-xl font-bold tracking-tight text-indigo-600">
        {{ config('app.name') }}
    </span>
@endif
