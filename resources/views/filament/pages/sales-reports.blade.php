<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Filter Form --}}
        <div class="fi-fo-ctn">
            {{ $this->form }}
        </div>

        {{-- Stats Cards --}}
        @php
            $stats = $this->getStats();
        @endphp
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 8px;">
            {{-- Total Penjualan --}}
            <div style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Total Penjualan</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                </div>
            </div>

            {{-- Jumlah Transaksi --}}
            <div style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Jumlah Transaksi</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    {{ number_format($stats['transaction_count'], 0, ',', '.') }}
                </div>
            </div>

            {{-- Rata-rata Transaksi --}}
            <div style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Rata-rata Transaksi</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    Rp {{ number_format($stats['avg_transaction'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div style="width: 100%; margin-top: 12px;">
            <div class="fi-ta-ctn border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-sm" style="width: 100%;">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
