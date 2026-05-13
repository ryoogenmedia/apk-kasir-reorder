<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ImageColumn;

class RestockWidget extends TableWidget
{
    protected static ?string $heading = 'Manajemen Restock Produk';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Product::query()
                    ->orderByRaw("CASE WHEN stock = 0 THEN 1 WHEN stock <= low_stock_threshold THEN 2 ELSE 3 END")
                    ->orderBy('name')
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->height(40)
                    ->width(40)
                    ->rounded(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Minimum'),
                ViewColumn::make('stock')
                    ->label('Stok Saat Ini (Restock)')
                    ->view('filament.tables.columns.stock-counter')
                    ->alignment(\Filament\Support\Enums\Alignment::Center),
            ])
            ->paginated([10, 30, 100])
            ->recordClasses(fn (Product $record) => match (true) {
                $record->stock == 0 => 'bg-danger-500/10 border-l-4 border-danger-500',
                $record->stock <= $record->low_stock_threshold => 'bg-warning-500/10 border-l-4 border-warning-500',
                default => null,
            });
    }

    public function incrementStock($recordId)
    {
        $product = Product::find($recordId);
        if ($product) {
            $product->increment('stock');
        }
    }

    public function decrementStock($recordId)
    {
        $product = Product::find($recordId);
        if ($product && $product->stock > 0) {
            $product->decrement('stock');
        }
    }

    public function updateStock($recordId, $value)
    {
        $product = Product::find($recordId);
        if ($product && is_numeric($value) && $value >= 0) {
            $product->update(['stock' => (int) $value]);
        }
    }
}
