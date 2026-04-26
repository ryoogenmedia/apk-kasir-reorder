<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\Product;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Penjualan', Order::count())
                ->description('Jumlah transaksi selesai')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            Stat::make('Total Pendapatan', 'Rp ' . number_format(Order::sum('total_amount'), 0, ',', '.'))
                ->description('Total uang masuk')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Produk Stok Rendah', Product::whereColumn('stock', '<=', 'low_stock_threshold')->count())
                ->description('Perlu reorder segera')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
