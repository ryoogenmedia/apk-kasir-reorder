<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    public function getHeading(): string
    {
        return ''; // Sembunyikan tulisan "Sign in"
    }

    public function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk'); // Ubah teks tombol menjadi "Masuk"
    }
}
