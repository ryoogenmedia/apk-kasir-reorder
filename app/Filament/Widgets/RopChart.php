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
                    'products.low_stock_threshold',
                ]);

            if ($activeFilter && $activeFilter !== 'all') {
                $query->where('products.category_id', $activeFilter);
            }

            $products = $query->limit(30)->get();

            $labels = [];
            $stocks = [];
            $lowStocks = [];

            foreach ($products as $product) {
                $labels[] = $product->name;
                $stocks[] = $product->stock;
                $lowStocks[] = $product->low_stock_threshold;
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
                        'label' => 'Batas Minimum Stok',
                        'data' => $lowStocks,
                        'backgroundColor' => '#f59e0b',
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
