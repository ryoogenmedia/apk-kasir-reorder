<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;

class SalesReport extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $title = 'Laporan Penjualan';

    protected string $view = 'filament.pages.sales-report';

    protected static string | UnitEnum | null $navigationGroup = 'Laporan';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
