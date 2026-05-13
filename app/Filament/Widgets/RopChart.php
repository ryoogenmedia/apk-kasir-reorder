<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use Illuminate\Support\Carbon;

class RopChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Perhitungan ROP (Reorder Point)';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get all products and sum their sold quantities in the last 30 days
        $products = Product::withSum(['orderItems as total_sold_30_days' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('order_date', '>=', Carbon::now()->subDays(30));
            });
        }], 'quantity')->get();

        $labels = [];
        $stocks = [];
        $currentThresholds = [];
        $suggestedRops = [];

        foreach ($products as $product) {
            $labels[] = $product->name;
            $stocks[] = $product->stock;
            $currentThresholds[] = $product->low_stock_threshold;
            
            // Total sold in last 30 days
            $totalSold = $product->total_sold_30_days ?? 0;
            $avgDailyDemand = $totalSold / 30;
            
            // ROP Formula: (Avg Daily Demand * Lead Time) + Safety Stock
            $leadTime = 3; // Asumsi lead time 3 hari
            $safetyStock = 5; // Asumsi safety stock 5 unit
            
            $suggestedRop = ceil(($avgDailyDemand * $leadTime) + $safetyStock);
            $suggestedRops[] = $suggestedRop;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Stok Saat Ini',
                    'data' => $stocks,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Batas Minimum Saat Ini',
                    'data' => $currentThresholds,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.5)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                ],
                [
                    'label' => 'ROP Saran (Kalkulasi)',
                    'data' => $suggestedRops,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
