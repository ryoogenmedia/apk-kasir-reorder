<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\RopChart;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use BackedEnum;

class RopCalculation extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Perhitungan ROP';
    protected static ?string $title = 'Grafik Perhitungan ROP';
    protected string $view = 'filament.pages.rop-calculation';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array

    {
        return [
            RopChart::class,
        ];
    }
}
