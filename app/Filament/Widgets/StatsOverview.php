<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Polling interval — reduce from default 5s to 30s
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Cache stats for 60 seconds to avoid repeated queries
        return Cache::remember('dashboard_stats', 60, function () {
            // Single query for weekly sales + revenue (instead of 14 separate queries)
            $weeklyData = Order::query()
                ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->select(
                    DB::raw('DATE(order_date) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(total_amount), 0) as revenue')
                )
                ->groupBy(DB::raw('DATE(order_date)'))
                ->pluck('revenue', 'date')
                ->toArray();

            $weeklyCounts = Order::query()
                ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->select(
                    DB::raw('DATE(order_date) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('DATE(order_date)'))
                ->pluck('count', 'date')
                ->toArray();

            $weeklySales = [];
            $weeklyRevenue = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $weeklySales[] = $weeklyCounts[$date] ?? 0;
                $weeklyRevenue[] = (int) ($weeklyData[$date] ?? 0);
            }

            $todaySales = end($weeklySales);
            $yesterdaySales = $weeklySales[5] ?? 0;
            $salesTrend = $yesterdaySales > 0
                ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100)
                : ($todaySales > 0 ? 100 : 0);

            // Single query for product stock counts
            $stockStats = Product::query()
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock'),
                    DB::raw('SUM(CASE WHEN stock > 0 AND stock <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
                )
                ->first();

            $totalOrders = Order::count();
            $totalRevenue = Order::sum('total_amount');
            $categoryCount = Category::count();

            return [
                Stat::make('Total Penjualan', $totalOrders)
                    ->description($salesTrend >= 0 ? "Naik {$salesTrend}% dari kemarin" : "Turun " . abs($salesTrend) . "% dari kemarin")
                    ->descriptionIcon($salesTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->chart($weeklySales)
                    ->color($salesTrend >= 0 ? 'success' : 'danger'),

                Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                    ->description('Pendapatan 7 hari terakhir')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->chart($weeklyRevenue)
                    ->color('info'),

                Stat::make('Total Produk', $stockStats->total)
                    ->description($categoryCount . ' kategori')
                    ->descriptionIcon('heroicon-m-cube')
                    ->color('warning'),

                Stat::make('Stok Rendah', $stockStats->low_stock + $stockStats->out_of_stock)
                    ->description($stockStats->out_of_stock . ' produk habis stok')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        });
    }
}
