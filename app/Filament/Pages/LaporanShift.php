<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DailyShiftReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class LaporanShift extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Rekap Shift Harian';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['owner', 'admin', 'kasir']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DailyShiftReport::query())
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_transaksi')
                    ->label('Total Transaksi')
                    ->sortable(),
                TextColumn::make('tunai_masuk')
                    ->label('Uang Tunai Masuk')
                    ->money('IDR')
                    ->sortable()
                    ->color('success'),
                TextColumn::make('qris_masuk')
                    ->label('Uang QRIS Masuk')
                    ->money('IDR')
                    ->sortable()
                    ->color('info'),
                TextColumn::make('pengeluaran')
                    ->label('Pengeluaran (Belanja)')
                    ->money('IDR')
                    ->sortable()
                    ->color('danger'),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('created_from')->label('Mulai Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                Action::make('export_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('reports.shift-pdf', ['records' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'Laporan-Shift.pdf');
                    }),
                Action::make('export_csv')
                    ->label('Cetak CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        return response()->streamDownload(function () use ($records) {
                            $output = fopen('php://output', 'w');
                            fputcsv($output, ['Tanggal', 'Total Transaksi', 'Uang Tunai Masuk', 'Uang QRIS Masuk', 'Pengeluaran']);
                            foreach ($records as $record) {
                                fputcsv($output, [
                                    Carbon::parse($record->date)->format('Y-m-d'),
                                    $record->total_transaksi,
                                    $record->tunai_masuk,
                                    $record->qris_masuk,
                                    $record->pengeluaran
                                ]);
                            }
                            fclose($output);
                        }, 'Laporan-Shift.csv', ['Content-Type' => 'text/csv']);
                    }),
            ]);
    }
}
