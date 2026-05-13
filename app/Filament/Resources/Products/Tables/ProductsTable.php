<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->rounded(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('low_stock_threshold')
                    ->label('Batas Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
