<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class SalesReports extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $title = 'Laporan Penjualan';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales-reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'startDate' => now()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->description('Pilih rentang tanggal untuk menyaring data penjualan')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->default(now()->startOfMonth())
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                        DatePicker::make('endDate')
                            ->label('Tanggal Selesai')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->when($this->data['startDate'] ?? null, fn($q) => $q->whereDate('order_date', '>=', $this->data['startDate']))
                    ->when($this->data['endDate'] ?? null, fn($q) => $q->whereDate('order_date', '<=', $this->data['endDate']))
                    ->orderBy('order_date', 'desc')
            )
            ->columns([
                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->label('Metode')
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
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR'))
                    ->alignRight(),
            ])
            ->actions([])
            ->bulkActions([])
            ->headerActions([
                Action::make('cetak_pdf')
                    ->label('Cetak Laporan (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(fn () => $this->printPdf()),
            ]);
    }

    public function getStats(): array
    {
        $query = Order::query()
            ->when($this->data['startDate'] ?? null, fn($q) => $q->whereDate('order_date', '>=', $this->data['startDate']))
            ->when($this->data['endDate'] ?? null, fn($q) => $q->whereDate('order_date', '<=', $this->data['endDate']));

        return [
            'total_sales' => $query->sum('total_amount'),
            'transaction_count' => $query->count(),
            'avg_transaction' => $query->avg('total_amount') ?? 0,
        ];
    }

    public function printPdf()
    {
        $startDate = $this->data['startDate'];
        $endDate = $this->data['endDate'];

        $orders = Order::query()
            ->when($startDate, fn($q) => $q->whereDate('order_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('order_date', '<=', $endDate))
            ->with('user')
            ->orderBy('order_date', 'asc')
            ->get();

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'orders' => $orders,
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Laporan_Penjualan_{$startDate}_to_{$endDate}.pdf"
        );
    }
}
