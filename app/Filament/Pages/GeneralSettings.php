<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use UnitEnum;
use BackedEnum;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Umum';

    protected static ?string $title = 'Pengaturan Umum';

    protected string $view = 'filament.pages.general-settings';

    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'owner']);
    }

    public function mount(): void
    {
        $this->form->fill([
            'site_logo' => Setting::where('key', 'site_logo')->first()?->value,
            'site_favicon' => Setting::where('key', 'site_favicon')->first()?->value,
            'site_name' => Setting::where('key', 'site_name')->first()?->value ?? 'POS Kasir',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Branding')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Logo Aplikasi')
                            ->image()
                            ->directory('branding')
                            ->disk('public'),
                        FileUpload::make('site_favicon')
                            ->label('Favicon (Icon Browser)')
                            ->image()
                            ->directory('branding')
                            ->disk('public'),
                        TextInput::make('site_name')
                            ->label('Nama Aplikasi')
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

        // Clear settings cache so logo/favicon changes are reflected immediately
        cache()->forget('panel_settings_array_v2');
        cache()->forget('site_logo_url_cached');

        Notification::make()
            ->success()
            ->title('Pengaturan berhasil disimpan')
            ->send();
    }
}
