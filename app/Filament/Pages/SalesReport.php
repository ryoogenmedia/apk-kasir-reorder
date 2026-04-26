<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SalesReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.pages.sales-report';

    protected static $navigationGroup = 'Laporan';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
