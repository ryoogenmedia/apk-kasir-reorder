<?php

namespace App\Filament\Resources\Purchases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')
                    ->label('Pemasok')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Penanggung Jawab')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('purchase_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view_purchase_proof')
                    ->label('Bukti')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->modalHeading('Bukti Pembelian Supplier')
                    ->modalContent(fn ($record) => view('filament.components.proof-modal', ['image' => $record->proof_image]))
                    ->visible(fn ($record) => !empty($record->proof_image)),
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
