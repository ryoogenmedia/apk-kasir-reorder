<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class RopCalculation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'ROP';
    protected static ?string $title = 'Reorder Point (ROP)';
    protected string $view = 'filament.pages.rop-calculation';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['owner', 'admin']);
    }

    public function mount(): void
    {
        $this->form->fill([
            'startDate' => now()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
            'month' => null,
            'year' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        $years = \App\Models\Order::query()
            ->selectRaw('DISTINCT YEAR(order_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->mapWithKeys(fn ($y) => [(string) $y => (string) $y])
            ->toArray();

        if (empty($years)) {
            $currentYear = date('Y');
            $years = [(string) $currentYear => (string) $currentYear];
        }

        return $form
            ->schema([
                Section::make('Filter Ringkasan Keuangan ROP')
                    ->description('Saring pendapatan penjualan dan bersih berdasarkan rentang tanggal atau spesifik bulan & tahun')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->live(),
                        DatePicker::make('endDate')
                            ->label('Tanggal Selesai')
                            ->live(),
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '1' => 'Januari',
                                '2' => 'Februari',
                                '3' => 'Maret',
                                '4' => 'April',
                                '5' => 'Mei',
                                '6' => 'Juni',
                                '7' => 'Juli',
                                '8' => 'Agustus',
                                '9' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->nullable()
                            ->live(),
                        Select::make('year')
                            ->label('Tahun')
                            ->options($years)
                            ->nullable()
                            ->live(),
                    ])
                    ->columns(4),
            ])
            ->statePath('data');
    }

    public function getStats(): array
    {
        $startDate = $this->data['startDate'] ?? null;
        $endDate = $this->data['endDate'] ?? null;
        $month = $this->data['month'] ?? null;
        $year = $this->data['year'] ?? null;

        $salesQuery = \App\Models\Order::query();
        $purchasesQuery = \App\Models\Purchase::query();

        if ($month && $year) {
            $salesQuery->whereMonth('order_date', $month)->whereYear('order_date', $year);
            $purchasesQuery->whereMonth('purchase_date', $month)->whereYear('purchase_date', $year);
        } elseif ($year) {
            $salesQuery->whereYear('order_date', $year);
            $purchasesQuery->whereYear('purchase_date', $year);
        } elseif ($month) {
            $salesQuery->whereMonth('order_date', $month);
            $purchasesQuery->whereMonth('purchase_date', $month);
        } else {
            if ($startDate) {
                $salesQuery->whereDate('order_date', '>=', $startDate);
                $purchasesQuery->whereDate('purchase_date', '>=', $startDate);
            }
            if ($endDate) {
                $salesQuery->whereDate('order_date', '<=', $endDate);
                $purchasesQuery->whereDate('purchase_date', '<=', $endDate);
            }
        }

        $totalRevenue = (float) $salesQuery->sum('total_amount');
        $totalPurchases = (float) $purchasesQuery->sum('total_amount');
        $netIncome = $totalRevenue - $totalPurchases;

        return [
            'total_revenue' => $totalRevenue,
            'total_purchases' => $totalPurchases,
            'net_income' => $netIncome,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getBodyWidgets(): array
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
