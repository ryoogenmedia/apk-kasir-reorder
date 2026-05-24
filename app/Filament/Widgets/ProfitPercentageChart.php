<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Support\Carbon;

class ProfitPercentageChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '120s';

    public ?string $filter = 'all';

    public function getHeading(): ?string
    {
        $activeFilter = $this->filter;
        if (!$activeFilter || $activeFilter === 'all') {
            return 'Grafik Persentase Keuntungan Harian (30 Hari Terakhir)';
        }

        [$year, $month] = explode('-', $activeFilter);
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return 'Grafik Persentase Keuntungan Harian - ' . ($monthNames[(int)$month] ?? '') . ' ' . $year;
    }

    protected function getFilters(): ?array
    {
        $months = Order::query()
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

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('cashier');
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        if (!$activeFilter || $activeFilter === 'all') {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            $daysCount = 30;
        } else {
            [$year, $month] = explode('-', $activeFilter);
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
            $daysCount = $startDate->daysInMonth;
        }

        $sales = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $purchases = Purchase::selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $labels = [];
        $profitPercentages = [];

        for ($i = 0; $i < $daysCount; $i++) {
            if (!$activeFilter || $activeFilter === 'all') {
                $date = now()->subDays($daysCount - 1 - $i)->format('Y-m-d');
            } else {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            }
            $labels[] = Carbon::parse($date)->format('d M');

            $in = (float) ($sales[$date] ?? 0);
            $out = (float) ($purchases[$date] ?? 0);

            if ($out > 0) {
                $profitPct = (($in - $out) / $out) * 100;
            } elseif ($in > 0) {
                $profitPct = 100.0;
            } else {
                $profitPct = 0;
            }

            $profitPercentages[] = round($profitPct, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Persentase Keuntungan (%)',
                    'data' => $profitPercentages,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => 'start',
                    'borderWidth' => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
