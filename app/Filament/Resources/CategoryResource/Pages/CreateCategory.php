<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

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

                    return redirect()->to(CategoryResource::getUrl('index'));
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
} 