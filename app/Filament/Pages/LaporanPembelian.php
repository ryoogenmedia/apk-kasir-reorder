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
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class LaporanPembelian extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Pembelian';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['owner', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Purchase::query()->with('supplier'))
            ->columns([
                TextColumn::make('purchase_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('total_amount')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('purchase_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('purchase_date', '<=', $date),
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
                        $pdf = Pdf::loadView('reports.pembelian-pdf', ['records' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'Laporan-Pembelian.pdf');
                    }),
                Action::make('export_csv')
                    ->label('Cetak CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        return response()->streamDownload(function () use ($records) {
                            $output = fopen('php://output', 'w');
                            fputcsv($output, ['Tanggal', 'Supplier', 'Status', 'Total Belanja']);
                            foreach ($records as $record) {
                                fputcsv($output, [
                                    Carbon::parse($record->purchase_date)->format('Y-m-d'),
                                    $record->supplier->name ?? '-',
                                    $record->status,
                                    $record->total_amount
                                ]);
                            }
                            fclose($output);
                        }, 'Laporan-Pembelian.csv', ['Content-Type' => 'text/csv']);
                    }),
            ]);
    }
}
