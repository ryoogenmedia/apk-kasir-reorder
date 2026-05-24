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
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanProdukTerlaris extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Produk Terlaris';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['owner', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Product::query()
                    ->select([
                        'products.id',
                        'products.category_id',
                        'products.name',
                    ])
                    ->selectRaw('SUM(order_items.quantity) as total_qty')
                    ->selectRaw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->groupBy('products.id', 'products.category_id', 'products.name')
                    ->orderByDesc('total_qty')
            )
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_qty')
                    ->label('Total Terjual (Qty)')
                    ->sortable(),
                TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('orders.order_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('orders.order_date', '<=', $date),
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
                        $pdf = Pdf::loadView('reports.produk-terlaris-pdf', ['records' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'Laporan-Produk-Terlaris.pdf');
                    }),
                Action::make('export_csv')
                    ->label('Cetak CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        return response()->streamDownload(function () use ($records) {
                            $output = fopen('php://output', 'w');
                            fputcsv($output, ['Kategori', 'Nama Produk', 'Total Terjual (Qty)', 'Total Pendapatan']);
                            foreach ($records as $record) {
                                fputcsv($output, [
                                    $record->category->name ?? '-',
                                    $record->name ?? '-',
                                    $record->total_qty,
                                    $record->total_revenue
                                ]);
                            }
                            fclose($output);
                        }, 'Laporan-Produk-Terlaris.csv', ['Content-Type' => 'text/csv']);
                    }),
            ]);
    }
}
