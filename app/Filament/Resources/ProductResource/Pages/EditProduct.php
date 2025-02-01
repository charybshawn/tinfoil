<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

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

                    return redirect()->to(ProductResource::getUrl('index'));
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

    public function hasRelationManagerTabsDropdown(): bool
    {
        return false;
    }

    public function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\ProductResource\RelationManagers\VariationsRelationManager::class,
        ];
    }

    protected function getFormActions(): array
    {
        // Remove the default save button from the bottom
        return [];
    }
}
