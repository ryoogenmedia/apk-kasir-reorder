<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanStok extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?string $title = 'Laporan Stok & Reorder';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.laporan-page';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['superadmin', 'owner', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->with('category'))
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok Saat Ini')
                    ->sortable()
                    ->color(fn ($record) => $record->stock <= $record->low_stock_threshold ? 'danger' : 'success'),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Minimum')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(function ($record) {
                        if ($record->stock == 0) return 'Habis';
                        if ($record->stock <= $record->low_stock_threshold) return 'Perlu Reorder';
                        if ($record->max_stock_threshold && $record->stock >= $record->max_stock_threshold) return 'Overstock';
                        return 'Aman';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Habis' => 'danger',
                            'Perlu Reorder' => 'warning',
                            'Overstock' => 'info',
                            'Aman' => 'success',
                            default => 'secondary',
                        };
                    }),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori')
            ])
            ->headerActions([
                Action::make('export_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('reports.stok-pdf', ['records' => $records]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'Laporan-Stok.pdf');
                    }),
                Action::make('export_csv')
                    ->label('Cetak CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        return response()->streamDownload(function () use ($records) {
                            $output = fopen('php://output', 'w');
                            fputcsv($output, ['Kategori', 'Nama Produk', 'Stok Saat Ini', 'Batas Minimum', 'Status']);
                            foreach ($records as $record) {
                                $status = 'Aman';
                                if ($record->stock == 0) $status = 'Habis';
                                elseif ($record->stock <= $record->low_stock_threshold) $status = 'Perlu Reorder';
                                elseif ($record->max_stock_threshold && $record->stock >= $record->max_stock_threshold) $status = 'Overstock';

                                fputcsv($output, [
                                    $record->category->name ?? '-',
                                    $record->name,
                                    $record->stock,
                                    $record->low_stock_threshold,
                                    $status
                                ]);
                            }
                            fclose($output);
                        }, 'Laporan-Stok.csv', ['Content-Type' => 'text/csv']);
                    }),
            ]);
    }
}
