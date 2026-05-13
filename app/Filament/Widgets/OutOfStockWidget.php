<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class OutOfStockWidget extends TableWidget
{
    protected static ?string $heading = 'Daftar Produk Habis';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Product::query()->where('stock', '<=', 0)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Minimum')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('kritis')
                    ->label('Hanya Stok 0')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '=', 0))
            ])
            ->paginated([10, 30, 100])
            ->defaultPaginationPageOption(10);
    }
}
