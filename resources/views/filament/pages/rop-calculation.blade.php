<x-filament-panels::page>
    {{-- Rentang Tanggal & Bulan Filter Form --}}
    <form class="mb-6">
        {{ $this->form }}
    </form>

    {{-- Ringkasan Pendapatan Cards --}}
    @php
        $stats = $this->getStats();
        $isNegative = $stats['net_income'] < 0;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        {{-- Card 1: Pendapatan Penjualan --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div class="flex flex-col gap-1">
                <p class="text-xs font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">Pendapatan Penjualan</p>
                <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500">Total pemasukan dari penjualan (kasir)</span>
            </div>
            <div class="w-14 h-14 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 flex items-center justify-center text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.75rem; height: 1.75rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Card 2: Pendapatan Bersih --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
            <div class="flex flex-col gap-1">
                <p class="text-xs font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">Pendapatan Bersih</p>
                <h3 class="text-3xl font-black {{ $isNegative ? 'text-danger-600 dark:text-danger-400' : 'text-primary-600 dark:text-primary-400' }}">
                    Rp {{ number_format($stats['net_income'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] font-semibold text-gray-450 dark:text-gray-500">
                    {{ $isNegative ? 'Defisit Keuangan' : 'Surplus Keuangan' }} (Pendapatan Penjualan − Pengeluaran Pembelian)
                </span>
            </div>
            <div class="w-14 h-14 rounded-xl {{ $isNegative ? 'bg-danger-50 dark:bg-danger-950/20 text-danger-650' : 'bg-primary-50 dark:bg-primary-950/20 text-primary-600' }} flex items-center justify-center border {{ $isNegative ? 'border-danger-100 dark:border-danger-900' : 'border-primary-100 dark:border-primary-900' }}">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.75rem; height: 1.75rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

    </div>

    {{-- Render ROP Page Header Widgets --}}
    @if ($this->getHeaderWidgets())
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getColumns()"
        />
    @endif
</x-filament-panels::page>
