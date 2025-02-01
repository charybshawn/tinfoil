<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $this->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Saved successfully')
                        ->send();

                    return redirect()->to(CustomerResource::getUrl('index'));
                })
                ->label('Save')
                ->color('success')
                ->icon('heroicon-o-check'),
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
} 