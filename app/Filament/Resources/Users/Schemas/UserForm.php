<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrated(fn ($state) => !empty($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'owner' => 'Owner',
                        'super_admin' => 'Superadmin',
                        'admin' => 'Admin',
                        'cashier' => 'Kasir',
                    ])
                    ->required()
                    ->dehydrated(false)
                    ->default(fn ($record) => $record?->roles()->first()?->name),
            ]);
    }
}
