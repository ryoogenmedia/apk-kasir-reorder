<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Weekly sales trend for sparkline
        $weeklySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $weeklySales[] = Order::whereDate('order_date', Carbon::now()->subDays($i))->count();
        }

        // Weekly revenue trend
        $weeklyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $weeklyRevenue[] = (int) Order::whereDate('order_date', Carbon::now()->subDays($i))->sum('total_amount');
        }

        $todaySales = Order::whereDate('order_date', Carbon::today())->count();
        $yesterdaySales = Order::whereDate('order_date', Carbon::yesterday())->count();
        $salesTrend = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100) 
            : ($todaySales > 0 ? 100 : 0);

        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $outOfStockCount = Product::where('stock', 0)->count();

        return [
            Stat::make('Total Penjualan', Order::count())
                ->description($salesTrend >= 0 ? "Naik {$salesTrend}% dari kemarin" : "Turun " . abs($salesTrend) . "% dari kemarin")
                ->descriptionIcon($salesTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($weeklySales)
                ->color($salesTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pendapatan', 'Rp ' . number_format(Order::sum('total_amount'), 0, ',', '.'))
                ->description('Pendapatan 7 hari terakhir')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($weeklyRevenue)
                ->color('info'),

            Stat::make('Total Produk', Product::count())
                ->description(Category::count() . ' kategori')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),

            Stat::make('Stok Rendah', $lowStockCount)
                ->description($outOfStockCount . ' produk habis stok')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
