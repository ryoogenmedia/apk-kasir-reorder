@php
    $logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value;
    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
@endphp

@if ($logoUrl)
    <div class="flex justify-center w-full py-2">
        <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-auto max-w-full object-contain">
    </div>
@else
    <div class="flex justify-center w-full py-2">
        <span class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
            {{ \App\Models\Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir' }}
        </span>
    </div>
@endif
