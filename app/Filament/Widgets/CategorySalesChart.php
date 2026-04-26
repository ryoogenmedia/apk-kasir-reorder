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
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                ],
            ],
            'labels' => $data->pluck('category')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
