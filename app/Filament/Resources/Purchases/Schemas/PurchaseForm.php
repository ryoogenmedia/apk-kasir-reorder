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
                Section::make('Purchase Information')
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required(),
                        Select::make('user_id')
                            ->label('Buyer')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required(),
                        DatePicker::make('purchase_date')
                            ->default(now())
                            ->required(),
                        TextInput::make('status')
                            ->required()
                            ->default('completed'),
                        TextInput::make('total_amount')
                            ->label('Grand Total')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0.0)
                            ->readonly(),
                    ])->columns(2),

                Section::make('Purchase Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->live(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $get, $set) => $set('subtotal', $state * $get('unit_price'))),
                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $get, $set) => $set('subtotal', $state * $get('quantity'))),
                                TextInput::make('subtotal')
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
