<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class SalesChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Grafik Penjualan';
    protected static ?int $sort = 3;

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
        $cacheKey = 'sales_chart_filter_' . ($activeFilter ?? 'all');

        return Cache::remember($cacheKey, 60, function () use ($activeFilter) {
            if (!$activeFilter || $activeFilter === 'all') {
                $startDate = now()->subDays(30)->startOfDay();
                $endDate = now()->endOfDay();
                $daysCount = 30;
            } else {
                [$year, $month] = explode('-', $activeFilter);
                $startDate = \Illuminate\Support\Carbon::create($year, $month, 1)->startOfDay();
                $endDate = \Illuminate\Support\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                $daysCount = $startDate->daysInMonth;
            }

            $data = \App\Models\Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date');

            $labels = [];
            $totals = [];

            for ($i = 0; $i < $daysCount; $i++) {
                if (!$activeFilter || $activeFilter === 'all') {
                    $date = now()->subDays($daysCount - 1 - $i)->format('Y-m-d');
                } else {
                    $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                }
                $labels[] = \Carbon\Carbon::parse($date)->format('d M');
                $totals[] = (float) ($data[$date] ?? 0);
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Total Penjualan (Rp)',
                        'data' => $totals,
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
    public function placeholder(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.skeleton');
    }
}

