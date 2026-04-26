<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return ''; // Tetap sembunyikan tulisan "Sign in" jika diinginkan, atau hapus jika ingin benar-benar default
    }

    public function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk'); // Tetap gunakan "Masuk" karena ini permintaan awal
    }
}
