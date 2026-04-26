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
            ->label('Masuk');
    }

    protected function getEmailFormComponent(): \Filament\Schemas\Components\Component
    {
        return parent::getEmailFormComponent()
            ->label('Alamat Email')
            ->prefixIcon('heroicon-m-envelope');
    }

    protected function getPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return parent::getPasswordFormComponent()
            ->label('Kata Sandi')
            ->prefixIcon('heroicon-m-lock-closed');
    }
}
