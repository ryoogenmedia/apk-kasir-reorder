<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class GeneralSettings extends Page
{
    protected static $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected $view = 'filament.pages.general-settings';

    protected static $navigationGroup = 'Settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function mount(): void
    {
        $this->form->fill([
            'site_logo' => Setting::where('key', 'site_logo')->first()?->value,
            'site_name' => Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Branding')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Site Logo')
                            ->image()
                            ->directory('branding'),
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required(),
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Notification::make()
            ->success()
            ->title('Pengaturan berhasil disimpan')
            ->send();
    }
}
