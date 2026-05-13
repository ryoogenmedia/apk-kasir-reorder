<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Carbon;

class RopChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik ROP & Status Stok';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected function getFilters(): ?array
    {
        $categories = Category::pluck('name', 'id')->toArray();
        return ['all' => 'Semua Kategori'] + $categories;
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $query = Product::withSum(['orderItems as total_sold_30_days' => function ($q) {
            $q->whereHas('order', function ($o) {
                $o->where('order_date', '>=', Carbon::now()->subDays(30));
            });
        }], 'quantity');

        if ($activeFilter && $activeFilter !== 'all') {
            $query->where('category_id', $activeFilter);
        }

        $products = $query->get();

        $labels = [];
        $stocks = [];
        $suggestedRops = [];

        foreach ($products as $product) {
            $labels[] = $product->name;
            $stocks[] = $product->stock;
            
            $totalSold = $product->total_sold_30_days ?? 0;
            $avgDailyDemand = $totalSold / 30;
            $leadTime = 3; 
            $safetyStock = 5; 
            
            $suggestedRops[] = ceil(($avgDailyDemand * $leadTime) + $safetyStock);
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
