<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;

class LowStockAlertWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    // No auto-refresh needed
    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Product::query()
                    ->select(['id', 'name', 'stock', 'low_stock_threshold', 'category_id'])
                    ->whereColumn('stock', '<=', 'low_stock_threshold')
                    ->orderByRaw("CASE WHEN stock = 0 THEN 1 ELSE 2 END")
                    ->orderBy('stock', 'asc')
            )
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk'),
                TextColumn::make('stock')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Minimum'),
                TextColumn::make('category.name')
                    ->label('Kategori'),
            ])
            ->heading('Peringatan Stok Menipis');
    }
}
