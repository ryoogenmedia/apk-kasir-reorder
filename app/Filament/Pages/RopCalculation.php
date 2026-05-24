<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use BackedEnum;

class RopCalculation extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Stok';
    protected static ?string $title = 'Manajemen Stok Produk';
    protected string $view = 'filament.pages.rop-calculation';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'owner']);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\LowStockAlertWidget::class,
            \App\Filament\Widgets\RestockWidget::class,
            \App\Filament\Widgets\RopChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}
