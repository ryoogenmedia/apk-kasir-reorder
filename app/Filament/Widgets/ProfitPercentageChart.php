<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Purchase;
use Illuminate\Support\Carbon;

class ProfitPercentageChart extends ChartWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Grafik Persentase Keuntungan Harian (30 Hari Terakhir)';
    
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '120s';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('cashier');
    }

    protected function getData(): array
    {
        $sales = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->where('order_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $purchases = Purchase::selectRaw('DATE(purchase_date) as date, SUM(total_amount) as total')
            ->where('purchase_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $labels = [];
        $profitPercentages = [];

        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
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
