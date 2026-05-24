<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class LaporanLabaRugi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Laba Rugi';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.laporan-page'; // We'll create a generic view

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['superadmin', 'owner']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->select('orders.*')
                    ->selectRaw('COALESCE((SELECT SUM(order_items.quantity * products.purchase_price) FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = orders.id), 0) as total_cost')
            )
            ->columns([
                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir'),
                TextColumn::make('total_amount')
                    ->label('Total Penjualan')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Total Modal (HPP)')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('profit')
                    ->label('Keuntungan Bersih')
                    ->state(fn($record) => $record->total_amount - $record->total_cost)
                    ->money('IDR')
                    ->color('success'),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
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
                        $pdf = Pdf::loadView('reports.laba-rugi-pdf', ['records' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'Laporan-Laba-Rugi.pdf');
                    }),
                Action::make('export_csv')
                    ->label('Cetak CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $output = fopen('php://temp', 'r+');
                        fputcsv($output, ['Tanggal', 'Kasir', 'Total Penjualan', 'Total Modal', 'Keuntungan Bersih']);
                        foreach ($records as $record) {
                            fputcsv($output, [
                                Carbon::parse($record->order_date)->format('Y-m-d'),
                                $record->user->name ?? '-',
                                $record->total_amount,
                                $record->total_cost,
                                $record->total_amount - $record->total_cost
                            ]);
                        }
                        rewind($output);
                        $csv = stream_get_contents($output);
                        fclose($output);
                        return response()->streamDownload(fn () => print($csv), 'Laporan-Laba-Rugi.csv', ['Content-Type' => 'text/csv']);
                    }),
            ]);
    }
}
