<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/rop.css') }}">

    {{-- Rentang Tanggal & Bulan Filter Form --}}
    <form class="mb-6">
        {{ $this->form }}
    </form>

    {{-- Ringkasan Pendapatan Cards --}}
    @php
        $stats = $this->getStats();
        $isNegative = $stats['net_income'] < 0;
    @endphp
    <div class="rop-stats-grid">
        
        {{-- Card 1: Pendapatan Penjualan --}}
        <div class="rop-stat-card">
            <div class="rop-stat-card-left">
                <p class="rop-stat-label">Pendapatan Penjualan</p>
                <h3 class="rop-stat-value val-revenue">
                    Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                </h3>
                <span class="rop-stat-desc">Total pemasukan dari penjualan (kasir)</span>
            </div>
            <div class="rop-stat-icon-wrap icon-revenue">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.75rem; height: 1.75rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Card 2: Pendapatan Bersih --}}
        <div class="rop-stat-card">
            <div class="rop-stat-card-left">
                <p class="rop-stat-label">Pendapatan Bersih</p>
                <h3 class="rop-stat-value {{ $isNegative ? 'val-deficit' : 'val-surplus' }}">
                    Rp {{ number_format($stats['net_income'], 0, ',', '.') }}
                </h3>
                <span class="rop-stat-desc">
                    {{ $isNegative ? 'Defisit Keuangan' : 'Surplus Keuangan' }} (Pendapatan Penjualan − Pengeluaran Pembelian)
                </span>
            </div>
            <div class="rop-stat-icon-wrap {{ $isNegative ? 'icon-net-deficit' : 'icon-net-surplus' }}">
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
