<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class SalesReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $title = 'Laporan Penjualan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales-reports';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->when($this->startDate, fn($q) => $q->whereDate('order_date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('order_date', '<=', $this->endDate))
                    ->orderBy('order_date', 'desc')
            )
            ->columns([
                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('IDR'))
                    ->alignRight(),
            ])
            ->headerActions([
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(fn () => $this->printPdf()),
            ])
            ->filters([
                // Filter ditangani oleh mount() dan form di view jika manual, 
                // tapi di sini kita pakai headerActions untuk Cetak.
            ]);
    }

    public function printPdf()
    {
        $orders = Order::query()
            ->when($this->startDate, fn($q) => $q->whereDate('order_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('order_date', '<=', $this->endDate))
            ->with('user')
            ->orderBy('order_date', 'asc')
            ->get();

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'orders' => $orders,
            'startDate' => Carbon::parse($this->startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($this->endDate)->format('d/m/Y'),
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Laporan_Penjualan_{$this->startDate}_to_{$this->endDate}.pdf"
        );
    }
}
