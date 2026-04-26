<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembelian Stok')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Pemasok')
                            ->relationship('supplier', 'name')
                            ->required(),
                        Select::make('user_id')
                            ->label('Penanggung Jawab')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required(),
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian')
                            ->default(now())
                            ->required(),
                        TextInput::make('status')
                            ->label('Status')
                            ->required()
                            ->default('completed'),
                        TextInput::make('total_amount')
                            ->label('Total Akhir')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0.0)
                            ->readonly(),
                    ])->columns(2),

                Section::make('Item Pembelian')
                    ->schema([
                        Repeater::make('items')
                            ->label('Daftar Produk')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->live(),
                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $get, $set) => $set('subtotal', $state * $get('unit_price'))),
                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $get, $set) => $set('subtotal', $state * $get('quantity'))),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readonly()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $total = collect($state)->sum('subtotal');
                                $set('total_amount', $total);
                            }),
                    ]),
            ]);
    }
}
