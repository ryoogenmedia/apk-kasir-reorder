<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        @php
            $stats = $this->getStats();
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section class="bg-primary-50 dark:bg-primary-900/10 border-primary-100 dark:border-primary-800">
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penjualan</span>
                    <span class="text-2xl font-bold tracking-tight text-primary-600 dark:text-primary-500">
                        Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}
                    </span>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Transaksi</span>
                    <span class="text-2xl font-bold tracking-tight">
                        {{ number_format($stats['transaction_count'], 0, ',', '.') }}
                    </span>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Transaksi</span>
                    <span class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">
                        Rp {{ number_format($stats['avg_transaction'], 0, ',', '.') }}
                    </span>
                </div>
            </x-filament::section>
        </div>

        {{-- Filter Form --}}
        <div class="fi-fo-ctn">
            {{ $this->form }}
        </div>

        {{-- Table --}}
        <div class="fi-ta-ctn border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
