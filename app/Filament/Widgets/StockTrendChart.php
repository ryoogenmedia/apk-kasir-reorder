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

    protected ?string $heading = 'Grafik Tren Stok Menipis & Habis (30 Hari Terakhir)';
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '120s';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    protected function getData(): array
    {
        return Cache::remember('stock_trend_chart_30d', 120, function () {
            // Get all products with their current stock and low stock threshold
            $products = Product::select('id', 'stock', 'low_stock_threshold')->get();

            // Setup dates list for the last 30 days
            $dates = [];
            for ($i = 29; $i >= 0; $i--) {
                $dates[29 - $i] = Carbon::now()->subDays($i)->format('Y-m-d');
            }

            // Get order items quantity sold in the last 30 days per product per day
            $orderItems = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.order_date', '>=', Carbon::now()->subDays(30)->startOfDay())
                ->select('order_items.product_id', 'order_items.quantity', DB::raw('DATE(orders.order_date) as date'))
                ->get()
                ->groupBy('product_id');

            // Get purchase items quantity bought in the last 30 days per product per day
            $purchaseItems = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->where('purchases.purchase_date', '>=', Carbon::now()->subDays(30)->startOfDay())
                ->select('purchase_items.product_id', 'purchase_items.quantity', DB::raw('DATE(purchases.purchase_date) as date'))
                ->get()
                ->groupBy('product_id');

            // Initialize data counts
            $menipisCounts = array_fill(0, 30, 0);
            $habisCounts = array_fill(0, 30, 0);

            foreach ($products as $product) {
                $productId = $product->id;
                $threshold = $product->low_stock_threshold;
                
                $productOrders = isset($orderItems[$productId]) ? $orderItems[$productId]->groupBy('date') : collect();
                $productPurchases = isset($purchaseItems[$productId]) ? $purchaseItems[$productId]->groupBy('date') : collect();
                
                $currentStock = $product->stock;
                $stockOnDay = [];
                $stockOnDay[29] = $currentStock;
                
                // Calculate stock levels backwards
                for ($i = 29; $i > 0; $i--) {
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
                for ($i = 0; $i < 30; $i++) {
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
}
