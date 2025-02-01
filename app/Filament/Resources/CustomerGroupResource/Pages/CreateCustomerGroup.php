<?php

namespace App\Filament\Resources\CustomerGroupResource\Pages;

use App\Filament\Resources\CustomerGroupResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CreateCustomerGroup extends CreateRecord
{
    protected static string $resource = CustomerGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action(function () {
                    $this->save();
                    
                    Notification::make()
                        ->success()
                        ->title('Created successfully')
                        ->send();

                    return redirect()->to(CustomerGroupResource::getUrl('index'));
                })
                ->label('Save')
                ->color('success')
                ->icon('heroicon-o-check'),
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