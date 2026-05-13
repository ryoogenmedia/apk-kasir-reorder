@php
    $logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value;
    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
    $isLogin = request()->routeIs('filament.admin.auth.login');
@endphp

@if ($logoUrl)
    @if ($isLogin)
        {{-- Login page: logo positioned above card --}}
        <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <img src="{{ $logoUrl }}" 
                 alt="Logo" 
                 style="height: 100px; width: auto; max-width: 200px; object-fit: contain; border-radius: 12px;">
        </div>
    @else
        {{-- Sidebar: compact logo --}}
        <div style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 10px 8px;">
            <img src="{{ $logoUrl }}" 
                 alt="Logo" 
                 style="height: 48px; width: auto; max-width: 100%; object-fit: contain; border-radius: 8px;">
        </div>
    @endif
@else
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 10px 8px;">
        <div style="font-size: {{ $isLogin ? '1.5rem' : '0.95rem' }}; font-weight: 800; color: #a78bfa; letter-spacing: -0.02em; line-height: 1.2; text-align: center;">
            Tokonudhin & Hj Lina
        </div>
        <div style="font-size: {{ $isLogin ? '0.85rem' : '0.6rem' }}; color: #c4b5fd; font-weight: 500; margin-top: 2px; text-align: center;">
            Aneka Barang Campuran
        </div>
    </div>
@endif
