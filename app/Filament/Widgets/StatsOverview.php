<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $data = Cache::remember('dashboard_stats_data_final', 60, function () {
            $weeklySalesQuery = Order::query()
                ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->select(DB::raw('DATE(order_date) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
                ->groupBy('date')
                ->get();

            $weeklySales = [];
            $weeklyRevenue = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $row = $weeklySalesQuery->firstWhere('date', $date);
                $weeklySales[] = $row->count ?? 0;
                $weeklyRevenue[] = (int) ($row->revenue ?? 0);
            }

            $todaySales = end($weeklySales);
            $yesterdaySales = $weeklySales[5] ?? 0;
            $salesTrend = $yesterdaySales > 0 ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100) : ($todaySales > 0 ? 100 : 0);

            $stockStats = Product::query()
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock, SUM(CASE WHEN stock > 0 AND stock <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
                ->first();

            return [
                'weeklySales' => $weeklySales,
                'weeklyRevenue' => $weeklyRevenue,
                'salesTrend' => $salesTrend,
                'totalOrders' => Order::count(),
                'totalRevenue' => Order::sum('total_amount'),
                'categoryCount' => Category::count(),
                'stockStats' => $stockStats->toArray(),
            ];
        });

        return [
            Stat::make('Total Penjualan', $data['totalOrders'])
                ->description($data['salesTrend'] >= 0 ? "Naik {$data['salesTrend']}%" : "Turun " . abs($data['salesTrend']) . "%")
                ->chart($data['weeklySales'])
                ->color($data['salesTrend'] >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pendapatan', 'Rp ' . number_format($data['totalRevenue'], 0, ',', '.'))
                ->chart($data['weeklyRevenue'])
                ->color('info'),

            Stat::make('Total Produk', $data['stockStats']['total'])
                ->description($data['categoryCount'] . ' kategori')
                ->color('warning'),

            Stat::make('Stok Rendah', ($data['stockStats']['low_stock'] ?? 0) + ($data['stockStats']['out_of_stock'] ?? 0))
                ->description(($data['stockStats']['out_of_stock'] ?? 0) . ' habis')
                ->color('danger'),
        ];
    }
}
