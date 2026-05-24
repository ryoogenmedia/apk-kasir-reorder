<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StockTrendChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '120s';

    public ?string $filter = 'all';

    public function getHeading(): ?string
    {
        $activeFilter = $this->filter;
        if (!$activeFilter || $activeFilter === 'all') {
            return 'Grafik Tren Stok Menipis & Habis (30 Hari Terakhir)';
        }

        [$year, $month] = explode('-', $activeFilter);
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return 'Grafik Tren Stok Menipis & Habis - ' . ($monthNames[(int)$month] ?? '') . ' ' . $year;
    }

    protected function getFilters(): ?array
    {
        $months = \App\Models\Order::query()
            ->selectRaw('DISTINCT YEAR(order_date) as year, MONTH(order_date) as month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $options = ['all' => 'Semua Waktu (30 Hari Terakhir)'];
        
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        foreach ($months as $row) {
            $key = sprintf('%04d-%02d', $row->year, $row->month);
            $options[$key] = $monthNames[$row->month] . ' ' . $row->year;
        }

        if (count($options) === 1) {
            $year = date('Y');
            $month = (int) date('m');
            $options[sprintf('%04d-%02d', $year, $month)] = $monthNames[$month] . ' ' . $year;
        }

        return $options;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $cacheKey = 'stock_trend_chart_filter_' . ($activeFilter ?? 'all');

        return Cache::remember($cacheKey, 120, function () use ($activeFilter) {
            // Get all products with their current stock and low stock threshold
            $products = Product::select('id', 'stock', 'low_stock_threshold')->get();

            if (!$activeFilter || $activeFilter === 'all') {
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $daysCount = 30;
            } else {
                [$year, $month] = explode('-', $activeFilter);
                $startDate = Carbon::create($year, $month, 1)->startOfDay();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                $daysCount = $startDate->daysInMonth;
            }

            // Setup dates list for the selected period
            $dates = [];
            for ($i = 0; $i < $daysCount; $i++) {
                if (!$activeFilter || $activeFilter === 'all') {
                    $dates[$i] = Carbon::now()->subDays($daysCount - 1 - $i)->format('Y-m-d');
                } else {
                    $dates[$i] = $startDate->copy()->addDays($i)->format('Y-m-d');
                }
            }

            // Get order items quantity sold in the selected period per product per day
            $orderItems = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.order_date', [$startDate, $endDate])
                ->select('order_items.product_id', 'order_items.quantity', DB::raw('DATE(orders.order_date) as date'))
                ->get()
                ->groupBy('product_id');

            // Get purchase items quantity bought in the selected period per product per day
            $purchaseItems = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->whereBetween('purchases.purchase_date', [$startDate, $endDate])
                ->select('purchase_items.product_id', 'purchase_items.quantity', DB::raw('DATE(purchases.purchase_date) as date'))
                ->get()
                ->groupBy('product_id');

            // Rollback stock calculations for past month filters
            $salesAfter = collect();
            $purchasesAfter = collect();

            if ($activeFilter && $activeFilter !== 'all') {
                $salesAfter = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.order_date', '>', $endDate)
                    ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total'))
                    ->groupBy('order_items.product_id')
                    ->pluck('total', 'product_id');

                $purchasesAfter = DB::table('purchase_items')
                    ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                    ->where('purchases.purchase_date', '>', $endDate)
                    ->select('purchase_items.product_id', DB::raw('SUM(purchase_items.quantity) as total'))
                    ->groupBy('purchase_items.product_id')
                    ->pluck('total', 'product_id');
            }

            // Initialize data counts
            $menipisCounts = array_fill(0, $daysCount, 0);
            $habisCounts = array_fill(0, $daysCount, 0);

            foreach ($products as $product) {
                $productId = $product->id;
                $threshold = $product->low_stock_threshold;
                
                $productOrders = isset($orderItems[$productId]) ? $orderItems[$productId]->groupBy('date') : collect();
                $productPurchases = isset($purchaseItems[$productId]) ? $purchaseItems[$productId]->groupBy('date') : collect();
                
                // Starting stock level at the end of the period
                if (!$activeFilter || $activeFilter === 'all') {
                    $currentStock = $product->stock;
                } else {
                    $sa = (int) ($salesAfter[$productId] ?? 0);
                    $pa = (int) ($purchasesAfter[$productId] ?? 0);
                    $currentStock = max(0, $product->stock + $sa - $pa);
                }

                $stockOnDay = [];
                $stockOnDay[$daysCount - 1] = $currentStock;
                
                // Calculate stock levels backwards
                for ($i = $daysCount - 1; $i > 0; $i--) {
                    $date = $dates[$i];
                    
                    $sold = isset($productOrders[$date]) ? $productOrders[$date]->sum('quantity') : 0;
                    $purchased = isset($productPurchases[$date]) ? $productPurchases[$date]->sum('quantity') : 0;
                    
                    $currentStock = $currentStock + $sold - $purchased;
                    if ($currentStock < 0) {
                        $currentStock = 0;
                    }
                    $stockOnDay[$i - 1] = $currentStock;
                }
                
                // Count menipis vs habis per day
                for ($i = 0; $i < $daysCount; $i++) {
                    $stock = $stockOnDay[$i];
                    if ($stock == 0) {
                        $habisCounts[$i]++;
                    } elseif ($stock <= $threshold) {
                        $menipisCounts[$i]++;
                    }
                }
            }

            // Map dates to labels
            $labels = array_map(fn($date) => Carbon::parse($date)->format('d M'), $dates);

            return [
                'datasets' => [
                    [
                        'label' => 'Stok Menipis',
                        'data' => $menipisCounts,
                        'borderColor' => '#f59e0b',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'fill' => false,
                        'tension' => 0.4,
                        'borderWidth' => 3,
                    ],
                    [
                        'label' => 'Stok Habis',
                        'data' => $habisCounts,
                        'borderColor' => '#ef4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                        'fill' => false,
                        'tension' => 0.4,
                        'borderWidth' => 3,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
    public function placeholder(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.skeleton');
    }
}

