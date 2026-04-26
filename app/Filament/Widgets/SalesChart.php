<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Penjualan (30 Hari Terakhir)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = \App\Models\Order::selectRaw('order_date as date, SUM(total_amount) as total')
            ->where('order_date', '>=', now()->subDays(30))
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan (Rp)',
                    'data' => $data->pluck('total')->toArray(),
                    'fill' => 'start',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.1)',
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
