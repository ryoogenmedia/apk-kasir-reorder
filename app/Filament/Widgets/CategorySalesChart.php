<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class CategorySalesChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Penjualan Per Kategori';
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        return Cache::remember('category_sales_chart', 120, function () {
            $data = \App\Models\OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as category, SUM(order_items.subtotal) as total')
                ->groupBy('categories.name')
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Total (Rp)',
                        'data' => $data->pluck('total')->toArray(),
                        'backgroundColor' => [
                            '#6366f1', '#06b6d4', '#f59e0b', '#ef4444',
                            '#10b981', '#8b5cf6', '#ec4899', '#14b8a6',
                        ],
                        'borderWidth' => 0,
                        'hoverOffset' => 8,
                    ],
                ],
                'labels' => $data->pluck('category')->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
