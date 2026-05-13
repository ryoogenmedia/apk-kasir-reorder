<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CategorySalesChart extends ChartWidget
{
    protected ?string $heading = 'Penjualan Per Kategori';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
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
                        '#6366f1', // Indigo
                        '#06b6d4', // Cyan
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#10b981', // Emerald
                        '#8b5cf6', // Violet
                        '#ec4899', // Pink
                        '#14b8a6', // Teal
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $data->pluck('category')->toArray(),
        ];
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
