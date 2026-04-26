<x-filament-panels::layout.base :livewire="$this">
    <div class="flex min-h-screen items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-indigo-50 via-white to-blue-50">
        <div class="w-full max-w-md bg-white p-10 rounded-3xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] border border-gray-100/50 backdrop-blur-sm">
            <div class="flex flex-col items-center mb-8">
                @php
                    $logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value;
                    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
                    $siteName = \App\Models\Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir';
                @endphp
                
                @if($logoUrl)
                    <div class="bg-gray-50 p-4 rounded-2xl mb-4 shadow-sm border border-gray-100">
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-20 w-auto object-contain">
                    </div>
                @else
                    <div class="h-20 w-20 bg-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-200">
                        <span class="text-white text-3xl font-bold">{{ substr($siteName, 0, 1) }}</span>
                    </div>
                @endif
                
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Masuk ke Sistem
                </h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">
                    Silakan masukkan email dan password
                </p>
            </div>

            <div class="mt-8">
                <x-filament-panels::form wire:submit="authenticate">
                    <div class="space-y-6">
                        {{ $this->form }}
                    </div>

                    <div class="mt-8">
                        {{ $this->getAuthenticateFormAction() }}
                    </div>
                </x-filament-panels::form>
            </div>

            <div class="mt-10 text-center">
                <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400">
                    &copy; {{ date('Y') }} {{ $siteName }}
                </p>
            </div>
        </div>
    </div>

    <style>
        .fi-auth-card {
            display: none !important;
        }
        /* Custom styling for the form elements to make them look more premium */
        .fi-input-wrp {
            border-radius: 12px !important;
            transition: all 0.2s ease-in-out;
        }
        .fi-input-wrp:focus-within {
            ring: 2px !important;
            ring-color: rgb(79, 70, 229) !important;
            border-color: rgb(79, 70, 229) !important;
        }
        .fi-btn {
            border-radius: 14px !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.025em !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06) !important;
        }
        .fi-btn:hover {
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2), 0 4px 6px -2px rgba(79, 70, 229, 0.05) !important;
            transform: translateY(-1px);
        }
    </style>
</x-filament-panels::layout.base>
