<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SalesChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Grafik Penjualan (30 Hari Terakhir)';
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        return Cache::remember('sales_chart_30d', 120, function () {
            $data = \App\Models\Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
                ->where('order_date', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Total Penjualan (Rp)',
                        'data' => $data->pluck('total')->toArray(),
                        'fill' => 'start',
                        'borderColor' => '#6366f1',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                        'tension' => 0.4,
                        'pointBackgroundColor' => '#6366f1',
                        'pointBorderColor' => '#fff',
                        'pointBorderWidth' => 2,
                        'pointRadius' => 4,
                        'pointHoverRadius' => 6,
                        'borderWidth' => 3,
                    ],
                ],
                'labels' => $data->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))->toArray(),
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
