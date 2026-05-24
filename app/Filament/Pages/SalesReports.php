<?php

namespace App\Filament\Pages;

use App\Models\TransactionLedger;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
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
    protected static ?string $navigationLabel = 'Laporan Transaksi';
    protected static ?string $title = 'Laporan Transaksi';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales-reports';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'owner']);
    }

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
                    ->description('Pilih rentang tanggal untuk menyaring data transaksi')
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
                TransactionLedger::query()
                    ->when($this->data['startDate'] ?? null, fn($q) => $q->whereDate('date', '>=', $this->data['startDate']))
                    ->when($this->data['endDate'] ?? null, fn($q) => $q->whereDate('date', '<=', $this->data['endDate']))
                    ->orderBy('date', 'desc')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('actor_name')
                    ->label('Kasir / PJ')
                    ->searchable(),
                TextColumn::make('detail')
                    ->label('Detail / Keterangan')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'pemasukan' 
                        ? 'Penjualan (' . ($state === 'cash' ? 'Tunai' : 'QRIS') . ')'
                        : 'Pembelian (' . $state . ')'
                    )
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->color(fn ($record) => $record->type === 'pemasukan' ? 'success' : 'danger')
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
                Action::make('cetak_excel')
                    ->label('Ekspor Excel (CSV)')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(fn () => $this->exportCsv()),
            ]);
    }

    public function getStats(): array
    {
        $startDate = $this->data['startDate'] ?? null;
        $endDate = $this->data['endDate'] ?? null;

        $query = TransactionLedger::query()
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate));

        $pemasukan = (float) (clone $query)->where('type', 'pemasukan')->sum('amount');
        $pengeluaran = (float) (clone $query)->where('type', 'pengeluaran')->sum('amount');
        $balance = $pemasukan - $pengeluaran;

        return [
            'total_sales' => $pemasukan,
            'total_purchases' => $pengeluaran,
            'balance' => $balance,
        ];
    }

    public function printPdf()
    {
        $startDate = $this->data['startDate'];
        $endDate = $this->data['endDate'];

        $transactions = TransactionLedger::query()
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->orderBy('date', 'asc')
            ->get();

        $stats = $this->getStats();

        $pdf = Pdf::loadView('reports.transactions-pdf', [
            'transactions' => $transactions,
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
            'stats' => $stats,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Laporan_Transaksi_{$startDate}_to_{$endDate}.pdf"
        );
    }

    public function exportCsv()
    {
        $startDate = $this->data['startDate'];
        $endDate = $this->data['endDate'];

        $transactions = TransactionLedger::query()
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->orderBy('date', 'asc')
            ->get();

        $filename = "Laporan_Transaksi_{$startDate}_to_{$endDate}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Tanggal', 'Tipe', 'Kasir / PJ', 'Keterangan / Detail', 'Jumlah (IDR)']);

            $totalPemasukan = 0;
            $totalPengeluaran = 0;

            foreach ($transactions as $row) {
                $detailStr = $row->type === 'pemasukan' 
                    ? 'Penjualan (' . ($row->detail === 'cash' ? 'Tunai' : 'QRIS') . ')'
                    : 'Pembelian (' . $row->detail . ')';

                fputcsv($file, [
                    $row->date->format('d/m/Y'),
                    ucfirst($row->type),
                    $row->actor_name,
                    $detailStr,
                    (float) $row->amount
                ]);

                if ($row->type === 'pemasukan') {
                    $totalPemasukan += $row->amount;
                } else {
                    $totalPengeluaran += $row->amount;
                }
            }

            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Pemasukan', $totalPemasukan]);
            fputcsv($file, ['Total Pengeluaran', $totalPengeluaran]);
            fputcsv($file, ['Balance (Net)', $totalPemasukan - $totalPengeluaran]);

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
