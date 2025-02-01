<?php

namespace App\Filament\Resources\CustomerGroupResource\Pages;

use App\Filament\Resources\CustomerGroupResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditCustomerGroup extends EditRecord
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
                        ->title('Saved successfully')
                        ->send();

                    return redirect()->to(CustomerGroupResource::getUrl('index'));
                })
                ->label('Save')
                ->color('success')
                ->icon('heroicon-o-check'),
            DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
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