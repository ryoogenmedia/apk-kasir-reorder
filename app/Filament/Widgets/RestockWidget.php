<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class RestockWidget extends TableWidget
{
    protected static bool $isLazy = true;

    protected static ?string $heading = 'Manajemen Restock Produk';
    
    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Product::query()
                    ->select(['id', 'name', 'stock', 'low_stock_threshold', 'price', 'image'])
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Min.')
                    ->sortable(),
                ViewColumn::make('stock')
                    ->label('Stok')
                    ->view('filament.tables.columns.stock-counter')
                    ->alignment(\Filament\Support\Enums\Alignment::Center),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50])
            ->recordClasses(fn (Product $record) => match (true) {
                $record->stock == 0 => 'bg-danger-500/10 border-l-4 border-danger-500',
                $record->stock <= $record->low_stock_threshold => 'bg-warning-500/10 border-l-4 border-warning-500',
                default => null,
            });
    }

    public function incrementStock($recordId)
    {
        DB::table('products')->where('id', $recordId)->increment('stock');
        
        $name = Product::where('id', $recordId)->value('name');

        Notification::make()
            ->title('Stok Ditambah')
            ->body("Stok \"{$name}\" berhasil ditambah")
            ->success()
            ->duration(2000)
            ->send();
    }

    public function decrementStock($recordId)
    {
        $product = Product::select(['id', 'name', 'stock', 'low_stock_threshold'])->find($recordId);
        
        if ($product && $product->stock > 0) {
            $product->decrement('stock');
            $newStock = $product->stock - 1;

            $notification = Notification::make()
                ->title('Stok Dikurangi')
                ->body("Stok \"{$product->name}\" berkurang menjadi {$newStock}")
                ->duration(2000);

            if ($newStock <= $product->low_stock_threshold) {
                $notification->warning();
            } else {
                $notification->info();
            }

            $notification->send();
        }
    }

    public function updateStock($recordId, $value)
    {
        if (is_numeric($value) && $value >= 0) {
            Product::where('id', $recordId)->update(['stock' => (int) $value]);

            Notification::make()
                ->title('Stok Diperbarui')
                ->success()
                ->duration(2000)
                ->send();
        }
    }
}
