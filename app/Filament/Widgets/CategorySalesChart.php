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

    public ?string $filter = 'all';

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

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $cacheKey = 'category_sales_chart_filter_' . ($activeFilter ?? 'all');

        return Cache::remember($cacheKey, 60, function () use ($activeFilter) {
            $query = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as category, SUM(order_items.subtotal) as total');

            if (!$activeFilter || $activeFilter === 'all') {
                $query->where('orders.order_date', '>=', now()->subDays(30));
            } else {
                [$year, $month] = explode('-', $activeFilter);
                $startDate = \Illuminate\Support\Carbon::create($year, $month, 1)->startOfDay();
                $endDate = \Illuminate\Support\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                $query->whereBetween('orders.order_date', [$startDate, $endDate]);
            }

            $data = $query->groupBy('categories.name')->get();

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
