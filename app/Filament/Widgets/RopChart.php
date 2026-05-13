<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RopChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Grafik ROP & Status Stok';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '60s';

    protected function getFilters(): ?array
    {
        $categories = Cache::remember('rop_chart_categories', 300, function () {
            return Category::pluck('name', 'id')->toArray();
        });
        return ['all' => 'Semua Kategori'] + $categories;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $cacheKey = 'rop_chart_data_' . ($activeFilter ?? 'all');

        return Cache::remember($cacheKey, 120, function () use ($activeFilter) {
            $query = Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.stock',
                    DB::raw('COALESCE(sold.total_qty, 0) as total_sold_30_days'),
                ])
                ->leftJoin(DB::raw('(
                    SELECT oi.product_id, SUM(oi.quantity) as total_qty
                    FROM order_items oi
                    INNER JOIN orders o ON o.id = oi.order_id
                    WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY oi.product_id
                ) as sold'), 'products.id', '=', 'sold.product_id');

            if ($activeFilter && $activeFilter !== 'all') {
                $query->where('products.category_id', $activeFilter);
            }

            $products = $query->limit(30)->get();

            $labels = [];
            $stocks = [];
            $suggestedRops = [];

            foreach ($products as $product) {
                $labels[] = $product->name;
                $stocks[] = $product->stock;

                $totalSold = $product->total_sold_30_days;
                $avgDailyDemand = $totalSold / 30;
                $leadTime = 3;
                $safetyStock = 5;

                $suggestedRops[] = (int) ceil(($avgDailyDemand * $leadTime) + $safetyStock);
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Stok Saat Ini',
                        'data' => $stocks,
                        'backgroundColor' => '#3b82f6',
                        'borderRadius' => 6,
                        'borderWidth' => 0,
                    ],
                    [
                        'label' => 'ROP Saran (Min. Stok)',
                        'data' => $suggestedRops,
                        'backgroundColor' => '#10b981',
                        'borderRadius' => 6,
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
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
                        'color' => 'rgba(156, 163, 175, 0.1)',
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
