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
            {{-- Total Pemasukan --}}
            <div style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Total Pemasukan</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Total Pengeluaran</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    Rp {{ number_format($stats['total_purchases'], 0, ',', '.') }}
                </div>
            </div>

            {{-- Balance (Net) --}}
            <div style="background: linear-gradient(135deg, {{ $stats['balance'] >= 0 ? '#3b82f6' : '#f59e0b' }} 0%, {{ $stats['balance'] >= 0 ? '#60a5fa' : '#fbbf24' }} 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -10px; right: 20px; width: 50px; height: 50px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5" /></svg>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em;">Balance (Net)</span>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; position: relative; z-index: 1;">
                    Rp {{ number_format($stats['balance'], 0, ',', '.') }}
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
