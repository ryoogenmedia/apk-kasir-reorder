<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('cashier')) {
            $totalOrders = Order::count();
            $totalRevenue = Order::sum('total_amount');

            $weeklySalesQuery = Order::query()
                ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->select(DB::raw('DATE(order_date) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->get();

            $weeklySales = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $row = $weeklySalesQuery->firstWhere('date', $date);
                $weeklySales[] = $row->count ?? 0;
            }

            $todaySales = end($weeklySales);
            $yesterdaySales = $weeklySales[5] ?? 0;
            $salesTrend = $yesterdaySales > 0 ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100) : ($todaySales > 0 ? 100 : 0);

            $stockStats = Product::query()
                ->selectRaw('SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock, SUM(CASE WHEN stock > 0 AND stock <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
                ->first();
            $outOfStock = $stockStats->out_of_stock ?? 0;
            $lowStock = $stockStats->low_stock ?? 0;

            return [
                Stat::make('Total Penjualan (Qty)', $totalOrders)
                    ->description($salesTrend >= 0 ? "Naik {$salesTrend}%" : "Turun " . abs($salesTrend) . "%")
                    ->chart($weeklySales)
                    ->color($salesTrend >= 0 ? 'success' : 'danger'),

                Stat::make('Total Pendapatan Penjualan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                    ->color('info'),

                Stat::make('Stok Menipis', $lowStock)
                    ->color($lowStock > 0 ? 'warning' : 'success'),

                Stat::make('Stok Habis', $outOfStock)
                    ->color($outOfStock > 0 ? 'danger' : 'success'),
            ];
        }

        // Stats for Owner, Admin, Super Admin
        $totalPemasukan = (float) Order::sum('total_amount');
        $totalPengeluaran = (float) Purchase::sum('total_amount');
        $balance = $totalPemasukan - $totalPengeluaran;

        $stockStats = Product::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock, SUM(CASE WHEN stock > 0 AND stock <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
            ->first();
        $totalProducts = $stockStats->total ?? 0;
        $outOfStock = $stockStats->out_of_stock ?? 0;
        $lowStock = $stockStats->low_stock ?? 0;

        $weeklyPemasukanQuery = Order::query()
            ->where('order_date', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(total_amount) as amount'))
            ->groupBy('date')
            ->get();

        $weeklyPengeluaranQuery = Purchase::query()
            ->where('purchase_date', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(purchase_date) as date'), DB::raw('SUM(total_amount) as amount'))
            ->groupBy('date')
            ->get();

        $weeklyPemasukan = [];
        $weeklyPengeluaran = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $p = $weeklyPemasukanQuery->firstWhere('date', $date);
            $weeklyPemasukan[] = (int) ($p->amount ?? 0);

            $pr = $weeklyPengeluaranQuery->firstWhere('date', $date);
            $weeklyPengeluaran[] = (int) ($pr->amount ?? 0);
        }

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan, 0, ',', '.'))
                ->chart($weeklyPemasukan)
                ->color('success'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'))
                ->chart($weeklyPengeluaran)
                ->color('danger'),

            Stat::make('Balance (Net)', 'Rp ' . number_format($balance, 0, ',', '.'))
                ->description($balance >= 0 ? 'Surplus Keuangan' : 'Defisit Keuangan')
                ->color($balance >= 0 ? 'info' : 'warning'),

            Stat::make('Total Produk', $totalProducts)
                ->color('gray'),

            Stat::make('Stok Menipis', $lowStock)
                ->color($lowStock > 0 ? 'warning' : 'success'),

            Stat::make('Stok Habis', $outOfStock)
                ->color($outOfStock > 0 ? 'danger' : 'success'),
        ];
    }
    public function placeholder(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.stats-skeleton');
    }
}

