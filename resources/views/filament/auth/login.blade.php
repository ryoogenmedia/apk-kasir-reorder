<div class="flex min-h-screen bg-gray-50 items-center justify-center p-4">
    <div class="w-full max-w-[440px]">
        <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
            <div class="p-10">
                <div class="flex flex-col items-center mb-10">
                    @php
                        $logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value;
                        $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($logo) : null;
                        $siteName = \App\Models\Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir';
                    @endphp
                    
                    @if($logoUrl)
                        <div class="w-24 h-24 bg-gray-50 rounded-2xl flex items-center justify-center mb-6 border border-gray-100 p-4 shadow-sm">
                            <img src="{{ $logoUrl }}" alt="Logo" class="max-h-full max-w-full object-contain">
                        </div>
                    @else
                        <div class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-100">
                            <span class="text-white text-3xl font-bold italic">{{ substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif
                    
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                        Selamat Datang
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 font-medium">
                        Silakan masuk ke panel admin Anda
                    </p>
                </div>

                <form wire:submit="authenticate" class="space-y-6">
                    <div>
                        {{ $this->form }}
                    </div>

                    <div class="pt-2">
                        {{ $this->getAuthenticateFormAction() }}
                    </div>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-[11px] uppercase tracking-[0.2em] font-bold text-gray-400">
                        &copy; {{ date('Y') }} {{ $siteName }} &bull; POS System
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Sembunyikan header bawaan Filament */
        .fi-simple-header { display: none !important; }
        
        /* Buat container utama Filament menjadi transparan agar desain kita terlihat bersih */
        .fi-simple-main {
            background-color: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            border: none !important;
        }

        /* Pastikan background mencakup seluruh layar */
        body {
            background-color: #f9fafb !important;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%) !important;
            background-attachment: fixed !important;
        }

        /* Styling Form Premium */
        .fi-input-wrp {
            border-radius: 12px !important;
            border-color: #f3f4f6 !important;
            background-color: #f9fafb !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
        }
        .fi-input-wrp:focus-within {
            background-color: #fff !important;
            border-color: #6366f1 !important;
            ring: 4px rgba(99, 102, 241, 0.1) !important;
        }
        
        /* Styling Button Masuk */
        .fi-btn {
            background-color: #4f46e5 !important;
            border-radius: 14px !important;
            height: 52px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4) !important;
            background-color: #4338ca !important;
        }
        
        /* Styling Label */
        .fi-fo-field-wrp-label label {
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 6px !important;
            font-size: 13px !important;
        }
    </style>
</div>
