@php
    $logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value;
    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
    // Cek apakah di halaman login
    $isLogin = request()->routeIs('filament.admin.auth.login');
@endphp

@if ($logoUrl)
    <div class="flex justify-center w-full py-2">
        <img src="{{ $logoUrl }}" 
             alt="Logo" 
             class="{{ $isLogin ? 'h-32' : 'h-10' }} w-auto max-w-full object-contain transition-all duration-300">
    </div>
@else
    <div class="flex justify-center w-full py-2">
        <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
            {{ \App\Models\Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir' }}
        </span>
    </div>
@endif
